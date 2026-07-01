<?php
// app/Http/Controllers/ChatbotController.php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    private ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Inicializar o recuperar conversación
     */
    public function initialize(Request $request)
    {
        $sessionId = $request->session()->getId();

        // Buscar conversación activa para esta sesión
        $conversation = ChatConversation::where('session_id', $sessionId)
            ->where('status', 'active')
            ->latest()
            ->first();

        $isNewConversation = false;

        if (!$conversation) {
            $isNewConversation = true;

            // Crear nueva conversación
            $conversation = ChatConversation::create([
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'active',
                'started_at' => now(),
                'visitor_name' => $this->generateVisitorName($sessionId)
            ]);

            // Mensaje de bienvenida
            $greeting = $this->getGreeting();

            ChatMessage::create([
                'chat_conversation_id' => $conversation->id,
                'sender'   => 'assistant',
                'message'  => $greeting,
                'metadata' => ['type' => 'welcome']
            ]);
        }

        // Obtener historial de mensajes
        $history = $this->getConversationHistory($conversation);

        return response()->json([
            'success' => true,
            'chat_conversation_id' => $conversation->id,
            'is_new' => $isNewConversation,
            'history' => $history,
            'welcome_message' => $isNewConversation ? $history[0] ?? null : null
        ]);
    }

    public function index()
    {
        return view('home.chat');
    }

    public function message(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'chat_conversation_id' => 'sometimes|integer'
        ]);

        $sessionId = $request->session()->getId();

        // Obtener o crear conversación
        $conversation = null;

        if ($request->has('chat_conversation_id')) {
            $conversation = ChatConversation::find($request->chat_conversation_id);
        }

        if (!$conversation) {
            $conversation = ChatConversation::where('session_id', $sessionId)
                ->where('status', 'active')
                ->latest()
                ->first();
        }

        // Si no hay conversación activa, crear una nueva
        if (!$conversation) {
            $conversation = ChatConversation::create([
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'active',
                'started_at' => now(),
                'visitor_name' => $this->generateVisitorName($sessionId)
            ]);
        }

        $message = trim($request->message);

        $history = $this->getConversationHistory($conversation);

        $loghistory = 'HISTORIAL DE CONVERSACION CON EL CLIENTE: ';

        foreach ($history as $item) {
            if($item['message'] != ''){
                $loghistory .= $item['sender'].': '.$item['message'].'\n';
            }
        }

        $relevantInfo = $this->chatbotService->getRelevantKnowledge($message);

        // Formatear para la IA
        $context = $this->chatbotService->formatForAi($relevantInfo);

        $prompt = "$loghistory \n\n". $context . "\n\n Mensaje del Cliente:  " . $message . "\n";

        // Guardar mensaje del usuario
        $userMessage = ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender' => 'user',
            'message' => $request->message,
            'metadata' => [
                'timestamp' => now(),
                'ip' => $request->ip()
            ]
        ]);

        $result = $this->chatbotService->processMessage($prompt, $conversation->id);

        return response()->json($result);

    }


    /**
     * Verifica el estado del servicio
     */
    public function health(): JsonResponse
    {
        $health = $this->chatbotService->health();

        return response()->json([
            'status'   => 'online',
            'services' => $health
        ]);
    }
    /**
     * Finalizar conversación
     */
    public function endConversation(Request $request)
    {
        $conversation = ChatConversation::find($request->chat_conversation_id);

        if ($conversation) {
            $conversation->update([
                'status' => 'resolved',
                'ended_at' => now()
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Obtener historial de conversación
     */
    private function getConversationHistory($conversation)
    {
        return $conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(function($message) {
                return [
                    'id'      => $message->id,
                    'sender'  => $message->sender,
                    'message' => $message->message,
                    'time'    => $message->created_at->format('H:i'),
                    'date'    => $message->created_at->format('Y-m-d')
                ];
            });
    }

    /**
     * Obtener contexto completo de la conversación
     */
    private function getConversationContext($conversation)
    {
        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get();

        $context = [
            'chat_conversation_id' => $conversation->id,
            'visitor_name'         => $conversation->visitor_name,
            'message_count'        => $messages->count(),
            'history'              => []
        ];

        foreach ($messages as $message) {
            $context['history'][] = [
                'role'    => $message->sender === 'user' ? 'usuario' : 'asistente',
                'content' => $message->message,
                'time'    => $message->created_at->format('H:i:s')
            ];
        }

        return $context;
    }

    /**
     * Obtener saludo según hora
     */
    private function getGreeting()
    {
        $hour = now()->hour;
        $default = " Soy Olivia, tu asistente virtual de CIRO. ¿En qué puedo ayudarte?";

        if ($hour >= 6 && $hour < 12) {
            return "Hola buenos días, $default";
        } elseif ($hour >= 12 && $hour < 20) {
            return "Hola buenas tardes, $default";
        } else {
            return "Hola buenas noches, $default";
        }
    }
    /**
     * Generar nombre de visitante
     */
    private function generateVisitorName($sessionId)
    {
        $names = ['Visitante', 'Cliente', 'Usuario', 'Amigo'];
        $randomName = $names[array_rand($names)];
        $shortId = substr($sessionId, -4);

        return "{$randomName}{$shortId}";
    }
}
