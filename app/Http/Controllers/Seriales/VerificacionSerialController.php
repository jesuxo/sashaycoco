<?php
// app/Http/Controllers/Seriales/VerificacionSerialController.php

namespace App\Http\Controllers\Seriales;

use App\Http\Controllers\Controller;
use App\Models\Saseprcom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerificacionSerialController extends Controller
{
    public function getComentario($id)
    {
        try {
            $serial = Saseprcom::with('checker')->find($id);

            if (!$serial) {
                return response()->json([
                    'success' => false,
                    'message' => 'Serial no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'comentario' => $serial->check_comment,
                    'estado' => $serial->checked,
                    'usuario' => $serial->checker ? $serial->checker->name : null,
                    'fecha' => $serial->checked_at ? $serial->checked_at->format('d/m/Y H:i') : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener comentario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener comentario'
            ], 500);
        }
    }

    public function verificar(Request $request, $id)
    {
        try {
            $request->validate([
                'estado' => 'required|in:0,1,2,3',
                'comentario' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();

            $serial = Saseprcom::with('checker')->find($id);

            if (!$serial) {
                return response()->json([
                    'success' => false,
                    'message' => 'Serial no encontrado'
                ], 404);
            }

            // Determinar el status basado en el estado
            $statusMap = [
                0 => 'PENDIENTE',
                1 => 'EN_STOCK',
                2 => 'DESCARGADO',
                3 => 'VENDIDO'
            ];

            $estadoAnterior = $serial->checked;

            $serial->checked = $request->estado;
            $serial->check_status = $statusMap[$request->estado];
            $serial->check_comment = $request->comentario;
            $serial->checked_by = Auth::id();
            $serial->checked_at = now();

            $serial->save();

            DB::commit();

            // Obtener el usuario que verificó
            $usuario = Auth::user()->name ?? Auth::user()->email ?? 'Sistema';

            return response()->json([
                'success' => true,
                'message' => 'Serial verificado correctamente',
                'data' => [
                    'id' => $serial->id,
                    'nroserial' => $serial->nroserial,
                    'estado' => $request->estado,
                    'estado_texto' => $serial->status_text,
                    'comentario' => $request->comentario,
                    'usuario' => $usuario,
                    'fecha' => now()->format('d/m/Y H:i'),
                    'color' => $serial->status_color,
                    'icono' => $serial->status_icon
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al verificar serial: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al verificar serial: ' . $e->getMessage()
            ], 500);
        }
    }

    private function registrarOperacionInventario($serial, $estado, $comentario)
    {
        // Aquí puedes registrar en saopei o sasepropi si es necesario
        // Por ahora solo lo dejamos indicado
        $tipoOperacion = $estado == 2 ? 'DESCARGO' : 'VENTA';

        Log::info("Registro de operación de inventario: {$tipoOperacion}", [
            'serial' => $serial->nroserial,
            'producto' => $serial->coditem,
            'comentario' => $comentario
        ]);
    }

    public function getSerial($id)
    {
        try {
            $serial = Saseprcom::with('checker')->find($id);

            if (!$serial) {
                return response()->json([
                    'success' => false,
                    'message' => 'Serial no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $serial->id,
                    'nroserial' => $serial->nroserial,
                    'checked' => $serial->checked,
                    'check_comment' => $serial->check_comment,
                    'checked_by' => $serial->checked_by,
                    'checked_at' => $serial->checked_at ? $serial->checked_at->format('d/m/Y H:i') : null,
                    'usuario' => $serial->checker->name ?? 'Sistema'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function estadisticasVerificacion($compraId)
    {
        try {
            $compra = \App\Models\Sacomp::find($compraId);

            if (!$compra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Compra no encontrada'
                ], 404);
            }

            $seriales = Saseprcom::where('numerod', $compra->numerod)
                ->where('tipocom', $compra->tipocom)
                ->get();

            $estadisticas = [
                'total' => $seriales->count(),
                'pendientes' => $seriales->where('checked', 0)->count(),
                'verificados' => $seriales->where('checked', 1)->count(),
                'descargados' => $seriales->where('checked', 2)->count(),
                'vendidos' => $seriales->where('checked', 3)->count(),
                'con_comentarios' => $seriales->whereNotNull('check_comment')->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
