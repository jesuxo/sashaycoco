<?php

namespace App\Http\Controllers;

use App\Models\Cwbancos;
use App\Models\Cwtransferencia;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PagoMovilAuditoria;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;

class TesoroController extends Controller
{
    const REFERENCIA_BLOQUEADA_PREFIX = 'pago_movil_bloqueado_';
    const BLOQUEO_MINUTOS = 60;

    public function index(Request $request)
    {
        if($request->ajax()){
            DB::beginTransaction();
            try {
                $fksucursal = $request->fksucursal;
                $monto      = $request->monto;
                $referencia = $request->referencia;
                $idReceptor = $request->idReceptor;
                $email      = $request->email;
                $auxreferencia = substr($referencia,-6,6);
                $user       = auth()->user();
                $userId     = $user ? $user->id : null;
                $userEmail  = $user ? $user->email : ($email ?? 'anonimo@consulta.com');
                $userIp     = $request->ip();
                $userAgent  = $request->userAgent();

                // Verificar si la referencia ya fue aprobada
                $aprobada = PagoMovilAuditoria::where('referencia', $auxreferencia)
                    ->where('estado', 'OK')
                    ->first();

                if ($aprobada) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'status' => 'Bloqueado',
                        'mensaje' => 'Esta referencia ya fue utilizada y aprobada anteriormente. No puede ser reutilizada.',
                        'codigo' => 'REFERENCIA_YA_UTILIZADA'
                    ], 200);
                }

                // Verificar bloqueo temporal
                $cacheKey = self::REFERENCIA_BLOQUEADA_PREFIX . $auxreferencia;
                if (Cache::has($cacheKey)) {
                    $tiempoRestante = Cache::get($cacheKey);
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'status' => 'Bloqueado',
                        'mensaje' => "Esta referencia está bloqueada temporalmente. Intente nuevamente en {$tiempoRestante} minutos.",
                        'codigo' => 'REFERENCIA_BLOQUEADA_TEMPORALMENTE',
                        'tiempo_restante' => $tiempoRestante
                    ], 200);
                }

                // Procesar imagen si fue enviada
                $imagenPath     = null;
                $imagenOriginal = null;

                if ($request->hasFile('imagen')) {
                    $imagen         = $request->file('imagen');
                    $imagenOriginal = $imagen->getClientOriginalName();
                    $extension      = strtolower($imagen->getClientOriginalExtension());
                    $nombreImagen   = time() . '_' . uniqid() . '_' . $auxreferencia . '.' . $extension;

                    $uploadPath = public_path('uploads/pagos_movil');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    $rutaCompleta = $uploadPath . '/' . $nombreImagen;

                    // Comprimir imagen
                    $this->comprimirImagen($imagen->getPathname(), $rutaCompleta, $extension);

                    $imagenPath = 'uploads/pagos_movil/' . $nombreImagen;
                }

                // Registrar consulta
                $auditoria = new PagoMovilAuditoria();
                $auditoria->referencia      = $auxreferencia;
                $auditoria->referenciaFull  = $referencia;
                $auditoria->monto           = $monto;
                $auditoria->id_receptor     = $idReceptor;
                $auditoria->estado          = 'Pendiente';
                $auditoria->mensaje         = 'Consulta en proceso';
                $auditoria->ip_usuario      = $userIp;
                $auditoria->user_agent      = $userAgent;
                $auditoria->user_id         = $userId;
                $auditoria->email_usuario   = $userEmail;
                $auditoria->imagen          = $imagenPath;
                $auditoria->imagen_original = $imagenOriginal;
                $auditoria->consultado_en   = now();
                $auditoria->save();

                // Llamar al servicio del banco
                $jsonobj = json_encode([
                    "referencia" => $auxreferencia,
                    "monto"      => $monto,
                    "idReceptor" => $idReceptor,
                ]);

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL            => "https://tpmovil.bt.gob.ve/RestTesoro/com/services/P2P/conformacion",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING       => '',
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 15,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => 'POST',
                    CURLOPT_POSTFIELDS     => $jsonobj,
                    CURLOPT_HTTPHEADER     => array(
                        'Content-Type: application/json'
                    ),
                ));

                $response  = curl_exec($curl);
                $httpCode  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $curlError = curl_error($curl);
                curl_close($curl);

                // Procesar respuesta
                if ($response === false || $httpCode !== 200) {
                    $estado  = 'Error';
                    $mensaje = $curlError ? "Error de conexión: {$curlError}" : "Error al conectar con el banco (HTTP {$httpCode})";

                    $auditoria->estado  = $estado;
                    $auditoria->mensaje = $mensaje;
                    $auditoria->save();

                    DB::commit();

                    return response()->json([
                        'success' => false,
                        'status'  => 'Error',
                        'mensaje' => $mensaje,
                        'codigo'  => 'ERROR_CONEXION'
                    ], 200);
                }

                $responseData = json_decode($response);

                if (!isset($responseData->status)) {
                    $estado  = 'Error';
                    $mensaje = 'Respuesta inválida del banco';

                    $auditoria->estado  = $estado;
                    $auditoria->mensaje = $mensaje;
                    $auditoria->save();

                    DB::commit();

                    return response()->json([
                        'success' => false,
                        'status'  => 'Error',
                        'mensaje' => $mensaje,
                        'codigo'  => 'RESPUESTA_INVALIDA'
                    ], 200);
                }

                $status  = $responseData->status;
                $mensaje = $responseData->mensaje ?? '';

                $auditoria->estado  = $status;
                $auditoria->mensaje = $mensaje;
                $auditoria->save();

                // Manejar rechazos
                if ($status !== 'OK') {
                    $intentosFallidos = PagoMovilAuditoria::where('referencia', $auxreferencia)
                        ->where('estado', '!=', 'OK')
                        ->where('created_at', '>=', now()->subHours(24))
                        ->count();

                    if ($intentosFallidos >= 3) {
                        Cache::put($cacheKey, self::BLOQUEO_MINUTOS, now()->addMinutes(self::BLOQUEO_MINUTOS));

                        DB::commit();
                        return response()->json([
                            'success'  => false,
                            'status'   => 'Bloqueado',
                            'mensaje'  => "La referencia {$referencia} ha sido bloqueada por 60 minutos debido a múltiples intentos fallidos.",
                            'codigo'   => 'DEMASIADOS_INTENTOS_FALLIDOS',
                            'intentos' => $intentosFallidos
                        ], 200);
                    }

                    $intentosRestantes = 3 - $intentosFallidos;

                    DB::commit();
                    return response()->json([
                        'success' => false,
                        'status'  => $status,
                        'mensaje' => $mensaje,
                        'codigo'  => 'PAGO_RECHAZADO',
                        'intentos_restantes' => $intentosRestantes,
                        'advertencia'        => $intentosRestantes <= 1 ? "⚠️ ÚLTIMO INTENTO - Si vuelve a fallar, la referencia será bloqueada por 60 minutos" : null
                    ], 200);
                }

                DB::commit();

                $fecha             = Carbon::now()->format('d/m/Y');
                list($d, $m, $y)   = explode('/', $fecha);

                $new               = new Cwtransferencia();
                $new->fecha        = "$y-$m-$d";
                $new->numero       = $referencia;
                $new->observacion  = "VERIFICADA POR INTERNET EN LINEA CON EL BANCO DEL TESORO";
                $new->titular      = 'BANCO DEL TESORO';
                $new->monto        = $monto;
                $new->fkbanco      = 4;
                $new->fksucursal   = $fksucursal;
                $new->fk_usuario   = auth()->id();
                $new->status       = 1;
                $new->tipo         = 'venta';
                $new->categoria    = '';
                $new->referencia   = $request->referencia;
                $new->proveedor_id = 0;
                $new->ahorro_id    = 0;
                $new->imagen       = $imagenPath;
                $new->imagen_original = $imagenOriginal;
                $new->bs           = 1;
                $new->save();

                return response()->json([
                    'success'    => true,
                    'status'     => $status,
                    'mensaje'    => $mensaje,
                    'codigo'     => 'PAGO_APROBADO',
                    'referencia' => $auxreferencia,
                    'monto'      => $monto,
                    'imagen'     => $imagenPath ? asset($imagenPath) : null
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error en consulta pago móvil: ' . $e->getMessage(), [
                    'referencia' => $request->referencia ?? null,
                    'user_id'    => auth()->id(),
                    'ip'         => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'status'  => 'Error',
                    'mensaje' => 'Error interno del servidor. Por favor intente más tarde.',
                    'codigo'  => 'ERROR_INTERNO'
                ], 200);
            }
        }

        // ========== PARTE CORREGIDA ==========

        // Verificar si el usuario está autenticado
        $user = auth()->user();

        if (!$user) {
            return redirect()->to('/');
        }

        // Verificar si el usuario tiene sucursales asignadas
        if (!$user->tieneSucursalesAsignadas()) {
            return redirect()->to('/')->with('error', 'No tiene sucursales asignadas');
        }

        $arraysucursales = $user->getSucursalesIdsComercialActual();

        // Verificar si el array está vacío
        if (empty($arraysucursales)) {
            return redirect()->to('/')->with('error', 'No tiene sucursales disponibles para esta comercial');
        }

        // Usar whereIn en lugar de whereRaw (más seguro)
        $sucursales = Sasucursal::whereIn('id', $arraysucursales)
            ->orderBy('descrip', 'asc')
            ->get();

        // Si no hay sucursales después del filtro, redirigir
        if ($sucursales->isEmpty()) {
            return redirect()->to('/')->with('error', 'No se encontraron sucursales');
        }

        return view('tesoro', compact('sucursales'));
    }

    /**
     * Comprimir imagen antes de guardar
     */
    private function comprimirImagen($rutaOrigen, $rutaDestino, $extension)
    {
        try {
            // Usar ImageManagerStatic de Intervention Image 2.x
            $img = Image::make($rutaOrigen);

            // Obtener dimensiones
            $anchoOriginal = $img->width();
            $altoOriginal  = $img->height();

            // Calcular nuevas dimensiones (máximo 1920px)
            $maxLado = 1920;

            if ($anchoOriginal > $maxLado || $altoOriginal > $maxLado) {
                $img->resize($maxLado, $maxLado, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Guardar con calidad 75%
            $img->save($rutaDestino, 75);

        } catch (\Exception $e) {
            Log::warning('Error al comprimir imagen: ' . $e->getMessage());
            copy($rutaOrigen, $rutaDestino);
        }
    }

    /**
     * Obtener historial de pagos
     */
    public function historial(Request $request)
    {
        $query = PagoMovilAuditoria::query()
            ->orderBy('consultado_en', 'desc')
            ->limit(50);

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        if ($request->referencia) {
            $query->where('referencia', 'like', "%{$request->referencia}%");
        }

        $historial = $query->get()->map(function($item) {
            return [
                'id'            => $item->id,
                'referencia'    => $item->referencia,
                'monto'         => $item->monto,
                'estado'        => $item->estado,
                'mensaje'       => $item->mensaje,
                'ip_usuario'    => $item->ip_usuario,
                'referenciaFull'=> $item->referenciaFull,
                'email_usuario' => $item->email_usuario,
                'consultado_en' => $item->consultado_en,
                'imagen'        => $item->imagen_url,
                'imagen_original' => $item->imagen_original
            ];
        });

        return response()->json($historial);
    }
}
