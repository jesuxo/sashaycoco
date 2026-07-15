<?php
// app/Http/Controllers/TransferenciaController.php

namespace App\Http\Controllers;

use App\Models\Saexis;
use App\Models\Saprod;
use App\Models\Sasucursal;
use App\Models\TransferenciaItem;
use App\Models\TransferenciaProceso;
use App\Models\TransferenciaSesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TransferenciaController extends Controller
{
    // Vista principal de transferencias
    public function index()
    {
        $comercialid = session('comercialid');
        $sucursales = Sasucursal::where('fk_comercial', $comercialid)->get();
        $itemsSesion = TransferenciaSesion::getItemsSesion();

        // Obtener sugerencias pendientes de transferencia
        $sugerencias = session('sugerencias_pendientes', []);

        return view('transferencias.index', compact('sucursales', 'itemsSesion', 'sugerencias'));
    }

    // Buscar producto por código o descripción
    public function buscarProducto(Request $request)
    {
        $comercialid = session('comercialid');
        $busqueda = $request->busqueda;
        $sucursalId = $request->sucursal_id;

        $productos = Saprod::where('comercial', $comercialid)
            ->where(function($q) use ($busqueda) {
                $q->where('codprod', 'like', "%{$busqueda}%")
                    ->orWhere('descrip', 'like', "%{$busqueda}%");
            })
            ->limit(20)
            ->get();

        foreach ($productos as $producto) {
            // Obtener existencia en sucursal origen
            $existencia = Saexis::where('codprod', $producto->codprod)
                ->where('fk_sucursal', $sucursalId)
                ->sum('existen');
            $producto->existencia_origen = $existencia;
        }

        return response()->json(['productos' => $productos]);
    }

    // Agregar item a la sesión
    public function agregarItem(Request $request)
    {
        $origenId = $request->origen_id;
        $destinoId = $request->destino_id;
        $codprod = $request->codprod;
        $cantidad = $request->cantidad;

        // Verificar existencia suficiente
        $existencia = Saexis::where('codprod', $codprod)
            ->where('fk_sucursal', $origenId)
            ->sum('existen');

        if ($existencia < $cantidad) {
            return response()->json([
                'error' => "Stock insuficiente. Disponible: {$existencia} unidades"
            ], 400);
        }

        TransferenciaSesion::agregarItem($origenId, $destinoId, $codprod, $cantidad);

        return response()->json(['success' => true]);
    }

    // Eliminar item de la sesión
    public function eliminarItem($id)
    {
        TransferenciaSesion::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // Actualizar cantidad de item
    public function actualizarItem(Request $request, $id)
    {
        $item = TransferenciaSesion::findOrFail($id);
        $nuevaCantidad = $request->cantidad;

        // Verificar existencia suficiente
        $existencia = Saexis::where('codprod', $item->codprod)
            ->where('fk_sucursal', $item->fk_sucursal_origen)
            ->sum('existen');

        if ($existencia < $nuevaCantidad) {
            return response()->json([
                'error' => "Stock insuficiente. Disponible: {$existencia} unidades"
            ], 400);
        }

        $item->cantidad = $nuevaCantidad;
        $item->save();

        return response()->json(['success' => true]);
    }

    // Guardar proceso de transferencia
    public function guardarProceso(Request $request)
    {
        $items = TransferenciaSesion::getItemsSesion();

        if ($items->isEmpty()) {
            return redirect()->back()->with('error', 'No hay items para transferir');
        }

        DB::beginTransaction();
        try {
            // Determinar si es una transferencia múltiple o única
            $destinosUnicos = $items->pluck('fk_sucursal_destino')->unique();

            if ($destinosUnicos->count() == 1) {
                // Transferencia a una sola sucursal
                $proceso = new TransferenciaProceso();
                $proceso->codigo = TransferenciaProceso::generarCodigo();
                $proceso->fecha = now();
                $proceso->fk_sucursal_origen = $items->first()->fk_sucursal_origen;
                $proceso->fk_sucursal_destino = $destinosUnicos->first();
                $proceso->estado = 'completado';
                $proceso->observaciones = $request->observaciones;
                $proceso->creado_por = Auth::id();
                $proceso->creado_por_nombre = Auth::user()->name;
                $proceso->save();

                foreach ($items as $item) {
                    TransferenciaItem::create([
                        'fk_proceso' => $proceso->id,
                        'codprod' => $item->codprod,
                        'descripcion' => $item->producto->descrip ?? '',
                        'cantidad' => $item->cantidad,
                        'fk_sucursal_origen' => $item->fk_sucursal_origen,
                        'fk_sucursal_destino' => $item->fk_sucursal_destino,
                        'precio_unitario' => $item->producto->costod ?? 0,
                        'costo_unitario' => $item->producto->preciod ?? 0,
                        'observaciones' => $request->observaciones_item ?? ''
                    ]);
                }

                // Ejecutar transferencia
                $proceso->completar();

            } else {
                // Múltiples destinos - crear proceso maestro
                $proceso = new TransferenciaProceso();
                $proceso->codigo = TransferenciaProceso::generarCodigo();
                $proceso->fecha = now();
                $proceso->fk_sucursal_origen = $items->first()->fk_sucursal_origen;
                $proceso->estado = 'completado';
                $proceso->observaciones = $request->observaciones;
                $proceso->creado_por = Auth::id();
                $proceso->creado_por_nombre = Auth::user()->name;
                $proceso->save();

                foreach ($items as $item) {
                    TransferenciaItem::create([
                        'fk_proceso' => $proceso->id,
                        'codprod' => $item->codprod,
                        'descripcion' => $item->producto->descrip ?? '',
                        'cantidad' => $item->cantidad,
                        'fk_sucursal_origen' => $item->fk_sucursal_origen,
                        'fk_sucursal_destino' => $item->fk_sucursal_destino,
                        'precio_unitario' => $item->producto->costod ?? 0,
                        'costo_unitario' => $item->producto->preciod ?? 0,
                        'observaciones' => $request->observaciones_item ?? ''
                    ]);
                }

                // Procesar cada transferencia individual
                foreach ($items->groupBy('fk_sucursal_destino') as $destinoId => $itemsDestino) {
                    foreach ($itemsDestino as $item) {
                        // Restar stock de origen
                        $exisOrigen = Saexis::where('codprod', $item->codprod)
                            ->where('fk_sucursal', $item->fk_sucursal_origen)
                            ->first();
                        if ($exisOrigen) {
                            $exisOrigen->existen -= $item->cantidad;
                            $exisOrigen->save();
                        }

                        // Sumar stock a destino
                        $exisDestino = Saexis::where('codprod', $item->codprod)
                            ->where('fk_sucursal', $item->fk_sucursal_destino)
                            ->first();
                        if ($exisDestino) {
                            $exisDestino->existen += $item->cantidad;
                            $exisDestino->save();
                        } else {
                            $exisDestino = new Saexis();
                            $exisDestino->codprod = $item->codprod;
                            $exisDestino->fk_sucursal = $item->fk_sucursal_destino;
                            $exisDestino->existen = $item->cantidad;
                            $exisDestino->save();
                        }
                    }
                }
            }

            // Limpiar sesión
            TransferenciaSesion::limpiarSesion();

            DB::commit();
            return redirect()->route('transferencias.historial')->with('success', 'Transferencia completada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar transferencia: ' . $e->getMessage());
        }
    }

    // Historial de transferencias
    public function historial(Request $request)
    {
        $comercialid = session('comercialid');
        $sucursales = Sasucursal::where('fk_comercial', $comercialid)->get();

        $procesos = TransferenciaProceso::with(['items.producto', 'sucursalOrigen', 'sucursalDestino'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('transferencias.historial', compact('procesos', 'sucursales'));
    }

    // Ver detalle de transferencia
    public function detalle($id)
    {
        $proceso = TransferenciaProceso::with(['items.producto', 'sucursalOrigen', 'sucursalDestino'])
            ->findOrFail($id);

        return view('transferencias.detalle', compact('proceso'));
    }

    // Cancelar transferencia (solo si está en borrador)
    public function cancelar($id)
    {
        $proceso = TransferenciaProceso::findOrFail($id);

        if ($proceso->estado != 'borrador') {
            return redirect()->back()->with('error', 'Solo se pueden cancelar transferencias en estado borrador');
        }

        $proceso->estado = 'cancelado';
        $proceso->save();

        return redirect()->back()->with('success', 'Transferencia cancelada');
    }

    // Cargar sugerencia desde análisis de rotación
    public function cargarSugerencia(Request $request)
    {
        $sugerencia = $request->sugerencia;

        // Agregar a sesión
        TransferenciaSesion::agregarItem(
            $sugerencia['sucursal_origen_id'],
            $sugerencia['sucursal_destino_id'],
            $sugerencia['producto_cod'],
            $sugerencia['cantidad_sugerida']
        );

        return response()->json(['success' => true]);
    }

    // Limpiar sesión actual
    public function limpiarSesion()
    {
        TransferenciaSesion::limpiarSesion();
        return redirect()->route('transferencias.index')->with('success', 'Lista de transferencia limpiada');
    }
}
