<?php
// app/Http/Controllers/Seriales/SerialController.php

namespace App\Http\Controllers\Seriales;

use App\Http\Controllers\Controller;
use App\Models\Sacomp;
use App\Models\Saprod;
use App\Models\Saseprcom;
use App\Models\Sasucursal;
use App\Services\Serial\SerialTrackerService;
use App\Traits\ComercialTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SerialController extends Controller
{
    use ComercialTrait;

    protected $serialTracker;

    public function __construct(SerialTrackerService $serialTracker)
    {
        $this->serialTracker = $serialTracker;
    }

    public function testAjax(Request $request)
    {
        Log::info('Test AJAX llamado', ['data' => $request->all()]);

        return response()->json([
            'success' => true,
            'message' => 'El AJAX funciona correctamente',
            'received' => $request->serial
        ]);
    }

    /**
     * Vista principal con buscador de seriales
     */
    public function historial()
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        return view('seriales.historial', [
            'historial' => collect(),
            'serial' => null,
            'producto' => null,
            'coincidencias' => collect(),
            'busqueda_actual' => null
        ]);
    }

    /**
     * Buscar seriales en todo el sistema (búsqueda parcial)
     */
    public function buscarSeriales(Request $request)
    {
        $request->validate([
            'serial' => 'required|string|min:2'
        ]);

        $serialBuscado = trim($request->serial);
        $coincidencias = $this->buscarCoincidenciasEnTodo($serialBuscado);

        // Convertir la colección a array para asegurar que sea array en JSON
        $coincidenciasArray = $coincidencias->values()->toArray();

        return response()->json([
            'success' => true,
            'data' => $coincidenciasArray,
            'total' => count($coincidenciasArray),
            'busqueda' => $serialBuscado
        ]);
    }

    /**
     * Buscar coincidencias de serial en todas las tablas (compras, ventas, operaciones)
     */
    private function buscarCoincidenciasEnTodo($serial)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)
            ->orderBy('descrip','asc')->get();
        $sucursalarr = $allsucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        $coincidencias = collect();


        $busqueda = str_replace("\"", "", $serial);
        $busqueda = str_replace("'", "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector   = explode(" ", $busqueda);
        $numerito = 0;
        $cadena   = '';
        if ($vector) {

            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena .= ' AND ';
                }
                $cadena .= "(s.nroserial like '%$value%')";
                $numerito++;
            }
        }

        if($cadena !='')
            $cadena = " ( $cadena ) and ";

        // Buscar en compras (saseprcom)
        $compras = DB::table('saseprcom as s')
            ->join('sacomp as c', function($join) {
                $join->on('s.numerod', '=', 'c.numerod')
                    ->on('s.tipocom', '=', 'c.tipocom');
            })
            ->join('saprod as p', 's.coditem', '=', 'p.codprod')
            ->whereRaw(" $cadena  s.fk_sucursal in ($sucursalIds)")
            ->select(
                's.coditem as codprod',
                's.nroserial as serial',
                'c.fechat as fecha',
                'p.descrip as producto',
                DB::raw("'COMPRA' as origen"),
                'c.tipocom as tipo_documento',
                'c.numerod as numero_documento',
                's.fk_sucursal as sucursal'
            )
            ->orderBy('s.nroserial')
            ->limit(50)
            ->get();

        // Buscar en ventas (saseprfac)
        $ventas = DB::table('saseprfac as s')
            ->join('safact as f', function($join) {
                $join->on('s.numerod', '=', 'f.numerod')
                    ->on('s.tipofac', '=', 'f.tipofac');
            })
            ->join('saprod as p', 's.coditem', '=', 'p.codprod')
            ->whereRaw(" $cadena   s.fk_sucursal in ($sucursalIds)")
            ->select(
                's.coditem as codprod',
                's.nroserial as serial',
                'f.fechat as fecha',
                'p.descrip as producto',
                DB::raw("'VENTA' as origen"),
                'f.tipofac as tipo_documento',
                'f.numerod as numero_documento',
                's.fk_sucursal as sucursal'
            )
            ->orderBy('s.nroserial')
            ->limit(50)
            ->get();

        // Buscar en operaciones (sasepropi)
        $operaciones = DB::table('sasepropi as s')
            ->join('saopei as o', function($join) {
                $join->on('s.numerod', '=', 'o.numerod')
                    ->on('s.tipoopi', '=', 'o.tipoopi');
            })
            ->join('saprod as p', 's.coditem', '=', 'p.codprod')
            ->whereRaw(" $cadena   s.fk_sucursal in ($sucursalIds)")
            ->select(
                's.coditem as codprod',
                's.nroserial as serial',
                'o.fechat as fecha',
                'p.descrip as producto',
                DB::raw("'OPERACION' as origen"),
                'o.tipoopi as tipo_documento',
                'o.numerod as numero_documento',
                's.fk_sucursal as sucursal'
            )
            ->orderBy('s.nroserial')
            ->limit(50)
            ->get();

        // Combinar y eliminar duplicados por serial y codprod
        $todos = $compras->concat($ventas)->concat($operaciones);
        $coincidencias = $todos->unique(function($item) {
            return $item->serial . '_' . $item->codprod;
        });

        return $coincidencias;
    }

    /**
     * Obtener historial vía AJAX para el serial seleccionado
     */
    public function getHistorialAjax(Request $request)
    {
        try {
            $codprod = $request->codprod;
            $serial = urldecode($request->serial);

            $comercialid = session('comercialid');
            if(!$comercialid) {
                session(['comercialid' => 1]);
                $comercialid = 1;
            }

            // Obtener información del producto
            $producto = Saprod::where('codprod', $codprod)
                ->where('comercial', $comercialid)
                ->first();

            // Obtener historial usando el servicio
            $historial = $this->serialTracker->obtenerHistorialCompleto($serial, $codprod);

            // Generar HTML del historial
            $html = view('seriales.partials.historial_content', [
                'historial' => $historial,
                'producto' => $producto,
                'serial' => $serial,
                'codprod' => $codprod
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'serial' => $serial,
                'codprod' => $codprod
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de un serial en formato JSON para modal
     */
    public function historialJson($codprod, $serial)
    {
        try {
            $serial = urldecode($serial);

            $historial = $this->serialTracker->obtenerHistorialCompleto($serial, $codprod);

            return response()->json([
                'success' => true,
                'data' => $historial
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de seriales en compra
     */
    public function estadisticasCompra($compraId)
    {
        $compra = Sacomp::find($compraId);

        if (!$compra) {
            return response()->json([
                'success' => false,
                'message' => 'Compra no encontrada'
            ], 404);
        }

        $seriales = Saseprcom::where('numerod', $compra->numerod)
            ->where('tipocom', $compra->tipocom)
            ->get();

        $estadisticas = $this->serialTracker->obtenerEstadisticasSeriales($seriales);

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }
}
