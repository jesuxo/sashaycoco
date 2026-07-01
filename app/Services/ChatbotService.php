<?php

namespace App\Services;


use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Ia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChatbotService
{
    private string $geminiApiKey;
    private string $modelName;
    private string $respuestasPath;

    private array $validModels = [
        'models/gemini-2.5-flash'
    ];

    public function __construct()
    {
        $this->geminiApiKey   = env('GEMINI_API_KEY');
        $this->modelName      = $this->getValidModelName(env('MODEL_NAME', 'models/gemini-2.5-flash'));

    }

    private function getValidModelName(string $requestedModel): string
    {
        // Si el modelo solicitado está en nuestra lista, úsalo
        if (in_array($requestedModel, $this->validModels)) {
            return $requestedModel;
        }

        return 'models/gemini-2.5-flash';
    }

    /**
     * Procesa el mensaje del usuario y retorna una respuesta
     */
    public function processMessage(string $message, $conversation_id): array
    {
        $message = $this->normalizeText($message);

        try {

            // 1. Intentar con respuestas del archivo TXT primero
            $txtReply = $this->callGeminiTxt($message);

            // Si la respuesta es 'SQL', significa que necesita consultar BD
            if (strtoupper(trim($txtReply)) === 'SQL') {
                // Obtener datos de productos
                $datosExtra = $this->getProductData($message);

                // Generar respuesta con Gemini usando los datos
                $reply = $this->callGemini($message, $datosExtra);

                // Verificar si la respuesta está truncada
                if ($this->isResponseTruncated($reply)) {
                    // Reintentar con más tokens
                    $reply = $this->callGeminiWithMoreTokens($message, $datosExtra);
                }

                $userMessage = ChatMessage::create([
                    'chat_conversation_id' => $conversation_id,
                    'sender' => 'assistant',
                    'message' => $reply,
                    'metadata' => [
                        'timestamp' => now(),
                        'ip' => ''
                    ]
                ]);

                return [
                    'success' => true,
                    'reply' => $reply
                ];
            }

            if ($this->isResponseTruncated($txtReply)) {
                $txtReply = $this->callGeminiWithMoreTokens($message, '');
            }

            $userMessage = ChatMessage::create([
                'chat_conversation_id' => $conversation_id,
                'sender' => 'assistant',
                'message' => $txtReply,
                'metadata' => [
                    'timestamp' => now(),
                    'ip' => ''
                ]
            ]);

            return [
                'success' => true,
                'reply' => $txtReply
            ];

        } catch (\Exception $exception) {

            $userMessage = ChatMessage::create([
                'chat_conversation_id' => $conversation_id,
                'sender' => 'assistant',
                'message' => 'Lo siento, tuve un problema técnico. ¿Podrías intentarlo de nuevo?',
                'metadata' => [
                    'timestamp' => now(),
                    'ip' => ''
                ]
            ]);

            return [
                'success' => false,
                'reply' => 'Lo siento, tuve un problema técnico. ¿Podrías intentarlo de nuevo?',
                'error' => $exception->getMessage()
            ];
        }
    }

    private function callGeminiWithMoreTokens(string $message, string $datosExtra): string
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/{$this->modelName}:generateContent?key={$this->geminiApiKey}";

            $prompt = "Eres un vendedor amable y cercano de CIRO. ";
            $prompt .= "Responde en español de forma COMPLETA y NATURAL. ";
            $prompt .= "es incomodo que siempre digas hola y que finalices muy amablemente, puedes decir Claro! una que otra vez pero no seguidamente";
            $prompt .= "Asegúrate de terminar las oraciones correctamente.\n\n";

            if (!empty($datosExtra)) {
                $prompt .= "Datos de inventario: {$datosExtra}\n\n";
            }

            $prompt .= "Cliente: {$message}\n";
            $prompt .= "Vendedor:";

            $response = Http::timeout(30)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024, // Aún más tokens
                    'topP' => 0.95,
                    'topK' => 40
                ]
            ]);

            if (!$response->successful()) {
                return 'Lo siento, hay problemas de conexión.';
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            return trim($text);

        } catch (\Exception $e) {
            Log::error('Error en callGeminiWithMoreTokens: ' . $e->getMessage());
            return 'Lo siento, hay problemas de conexión.';
        }
    }

    public function formatForAi(array $knowledgeItems): string
    {
        $context = "Información de la base de conocimiento sobre la empresa para que puedas sacar la informacion para darle al cliente:\n\n";

        foreach($knowledgeItems as $item) {
            $context .= "TÍTULO: {$item['title']}\n";
            if(!empty($item['category'])) {
                $context .= "CATEGORÍA: {$item['category']}\n";
            }
            $context .= "CONTENIDO: {$item['text']}\n";
            $context .= str_repeat('-', 50) . "\n\n";
        }

        return $context;
    }


    public function getRelevantKnowledge(string $userQuery, int $limit = 5): array
    {
        // Buscar información relevante en la base de conocimiento
        $knowledge = Ia::active()
            ->ordered()
            ->get();

        return $knowledge->toArray();
    }

    private function isResponseTruncated(string $response): bool
    {
        // Eliminar espacios al final
        $response = rtrim($response);

        // Si termina con puntos suspensivos o sin puntuación final
        if (str_ends_with($response, '...') ||
            !str_ends_with($response, '.') &&
            !str_ends_with($response, '?') &&
            !str_ends_with($response, '!')) {
            return true;
        }

        // Si la última palabra está incompleta (termina con espacio o carácter raro)
        $lastChar = substr($response, -1);
        if (preg_match('/[a-zA-Z]$/', $lastChar) && strlen($response) > 0) {
            // Podría estar truncada, pero no es seguro
            // Mejor verificar la longitud
            if (strlen($response) > 450) { // Cerca del límite
                return true;
            }
        }

        return false;
    }

    /**
     * Normaliza el texto para búsqueda
     */
    private function normalizeText(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9áéíóúüñ\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }


    /**
     * Clasifica si el mensaje necesita consultar la BD
     */
    private function classifyMessage(string $message): bool
    {
        $prompt = "Eres un asistente de ventas de CIRO. Analiza el mensaje del usuario y responde SOLO con UNA PALABRA: 'SQL' si el usuario pregunta por disponibilidad, existencia, precio o características de productos específicos (televisores, neveras, lavadoras, electrodomésticos). Responde 'TEXTO' si es un saludo, consulta general, horarios, promociones, o cualquier otra cosa.\n\nMensaje: {$message}";

        $response = $this->callGeminiApi($prompt);

        return str_contains(strtoupper($response), 'SQL');
    }

    /**
     * Obtiene datos de productos de la base de datos
     */
    private function getProductData(string $message): string
    {
        try {
            // Asumiendo que tienes una tabla 'productos'
            // Ajusta esto según tu estructura real de BD
            $productos = DB::table('productos')
                ->where('nombre', 'LIKE', "%{$message}%")
                ->orWhere('descripcion', 'LIKE', "%{$message}%")
                ->orWhere('categoria', 'LIKE', "%{$message}%")
                ->orderBy('precio')
                ->limit(5)
                ->get(['nombre', 'precio', 'existencia', 'descripcion']);

            if ($productos->isEmpty()) {
                return "No se encontraron productos relacionados con '{$message}' en el inventario.";
            }

            $resultados = [];
            foreach ($productos as $producto) {
                $resultados[] = [
                    'nombre' => $producto->nombre,
                    'precio' => number_format($producto->precio, 2) . ' Bs',
                    'existencia' => $producto->existencia > 0 ? "{$producto->existencia} unidades" : 'Agotado',
                    'descripcion' => $producto->descripcion
                ];
            }

            return "Productos encontrados: " . json_encode($resultados, JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            Log::error('Error consultando productos: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Llama a la API de Gemini
     */
    private function callGemini(string $message, string $datosExtra = ''): string
    {
        $prompt = "Eres un vendedor amable y cercano de CIRO. Respondes en español natural, breve (máximo 3 frases) y evitas sonar robótico. Usas un tono cálido y profesional, es incomodo que siempre digas hola y que finalices muy amablemente. Si falta información importante, haces una pregunta corta para ayudar al cliente.\n\n";

        if (!empty($datosExtra)) {
            $prompt .= "Datos de inventario: {$datosExtra}\n\n";
        }

        $prompt .= "Cliente: {$message}\n";

        return $this->callGeminiApi($prompt);
    }

    private function callGeminiTxt(string $message, string $datosExtra = ''): string
    {
        $prompt = "Eres un vendedor amable y cercano de CIRO. ";
        $prompt .= "IMPORTANTE: Debes responder SIEMPRE en español, de forma natural y completa. ";
        $prompt .= "No cortes las respuestas a medio camino.\n\n";
        $prompt .= "es incomodo que siempre digas hola y que finalices muy amablemente.\n\n";
        $prompt .= "REGLAS:\n";
        //$prompt .= "1. Si la pregunta es sobre productos específicos (precios, disponibilidad, características) que requieran consultar una base de datos, responde SOLO con la palabra 'SQL'.\n";
        $prompt .= "1. Si la pregunta es sobre productos específicos (precios, disponibilidad, características) tratalo amablemente y pidele datos de contacto y nombre para que un asesor se comunique con el o ella.\n";
        $prompt .= "2. Si la información está en el contexto de la empresa proporcionado, responde de manera completa y útil.\n";
        $prompt .= "3. Máximo 3 oraciones, pero sin truncar ideas.\n\n";

        $prompt .= "{$message}\n";

        return $this->callGeminiApi($prompt);
    }

    /**
     * Llama a la API de Gemini
     */
    private function callGeminiApi(string $prompt): string
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/{$this->modelName}:generateContent?key={$this->geminiApiKey}";

            $response = Http::timeout(30)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Gemini API error: ' . $response->body());
                return 'Gemini API error: ' . $response->body();
            }

            $data = $response->json();

            // Extraer el texto de la respuesta
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ??
                $data['candidates'][0]['text'] ??
                null;

            if (!$text) {
                Log::warning('Respuesta inesperada de Gemini', ['data' => $data]);
                return 'No pude generar una respuesta adecuada.';
            }

            return trim($text);

        } catch (\Exception $e) {
            Log::error('Error calling Gemini API: ' . $e->getMessage());
            return 'Lo siento, hay problemas de conexión. Por favor intenta más tarde.';
        }
    }

    /**
     * Verifica la salud del servicio
     */
    public function health(): array
    {
        $status = [
            'gemini_api' => 'unknown',
            'database' => 'unknown',
            'txt_file' => 'unknown'
        ];

        // Verificar Gemini API
        try {
            $testResponse = $this->callGeminiApi('Responde solo "ok"');
            $status['gemini_api'] = !empty($testResponse) ? 'ok' : 'error';
        } catch (\Exception $e) {
            $status['gemini_api'] = 'error';
        }

        // Verificar base de datos
        try {
            DB::select('SELECT 1');
            $status['database'] = 'ok';
        } catch (\Exception $e) {
            $status['database'] = 'error';
        }

        // Verificar archivo TXT
        $status['txt_file'] = file_exists($this->respuestasPath) ? 'ok' : 'missing';

        return $status;
    }
}
