<?php
// app/Http/Controllers/PagoProveedorController.php

namespace App\Http\Controllers;

use App\Exports\ResumenPagosExport;
use App\Models\PagoProveedor;
use App\Models\PagoProveedorDetalle;
use App\Models\PagoComprobante;
use App\Models\DespachoProveedor;
use App\Models\DespachoProveedorDetalle;
use App\Models\Saprov;
use App\Models\Saprod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Maatwebsite\Excel\Excel;

class PagoProveedorController extends Controller
{
    public function index(Request $request)
    {
        $estado      = (isset($request->estado))? $request->estado : 'pendiente ';
        $search      = (isset($request->search))? $request->search : '';
        $fecha_desde = (isset($request->fecha_desde))? $request->fecha_desde : '';
        $fecha_hasta = (isset($request->fecha_hasta))? $request->fecha_hasta : '';
        $codprov = (isset($request->codprov))? $request->codprov : '';

        $query = PagoProveedor::with(['proveedor', 'detalles', 'comprobantes'])
            ->withCount('comprobantes');

        if ($codprov) {
            $query->where('codprov', $codprov);
        }

        if ($estado != ''  and  $estado != 'todos' and $search =='') {
            $query->where('estado', $estado);
        }

        if ($fecha_desde) {
            $query->whereDate('fecha_pago', '>=', $fecha_desde);
        }
        if ($fecha_hasta) {
            $query->whereDate('fecha_pago', '<=', $fecha_hasta);
        }

        // Búsqueda general
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                    ->orWhere('numero_aprobacion', 'like', "%{$search}%")
                    ->orWhere('notas', 'like', "%{$search}%")
                    ->orWhereHas('proveedor', function($sq) use ($search) {
                        $sq->where('descrip', 'like', "%{$search}%")
                            ->orWhere('codprov', 'like', "%{$search}%");
                    });
            });
        }

        $pagos = $query->orderBy('created_at', 'desc')->paginate(10);

        // Estadísticas
        $estadisticas = [
            'total'       => PagoProveedor::count(),
            'pendientes'  => PagoProveedor::where('estado', 'pendiente')->count(),
            'parciales'   => PagoProveedor::where('estado', 'parcial')->count(),
            'completados' => PagoProveedor::where('estado', 'completado')->count(),
            'aprobados'   => PagoProveedor::where('estado', 'aprobado')->count(),
        ];

        // Total de motos pendientes de recibir SOLO de pedidos que están pagados (comprobantes cubren el monto total)
        $totalPendiente = DB::table('pagos_proveedores as pp')
            ->join('pagos_proveedores_detalles as ppd', 'pp.id', '=', 'ppd.pago_id')
            ->whereRaw("ppd.cantidad_recibida < ppd.cantidad and numero_aprobacion is not null and numero_aprobacion <>''")
            ->whereIn('pp.id', function($query) {
                $query->select('pago_id')
                    ->from('pagos_comprobantes')
                    ->groupBy('pago_id')
                    ->havingRaw('SUM(monto) >= (SELECT monto_total FROM pagos_proveedores WHERE id = pago_id)');
            })
            ->sum(DB::raw('ppd.cantidad - ppd.cantidad_recibida'));

        // NUEVO: Total de motos facturadas (suma de todas las cantidad_facturada)
        $totalFacturadas = DB::table('pagos_proveedores_detalles')
            ->where('cantidad_facturada', '>', 0)
            ->sum('cantidad_facturada');

        // Proveedores para filtro
        $proveedores = Saprov::where('pagomotos', 1)->orderBy('descrip')->get();

        if ($request->ajax()) {
            return view('pagos-proveedores.partials.lista', compact('pagos'))->render();
        }

        return view('pagos-proveedores.index', compact(
            'pagos',
            'estado',
            'search',
            'fecha_desde',
            'fecha_hasta',
            'codprov',
            'estadisticas',
            'totalFacturadas',
            'totalPendiente',
            'proveedores'));
    }

    public function create()
    {
        $proveedores = Saprov::where('pagomotos', 1)->orderBy('descrip')->get();
        $view = view('pagos-proveedores.partials.form-crear', compact('proveedores'))->render();
        return response()->json(['html' => $view]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_pago' => 'required|date',
            'codprov' => 'required|exists:saprov,codprov',
            'monto_total' => 'required|numeric|min:0',
            'productos' => 'required|array',
            'productos.*.producto_id' => 'required|exists:saprod,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'notas' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Crear el pago
            $pago = PagoProveedor::create([
                'fecha_pago'  => $request->fecha_pago,
                'codprov'     => $request->codprov,
                'monto_total' => $request->monto_total,
                'notas'       => $request->notas,
                'estado'      => 'pendiente'
            ]);

            // Crear los detalles del pago
            foreach ($request->productos as $producto) {
                $prod = Saprod::find($producto['producto_id']);

                PagoProveedorDetalle::create([
                    'pago_id'            => $pago->id,
                    'producto_id'        => $producto['producto_id'],
                    'producto_codprod'   => $prod->codprod,
                    'producto_descrip'   => $producto['producto_descrip'] ?? $prod->descrip,
                    'cantidad'           => $producto['cantidad'],
                    'cantidad_recibida'  => 0,
                    'cantidad_facturada' => 0, // Inicialmente igual a cantidad
                    'precio_unitario'    => $producto['precio_unitario'],
                    'subtotal'           => $producto['cantidad'] * $producto['precio_unitario']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago creado correctamente',
                'pago_id' => $pago->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Error al crear el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $pago = PagoProveedor::with(['proveedor', 'detalles.producto', 'comprobantes', 'despachos.detalles.pagoDetalle'])->findOrFail($id);
        $view = view('pagos-proveedores.partials.detalles', compact('pago'))->render();
        return response()->json(['html' => $view]);
    }

    public function edit($id)
    {
        $pago = PagoProveedor::with('proveedor')->findOrFail($id);
        $proveedores = Saprov::where('pagomotos', 1)->orderBy('descrip')->get();

        $view = view('pagos-proveedores.partials.form-editar', compact('pago', 'proveedores'))->render();
        return response()->json(['html' => $view]);
    }

    public function detalleFacturadas()
    {
        $detalles = PagoProveedorDetalle::with('pago.proveedor')
            ->where('cantidad_facturada', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalGeneral = $detalles->sum('cantidad_facturada');

        $view = view('pagos-proveedores.partials.detalle-facturadas', compact('detalles', 'totalGeneral'))->render();
        return response()->json(['html' => $view]);
    }

    public function updateProductos(Request $request, $id)
    {
        $pago = PagoProveedor::findOrFail($id);

        DB::beginTransaction();

        try {
            // Actualizar productos existentes
            foreach ($request->productos_actualizar as $producto) {
                $detalle = PagoProveedorDetalle::find($producto['id']);
                if ($detalle && $detalle->pago_id == $pago->id) {
                    $detalle->cantidad = $producto['cantidad'];
                    $detalle->cantidad_facturada = $producto['cantidad_facturada'] ?? 0; // Nuevo campo
                    $detalle->precio_unitario = $producto['precio_unitario'];
                    $detalle->subtotal = $producto['cantidad'] * $producto['precio_unitario'];
                    $detalle->save();
                }
            }

            // Crear nuevos productos
            foreach ($request->productos_nuevos as $producto) {
                $prod = Saprod::where('codprod',$producto['producto_codprod'])->where('comercial',1)->first();

                PagoProveedorDetalle::create([
                    'pago_id'           => $pago->id,
                    'producto_id'       => $producto['producto_id'],
                    'producto_codprod'  => $prod ? $prod->codprod : $producto['producto_codprod'],
                    'producto_descrip'  => $producto['producto_descrip'],
                    'cantidad'          => $producto['cantidad'],
                    'cantidad_recibida' => 0,
                    'cantidad_facturada' => $producto['cantidad_facturada'] ?? 0, // Nuevo campo
                    'precio_unitario'   => $producto['precio_unitario'],
                    'subtotal'          => $producto['cantidad'] * $producto['precio_unitario']
                ]);
            }

            // Actualizar monto total del pago
            $pago->monto_total = $pago->detalles()->sum('subtotal');
            $pago->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Productos actualizados correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPrecio($id)
    {

        return response()->json(['precio' => 0]);
/*
        $producto = Saprod::find($id);

        if ($producto) {
            // Usar el precio que corresponda según tu estructura
            $precio = $producto->preciod;
            return response()->json(['precio' => $precio]);
        }

        return response()->json(['precio' => 0]);*/
    }

    public function getProductos($id)
    {
        $pago = PagoProveedor::with('detalles')->findOrFail($id);

        return response()->json([
            'success' => true,
            'productos' => $pago->detalles->map(function($detalle) {
                return [
                    'id'                 => $detalle->id,
                    'producto_id'        => $detalle->producto_id,
                    'producto_codprod'   => $detalle->producto_codprod,
                    'producto_descrip'   => $detalle->producto_descrip,
                    'cantidad'           => $detalle->cantidad,
                    'cantidad_facturada' => $detalle->cantidad_facturada ?? 0,
                    'cantidad_recibida'  => $detalle->cantidad_recibida,
                    'precio_unitario'    => $detalle->precio_unitario,
                    'subtotal'           => $detalle->subtotal
                ];
            })
        ]);
    }

    public function update(Request $request, $id)
    {
        $pago = PagoProveedor::findOrFail($id);

        $request->validate([
            'fecha_pago' => 'required|date',
            'codprov' => 'required|exists:saprov,codprov',
            'notas' => 'nullable|string'
        ]);

        $pago->update([
            'fecha_pago' => $request->fecha_pago,
            'codprov'   => $request->codprov,
            'notas'     => $request->notas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pago actualizado correctamente'
        ]);
    }

    public function destroy($id)
    {
        $pago = PagoProveedor::findOrFail($id);

        // Eliminar comprobantes físicos
        foreach ($pago->comprobantes as $comprobante) {
            if ($comprobante->archivo_path) {
                $rutaArchivo = public_path($comprobante->archivo_path);
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
            }
        }

        $pago->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pago eliminado correctamente'
        ]);
    }

    public function motosPorFecha(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        $pagos = PagoProveedor::with(['detalles', 'comprobantes'])
            ->whereHas('comprobantes', function ($query) use ($desde, $hasta) {
                $query->whereBetween('fecha_comprobante', [$desde, $hasta]);
            })
            ->get();

        $totalMonto  = 0;
        $totalMotos  = 0;
        $totalmotosC = 0;
        $totalmotost = 0;

        foreach ($pagos as $pago) {
            $totalmotost += $pago->monto_total;
            $comprobantes = PagoComprobante::where('pago_id', $pago->id)->whereBetween('fecha_comprobante', [$desde, $hasta])->get();
            foreach ($comprobantes as $comprobante) {
                $totalMonto += $comprobante->monto;
            }
            $totalmotosC += $pago->total_productos;
        }
        if($totalmotost > 0)
            $totalMotos = number_format((($totalMonto * $totalmotosC) / $totalmotost),0,'','');

        return response()->json([
            'total_motos' => number_format($totalMotos, 0),
            'total_monto' => number_format($totalMonto, 2)
        ]);
    }

    // Productos
    public function productosForm($id)
    {
        $pago = PagoProveedor::with('detalles.producto')->findOrFail($id);
        $view = view('pagos-proveedores.partials.form-productos', compact('pago'))->render();
        return response()->json(['html' => $view]);
    }

    public function agregarProducto(Request $request, $id)
    {
        $pago = PagoProveedor::findOrFail($id);

        $request->validate([
            'producto_id'     => 'required|exists:saprod,id',
            'cantidad'        => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0'
        ]);

        $producto = Saprod::find($request->producto_id);

        $detalle = PagoProveedorDetalle::create([
            'pago_id'           => $pago->id,
            'producto_id'       => $producto->id,
            'producto_codprod'  => $producto->codprod,
            'producto_descrip'  => $producto->descrip,
            'cantidad'          => $request->cantidad,
            'cantidad_recibida' => 0,
            'precio_unitario'   => $request->precio_unitario,
            'subtotal'          => $request->cantidad * $request->precio_unitario
        ]);

        // Actualizar monto total del pago
        $pago->monto_total += $detalle->subtotal;
        $pago->save();

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado correctamente',
            'detalle' => $detalle->load('producto')
        ]);
    }

    public function eliminarProducto($id, $detalleId)
    {
        $pago    = PagoProveedor::findOrFail($id);
        $detalle = PagoProveedorDetalle::findOrFail($detalleId);

        if ($detalle->cantidad_recibida > 0) {
            return response()->json([
                'success' => false,
                'error' => 'No se puede eliminar porque ya se recibieron unidades de este producto'
            ], 400);
        }

        $pago->monto_total -= $detalle->subtotal;
        $pago->save();

        $detalle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]);
    }

    // Comprobantes
    public function comprobantesForm($id)
    {
        $pago = PagoProveedor::findOrFail($id);
        $view = view('pagos-proveedores.partials.form-comprobante', compact('pago'))->render();
        return response()->json(['html' => $view]);
    }
    public function getComprobantes($id)
    {
        $pago = PagoProveedor::with('comprobantes')->findOrFail($id);
        $comprobantes = $pago->comprobantes()->orderBy('fecha_comprobante', 'desc')->get();

        $view = view('pagos-proveedores.partials.lista-comprobantes', compact('pago', 'comprobantes'))->render();
        return response()->json(['html' => $view]);
    }

    public function agregarComprobante(Request $request, $id)
    {
        $pago = PagoProveedor::findOrFail($id);

        $request->validate([
            'tipo_comprobante' => 'nullable|string|max:50',
            'numero_comprobante' => 'nullable|string|max:100',
            'monto' => 'required|numeric|min:0',
            'fecha_comprobante' => 'required|date',
            'archivo' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240', // Aumentado a 10MB para permitir compresión
            'notas' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only(['tipo_comprobante', 'numero_comprobante', 'monto', 'fecha_comprobante', 'notas']);
            $data['pago_id'] = $pago->id;

            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                $extension = strtolower($archivo->getClientOriginalExtension());

                // Crear directorio si no existe
                $uploadPath = public_path('uploads/comprobantes');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Generar nombre único
                $nombre_archivo = time() . '_' . uniqid() . '.' . $extension;
                $ruta_completa = $uploadPath . '/' . $nombre_archivo;

                // Procesar según tipo de archivo
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    // Es imagen - aplicar compresión
                    $this->comprimirImagen($archivo->getPathname(), $ruta_completa, $extension);
                } else {
                    // Es PDF - solo mover sin comprimir
                    $archivo->move($uploadPath, $nombre_archivo);
                }

                $data['archivo_path'] = 'uploads/comprobantes/' . $nombre_archivo;
            }

            PagoComprobante::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Comprobante agregado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Error al agregar comprobante: ' . $e->getMessage()
            ], 500);
        }
    }


    public function exportarResumen()
    {
        $pagos = PagoProveedor::with(['proveedor', 'detalles', 'comprobantes'])
            ->withCount('comprobantes')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];
        foreach ($pagos as $pago) {
            $totalComprobantes = $pago->comprobantes->sum('monto');
            $data[] = [
                'Notas'              => $pago->notas,
                'Proveedor'          => $pago->proveedor->descrip ?? $pago->codprov,
                'Fecha Pago'         => $pago->fecha_pago->format('d/m/Y'),
                'Estado'             => $pago->estado,
                'Total Motos'        => $pago->total_productos,
                'Monto Pago'         => $pago->monto_total,
                'Total Comprobantes' => $totalComprobantes,
                'Diferencia'         => $pago->monto_total - $totalComprobantes,
                'N° Comprobantes'    => $pago->comprobantes_count,
                'N° Aprobación'      => $pago->numero_aprobacion ?? '',
            ];
        }

        return Excel::download(new ResumenPagosExport($data), 'resumen_pagos_' . now()->format('Y-m-d') . '.xlsx');
    }


    public function resumenGeneral(Request $request)
    {
        $tipo = $request->input('tipo', 'pendientes'); // 'pendientes', 'todos', 'completos'

        $pagos = PagoProveedor::with(['proveedor', 'detalles.producto.instancia', 'comprobantes'])
            ->withCount('comprobantes')
            ->orderBy('created_at', 'desc')
            ->get();

        $resumenPagos = [];
        $totalGeneralMotos = 0;
        $totalGeneralMontoPagos = 0;
        $totalGeneralComprobantes = 0;
        $totalGeneralDiferencia = 0;
        $pagosIncompletos = 0;
        $pagosCompletos = 0;

        foreach ($pagos as $pago) {
            $totalComprobantes = $pago->comprobantes->sum('monto');
            $diferencia = $pago->monto_total - $totalComprobantes;

            // Verificar si el pedido está pagado (comprobantes cubren el monto total)
            $estaPagado = $totalComprobantes >= $pago->monto_total;

            // Verificar si aún tiene motos pendientes por recibir
            $tieneMotosPendientes = $pago->total_pendiente > 0;

            // Filtrar según el tipo solicitado
            $incluir = false;
            if ($tipo == 'pendientes' && $diferencia > 0) {
                $incluir = true;
            } elseif ($tipo == 'completos' && $estaPagado && $tieneMotosPendientes) {
                // SOLO pedidos pagados que aún tienen motos por recibir
                $incluir = true;
            } elseif ($tipo == 'todos') {
                $incluir = true;
            }

            if ($incluir) {
                if ($diferencia > 0) {
                    $pagosIncompletos++;
                    $estadoComprobantes = $totalComprobantes > 0 ? 'parcial' : 'pendiente';
                } else {
                    $pagosCompletos++;
                    $estadoComprobantes = 'completo';
                }

                // Agrupar motos por instancia padre
                $motosPorInstancia = [];
                foreach ($pago->detalles as $detalle) {
                    $instancia = $detalle->producto->instancia ?? null;
                    if ($instancia) {
                        $instanciaPadre = $detalle->producto->instancia->descrip;
                        $nombreInstanciaPadre = $instanciaPadre;

                        if (!isset($motosPorInstancia[$nombreInstanciaPadre])) {
                            $motosPorInstancia[$nombreInstanciaPadre] = 0;
                        }
                        $motosPorInstancia[$nombreInstanciaPadre] += $detalle->cantidad;
                    }
                }

                $totalGeneralMotos += $pago->total_productos;
                $totalGeneralMontoPagos += $pago->monto_total;
                $totalGeneralComprobantes += $totalComprobantes;
                $totalGeneralDiferencia += max(0, $diferencia);

                $resumenPagos[] = [
                    'id' => $pago->id,
                    'notas' => $pago->notas,
                    'proveedor' => $pago->proveedor->descrip ?? $pago->codprov,
                    'fecha_pago' => $pago->fecha_pago,
                    'estado_pago' => $pago->estado,
                    'total_motos' => $pago->total_productos,
                    'motos_por_instancia' => $motosPorInstancia,
                    'monto_total' => $pago->monto_total,
                    'total_comprobantes' => $totalComprobantes,
                    'diferencia' => $diferencia,
                    'estado_comprobantes' => $estadoComprobantes,
                    'cantidad_comprobantes' => $pago->comprobantes_count,
                    'numero_aprobacion' => $pago->numero_aprobacion,
                    'motos_pendientes_recibir' => $pago->total_pendiente, // Agregar esta línea
                    'esta_pagado' => $estaPagado // Agregar esta línea
                ];
            }
        }

        $estadisticas = [
            'total_pagos' => count($resumenPagos),
            'total_motos' => $totalGeneralMotos,
            'total_monto_pagos' => $totalGeneralMontoPagos,
            'total_comprobantes' => $totalGeneralComprobantes,
            'total_diferencia' => $totalGeneralDiferencia,
            'pagos_incompletos' => $pagosIncompletos,
            'pagos_completos' => $pagosCompletos,
            'tasa_completitud' => ($pagosIncompletos + $pagosCompletos) > 0 ? round(($pagosCompletos / ($pagosIncompletos + $pagosCompletos)) * 100, 2) : 0
        ];

        $view = view('pagos-proveedores.partials.resumen-general', compact('resumenPagos', 'estadisticas', 'tipo'))->render();
        return response()->json(['html' => $view]);
    }

    /**
     * Obtener la instancia padre (nivel 1) de una instancia
     */
    private function getInstanciaPadre($instancia)
    {
        if (!$instancia) return null;

        // Si ya es nivel 1, retornar la misma
        if ($instancia->nivel == 1) {
            return $instancia;
        }

        // Buscar la instancia padre recursivamente
        $padre = $instancia->padre;
        while ($padre && $padre->nivel > 1) {
            $padre = $padre->padre;
        }

        return $padre;
    }

    /**
     * Comprimir imagen antes de guardar
     */
    private function comprimirImagen($rutaOrigen, $rutaDestino, $extension)
    {
        // Crear manager de imágenes con driver GD
        $manager = new ImageManager(new Driver());

        // Cargar la imagen
        $imagen = $manager->read($rutaOrigen);

        // Obtener dimensiones originales
        $anchoOriginal = $imagen->width();
        $altoOriginal = $imagen->height();

        // Calcular nuevas dimensiones (máximo 1920px en el lado más largo)
        $maxLado = 1920;
        $ratio = 1;

        if ($anchoOriginal > $maxLado || $altoOriginal > $maxLado) {
            if ($anchoOriginal > $altoOriginal) {
                $ratio = $maxLado / $anchoOriginal;
            } else {
                $ratio = $maxLado / $altoOriginal;
            }
        }

        $nuevoAncho = round($anchoOriginal * $ratio);
        $nuevoAlto = round($altoOriginal * $ratio);

        // Redimensionar si es necesario
        if ($ratio < 1) {
            $imagen->resize($nuevoAncho, $nuevoAlto);
        }

        // Guardar la imagen comprimida según el tipo
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                // Calidad 75%
                $imagen->toJpeg(75)->save($rutaDestino);
                break;
            case 'png':
                // Para PNG, usar compresión (0-9, 9 es máxima)
                $imagen->toPng()->save($rutaDestino);
                break;
            case 'gif':
                $imagen->toGif()->save($rutaDestino);
                break;
            default:
                // Si no se reconoce, guardar como JPEG por defecto
                $imagen->toJpeg(75)->save($rutaDestino);
                break;
        }

        // Si la imagen comprimida es más grande que la original, usar la original
        if (file_exists($rutaDestino) && filesize($rutaDestino) > filesize($rutaOrigen)) {
            copy($rutaOrigen, $rutaDestino);
        }
    }

    public function eliminarComprobante($id, $comprobanteId)
    {
        $comprobante = PagoComprobante::findOrFail($comprobanteId);

        // Eliminar el archivo físico usando la misma lógica que las transferencias
        if ($comprobante->archivo_path) {
            $rutaArchivo = public_path($comprobante->archivo_path);
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
        }

        $comprobante->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comprobante eliminado correctamente'
        ]);
    }


    // Despachos
    public function despachosForm($id)
    {
        $pago = PagoProveedor::with(['detalles' => function($q) {
            $q->whereRaw('cantidad_recibida < cantidad');
        }])->findOrFail($id);

        $view = view('pagos-proveedores.partials.form-despacho', compact('pago'))->render();
        return response()->json(['html' => $view]);
    }

    public function registrarDespacho(Request $request, $id)
    {
        $pago = PagoProveedor::findOrFail($id);

        $request->validate([
            'fecha_despacho'         => 'required|date',
            'numero_guia'            => 'nullable|string|max:100',
            'notas'                  => 'nullable|string',
            'productos'              => 'required|array',
            'productos.*.detalle_id' => 'required|exists:pagos_proveedores_detalles,id',
            'productos.*.cantidad'   => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            // Crear el despacho
            $despacho = DespachoProveedor::create([
                'pago_id'        => $pago->id,
                'fecha_despacho' => $request->fecha_despacho,
                'numero_guia'    => $request->numero_guia,
                'notas'          => $request->notas
            ]);

            // Registrar los detalles del despacho
            foreach ($request->productos as $producto) {
                $detalle   = PagoProveedorDetalle::find($producto['detalle_id']);
                $pendiente = $detalle->cantidad - $detalle->cantidad_recibida;

                if ($producto['cantidad'] > $pendiente) {
                    throw new \Exception("La cantidad recibida del producto {$detalle->producto_descrip} excede lo pendiente");
                }

                $detalle->cantidad_recibida  = $detalle->cantidad_recibida  + $producto['cantidad'];
                $detalle->cantidad_facturada = $detalle->cantidad_facturada - $producto['cantidad'];
                $detalle->save();

                DespachoProveedorDetalle::create([
                    'despacho_id'       => $despacho->id,
                    'pago_detalle_id'   => $producto['detalle_id'],
                    'cantidad_recibida' => $producto['cantidad']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Recepción registrada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function eliminarDespacho($id, $despachoId)
    {
        $despacho = DespachoProveedor::findOrFail($despachoId);

        DB::beginTransaction();

        try {
            // Los detalles se eliminan automáticamente por cascade
            $despacho->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Despacho eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar despacho: ' . $e->getMessage()
            ], 500);
        }
    }

    // Aprobación
    public function asignarAprobacion(Request $request, $id)
    {
        $pago = PagoProveedor::findOrFail($id);

        $request->validate([
            'numero_aprobacion' => 'required|string|max:100'
        ]);

        $pago->numero_aprobacion = $request->numero_aprobacion;

        $pago->save();

        return response()->json([
            'success' => true,
            'message' => 'Número de aprobación asignado correctamente'
        ]);
    }

    public function editarAprobacion(Request $request, $id)
    {
        $pago = PagoProveedor::findOrFail($id);

        $request->validate([
            'numero_aprobacion' => 'required|string|max:100'
        ]);

        $pago->numero_aprobacion = $request->numero_aprobacion;
        $pago->save();

        return response()->json([
            'success' => true,
            'message' => 'Número de aprobación actualizado correctamente'
        ]);
    }

    // Buscar productos
    public function buscarProductos(Request $request)
    {
        $term = $request->get('q', '');

        $busqueda = $term;
        $busqueda = str_replace("\"", "", $busqueda);
        $busqueda = str_replace("'", "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector = explode(" ", $busqueda);

        if ($vector) {
            $numerito = 0;
            $cadena   = '';
            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena  .= ' AND ';
                }
                $cadena  .= "(codprod like '%$value%' or descrip like '%$value%' or refere like '%$value%' or marca like '%$value%' or descrip2 like '%$value%')";
                $numerito++;
            }
        }

        $comercial = session('comercialid');

        // Obtener los productos
        $productos = Saprod::where('comercial', $comercial)
            ->whereRaw($cadena)
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get(['id', 'codprod', 'descrip']);

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        return response()->json($productos);
    }

    public function getDespachos($id)
    {
        $pago = PagoProveedor::with(['despachos.detalles.pagoDetalle', 'proveedor'])->findOrFail($id);
        $despachos = $pago->despachos()->with('detalles.pagoDetalle')->orderBy('created_at', 'desc')->get();

        $view = view('pagos-proveedores.partials.historial-despachos', compact('pago', 'despachos'))->render();
        return response()->json(['html' => $view]);
    }
}
