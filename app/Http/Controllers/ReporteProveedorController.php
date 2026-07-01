<?php
// app/Http/Controllers/ReporteProveedorController.php

namespace App\Http\Controllers;

use App\Models\Cwviajemoto;
use App\Models\Saprov;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReporteProveedorController extends Controller
{
    /**
     * Página principal del reporte del proveedor
     */
    public function index(Request $request)
    {
        // El código del proveedor específico (el que te descuenta)
        $proveedorId = '299805980'; // O el que corresponda

        $proveedor = Saprov::where('codprov', $proveedorId)->first();

        if (!$proveedor) {
            $proveedor = (object)['descrip' => 'Proveedor de Transporte', 'codprov' => $proveedorId];
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $estado = $request->get('estado', 'pendiente');

        // Consulta base: todas las motos donde el proveedor paga
        $query = Cwviajemoto::with(['viaje', 'cliente'])
            ->where('proveedor_paga', true)
            ->where('proveedor_codprov', $proveedorId)
            ->whereHas('viaje', function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [
                    Carbon::parse($fechaInicio)->startOfDay(),
                    Carbon::parse($fechaFin)->endOfDay()
                ]);
            });

        if ($estado !== 'todos') {
            $query->where('estado_conciliacion', $estado);
        }

        $registros = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calcular totales
        $totales = [
            'transporte' => $registros->sum('monto_transporte_proveedor'),
            'retenciones' => $registros->sum('retencion_proveedor'),
            'descuentos' => $registros->sum('descuento_aplicado_cliente'),
            'esperado' => $registros->sum('monto_esperado_cliente'),
            'real' => $registros->sum('monto_real_cliente'),
            'diferencia' => $registros->sum('diferencia'),
            'pendiente' => $registros->where('estado_conciliacion', 'pendiente')->count(),
            'conciliado' => $registros->where('estado_conciliacion', 'conciliado')->count(),
            'discrepancia' => $registros->where('estado_conciliacion', 'discrepancia')->count(),
        ];

        return view('reportes.proveedor.index', compact('registros', 'proveedor', 'totales', 'fechaInicio', 'fechaFin', 'estado'));
    }

    /**
     * Marcar como pagado (conciliar)
     */
    public function marcarPagado(Request $request, $id)
    {
        $registro = Cwviajemoto::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'monto_real' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'notas' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $diferencia = $request->monto_real - ($registro->monto_esperado_cliente ?? 0);
        $estado = abs($diferencia) < 0.01 ? 'conciliado' : 'discrepancia';

        $registro->update([
            'monto_real_cliente' => $request->monto_real,
            'diferencia' => $diferencia,
            'estado_conciliacion' => $estado,
            'notas_conciliacion' => $request->notas,
            'fecha_conciliacion' => $request->fecha_pago,
            'conciliado_por' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pago registrado correctamente',
            'estado' => $estado,
            'diferencia' => $diferencia
        ]);
    }

    /**
     * Resumen general del proveedor
     */
    public function resumen(Request $request)
    {
        $proveedorId = '299805980';

        $query = Cwviajemoto::where('proveedor_paga', true)
            ->where('proveedor_codprov', $proveedorId);

        if ($request->filled('anio')) {
            $query->whereHas('viaje', function($q) use ($request) {
                $q->whereYear('fecha_inicio', $request->anio);
            });
        }

        $resumen = [
            'total_pendiente' => $query->clone()->where('estado_conciliacion', 'pendiente')->sum('monto_esperado_cliente'),
            'total_conciliado' => $query->clone()->where('estado_conciliacion', 'conciliado')->sum('monto_real_cliente'),
            'total_discrepancia' => $query->clone()->where('estado_conciliacion', 'discrepancia')->sum('monto_real_cliente'),
            'por_mes' => []
        ];

        // Totales por mes
        $porMes = $query->select(
            DB::raw('MONTH(fecha_conciliacion) as mes'),
            DB::raw('YEAR(fecha_conciliacion) as anio'),
            DB::raw('SUM(monto_real_cliente) as total')
        )
            ->whereNotNull('fecha_conciliacion')
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return response()->json($resumen);
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportarExcel(Request $request)
    {
        $proveedorId = '299805980';

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

        $registros = Cwviajemoto::with(['viaje', 'cliente'])
            ->where('proveedor_paga', true)
            ->where('proveedor_codprov', $proveedorId)
            ->whereHas('viaje', function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'pagos_proveedor_' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');

        fputcsv($handle, [
            'Fecha Viaje',
            'Viaje',
            'Cliente',
            'Modelo',
            'Cantidad',
            'Monto Transporte',
            'Retención 30%',
            'Debe Pagar (Esperado)',
            'Pagó Real',
            'Diferencia',
            'Estado',
            'Fecha Pago',
            'Notas'
        ]);

        foreach ($registros as $reg) {
            fputcsv($handle, [
                $reg->viaje->fecha_inicio->format('d/m/Y'),
                $reg->viaje->folio ?? $reg->viaje_id,
                $reg->cliente->descrip ?? 'N/A',
                $reg->modelo_moto,
                $reg->cantidad,
                $reg->monto_transporte_proveedor,
                $reg->retencion_proveedor,
                $reg->monto_esperado_cliente,
                $reg->monto_real_cliente,
                $reg->diferencia,
                $reg->estado_conciliacion,
                $reg->fecha_conciliacion ? Carbon::parse($reg->fecha_conciliacion)->format('d/m/Y') : '',
                $reg->notas_conciliacion
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
