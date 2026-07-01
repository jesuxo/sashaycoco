<?php

namespace App\Http\Controllers;

use App\Exports\PlantillaProductosExport;
use App\Exports\SaprodExport;
use App\Imports\ProductosImport;
use App\Imports\SaprodUpdate;
use App\Models\Sacomercial;
use App\Models\Saexis;
use App\Models\Sainsta;
use App\Models\Saitemfac;
use App\Models\Saprod;
use App\Models\Saprodsucursal;
use App\Models\Saserv;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

class SaprodController extends Controller
{
    public function descargarPlantilla()
    {
        try {
            return Excel::download(new PlantillaProductosExport(), 'plantilla_productos_' . date('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar la plantilla: ' . $e->getMessage());
        }
    }

    public function importarcrear(Request $request)
    {
        $request->validate([
            'archivo_productos' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new ProductosImport();
            Excel::import($import, $request->file('archivo_productos'));

            $mensaje = "Importación completada. Productos creados: {$import->getProcesados()}";

            if ($import->getFallidos() > 0) {
                $mensaje .= ". Fallidos: {$import->getFallidos()}";
                session()->flash('errores_importacion', $import->getErrores());
            }

            return redirect()->back()->with('success', $mensaje);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    public function sugerirTransferencias(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        // Obtener parámetros del request
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $codinst = $request->input('codinst', '');

        // VALIDACIÓN: Si no hay instancia seleccionada, no mostrar resultados
        if (empty($codinst)) {
            $sugerencias = [];
            $instancias = Sainsta::where('comercial', $comercialid)
                ->where('tipoins', 0)
                ->orderBy('descrip')
                ->get();
            return view('sugerencias-transferencias', compact('sugerencias', 'instancias', 'fechaInicio', 'fechaFin', 'codinst'));
        }

        // Si no hay fechas, por defecto últimos 30 días
        if (!$fechaInicio || !$fechaFin) {
            $fechaFin = Carbon::now()->format('Y-m-d');
            $fechaInicio = Carbon::now()->subDays(30)->format('Y-m-d');
        }

        // Convertir fechas para la consulta SQL
        $fec1 = $fechaInicio . ' 00:00:00';
        $fec2 = $fechaFin . ' 23:59:59';

        // Obtener la instancia seleccionada y su codalte para buscar subinstancias
        $instanciaSeleccionada = Sainsta::where('codinst', $codinst)->first();
        if (!$instanciaSeleccionada) {
            $sugerencias = [];
            $instancias = Sainsta::where('comercial', $comercialid)
                ->where('tipoins', 0)
                ->orderBy('descrip')
                ->get();

            return view('sugerencias-transferencias', compact('sugerencias', 'instancias', 'fechaInicio', 'fechaFin', 'codinst'));
        }

        $codalte = $instanciaSeleccionada->codalte;

        // Obtener todos los productos de la instancia y sus subinstancias
        // SELECT * FROM saprod WHERE codinst IN (SELECT codinst FROM sainsta WHERE codalte LIKE '$codalte%')
        $productosInstancia = Saprod::where('comercial', $comercialid)
            ->whereIn('codinst', function($query) use ($codalte) {
                $query->select('codinst')
                    ->from('sainsta')
                    ->where('codalte', 'like', $codalte . '%')
                    ->where('tipoins', 0);
            })
            ->pluck('codprod')
            ->toArray();

        // Si no hay productos en esta instancia, no mostrar resultados
        if (empty($productosInstancia)) {
            $sugerencias = [];
            $instancias = Sainsta::where('comercial', $comercialid)
                ->where('tipoins', 0)
                ->orderBy('descrip')
                ->get();

            return view('sugerencias-transferencias', compact('sugerencias', 'instancias', 'fechaInicio', 'fechaFin', 'codinst'));
        }

        // 1. Obtener VENTAS NETAS por producto y sucursal en el período
        $ventasNetas = Saitemfac::selectRaw("
        fk_sucursal,
        CodItem,
        SUM(Cantidad * Signo) as ventas_netas
    ")
            ->whereIn('TipoFac', ['A', 'B'])
            ->where('esserv', 0)
            ->whereIn('CodItem', $productosInstancia) // Solo productos de la instancia seleccionada
            ->whereBetween('FechaE', [$fec1, $fec2])
            ->groupBy('fk_sucursal', 'CodItem')
            ->get();

        // 2. Obtener INVENTARIO ACTUAL por producto y sucursal
        $inventarioActual = Saexis::selectRaw("
        fk_sucursal,
        codprod,
        SUM(existen) as existencias
    ")
            ->whereIn('codprod', $productosInstancia) // Solo productos de la instancia seleccionada
            ->groupBy('fk_sucursal', 'codprod')
            ->get();

        // 3. Estructurar los datos para facilitar el cálculo
        $ventasPorSucursal = [];
        foreach ($ventasNetas as $venta) {
            $key = $venta->fk_sucursal . '|' . $venta->CodItem;
            $ventasPorSucursal[$key] = (int) $venta->ventas_netas; // Convertir a entero
        }

        $inventarioPorSucursal = [];
        foreach ($inventarioActual as $inv) {
            $key = $inv->fk_sucursal . '|' . $inv->codprod;
            $inventarioPorSucursal[$key] = (int) $inv->existencias; // Convertir a entero
        }

        // 4. Obtener todas las sucursales
        $sucursales = Sasucursal::where('fk_comercial', $comercialid)->get();

        // 5. Calcular días del período
        $diasPeriodo = Carbon::parse($fechaInicio)->diffInDays(Carbon::parse($fechaFin)) + 1;

        // 6. Obtener información de los productos
        $productos = Saprod::where('comercial', $comercialid)
            ->whereIn('codprod', $productosInstancia)
            ->get()
            ->keyBy('codprod');

        // 7. Generar sugerencias de transferencia
        $sugerencias = [];

        foreach ($productos as $producto) {
            foreach ($sucursales as $sucursalDestino) {
                $keyDestino = $sucursalDestino->id . '|' . $producto->codprod;

                $ventasDestino = $ventasPorSucursal[$keyDestino] ?? 0;
                $inventarioDestino = $inventarioPorSucursal[$keyDestino] ?? 0;

                // Stock de seguridad: 7 días de ventas promedio (redondeado a entero)
                $promedioVentasDiarias = $ventasDestino / $diasPeriodo;
                $stockSeguridad = (int) ceil($promedioVentasDiarias * 7); // Redondear hacia arriba

                // Solo si hay ventas y stock insuficiente
                if ($ventasDestino > 0 && $inventarioDestino < $stockSeguridad && $stockSeguridad > 0) {
                    $deficit = $stockSeguridad - $inventarioDestino;

                    // Buscar excedente en otras sucursales
                    $origen = null;
                    $excedenteMaximo = 0;

                    foreach ($sucursales as $sucursalOrigen) {
                        if ($sucursalOrigen->id == $sucursalDestino->id) continue;

                        $keyOrigen = $sucursalOrigen->id . '|' . $producto->codprod;
                        $invOrigen = $inventarioPorSucursal[$keyOrigen] ?? 0;
                        $ventasOrigen = $ventasPorSucursal[$keyOrigen] ?? 0;
                        $stockSeguridadOrigen = (int) ceil(($ventasOrigen / $diasPeriodo) * 7);

                        // Excedente = stock actual - stock de seguridad (si es positivo)
                        $excedente = $invOrigen - $stockSeguridadOrigen;
                        if ($excedente > 0 && $excedente > $excedenteMaximo) {
                            $excedenteMaximo = $excedente;
                            $origen = $sucursalOrigen;
                        }
                    }

                    if ($origen) {
                        $cantidadTransferir = min($deficit, $excedenteMaximo);
                        if ($cantidadTransferir > 0) {
                            $sugerencias[] = [
                                'producto_cod' => $producto->codprod,
                                'producto_nombre' => $producto->descrip,
                                'sucursal_destino_id' => $sucursalDestino->id,
                                'sucursal_destino_nombre' => $sucursalDestino->descrip,
                                'sucursal_origen_id' => $origen->id,
                                'sucursal_origen_nombre' => $origen->descrip,
                                'stock_actual_destino' => (int) $inventarioDestino,
                                'stock_seguridad_destino' => (int) $stockSeguridad,
                                'deficit' => (int) $deficit,
                                'excedente_disponible' => (int) $excedenteMaximo,
                                'cantidad_sugerida' => (int) $cantidadTransferir,
                                'ventas_periodo_destino' => (int) $ventasDestino,
                                'precio' => (float) $producto->preciodpro,
                                'costo' => (float) $producto->costod
                            ];
                        }
                    }
                }
            }
        }

        // Ordenar sugerencias por cantidad sugerida (mayor primero)
        usort($sugerencias, function($a, $b) {
            return $b['cantidad_sugerida'] <=> $a['cantidad_sugerida'];
        });

        // Obtener todas las instancias para el filtro
        $instancias = Sainsta::where('comercial', $comercialid)
            ->where('tipoins', 0)
            ->orderBy('descrip')
            ->get();

        $productosAnalizados = $productos->pluck('codprod')->toArray();
        $totalProductos = count($productosAnalizados);

        return view('sugerencias-transferencias', compact(
            'sugerencias', 'instancias', 'fechaInicio', 'fechaFin', 'codinst', 'totalProductos'
        ));


    }

    public function buscarproductoget($codprod, $comercial){

        $producto   = Saprod::where(['codprod'=>$codprod, "comercial" => $comercial])->first();
        session(['comercialid' => $comercial]);
        if(isset($producto) and isset($producto->id)){
            $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
                ->with(['padre'])
                ->where('comercial',$comercial)
                ->orderBy('codalte','asc')->get();

            $id = $producto->id;
            return view('product-edit', compact('instancias','producto', 'id'));
        }else{
            return response()->redirectTo('index');
        }

    }

    public function saprodexport($codalte)
    {
        $file = Excel::download(new SaprodExport($codalte), 'productos.xlsx');

        return $file;
    }


    public function updateSaprodData(Request $request)
    {
        $request->validate([
            'import_file' => [
                'required',
                'file'
            ],
        ]);

        Excel::import(new SaprodUpdate(), $request->file('import_file'));

        return redirect()->back()->with('status', 'Archivo Procesado Exitosamente');
    }

    public function index(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $sucursales = Sasucursal::where("fk_comercial", $comercialid)->get();

        $fechasaux      = '';
        $operacionesrep = '';

        $fechasreport   = (isset($request->fechasreport))? $request->fechasreport : '';
        $codprod        = (isset($request->codprod))? $request->codprod : '';
        $fechashoy      =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
            ->with(['padre','hijos',  'productos'])
            ->where('comercial',$comercialid)
            ->orderBy('codalte','asc')
            ->get();

        // OBTENER ÚLTIMOS PRODUCTOS CREADOS
        $ultimosProductos = Saprod::where('comercial', $comercialid)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calcular total de productos activos
        $totalProductos = Saprod::where('comercial', $comercialid)
            ->where('activo', 1)
            ->count();


        if(isset($codprod) and $codprod !=''){

            $compras = DB::table('saitemcom')
                ->select([
                    'id',
                    'tipocom as tipo',
                    'numerod',
                    'FechaE',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"),
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    'costod as precio',
                    'codubic as dep1',
                    DB::raw("'' as dep2"),
                    'descrip1 as descripcion',
                    DB::raw("'COMPRA' as tipo_movimiento")
                ])
                ->where('coditem', $codprod)
                ->whereBetween('fechae', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $ventas = DB::table('saitemfac')
                ->select([
                    'id',
                    'TipoFac as tipo',
                    'numerod',
                    'FechaE',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"),
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    'costod as precio',
                    'codubic as dep1',
                    DB::raw("'' as dep2"),
                    'Descrip1 as descripcion',
                    DB::raw("'VENTA' as tipo_movimiento")
                ])
                ->where('CodItem', $codprod)
                ->whereBetween('FechaE', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $operaciones = DB::table('saitemopi')
                ->select([
                    'id',
                    'tipoopi as tipo',
                    'numerod',
                    'FechaE',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"),
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    DB::raw('0 as precio'),
                    'codubic as dep1',
                    'codubic2 as dep2',
                    'Descrip1 as descripcion',
                    DB::raw("'OPERACION_INTERNA' as tipo_movimiento")
                ])
                ->where('CodItem', $codprod)
                ->whereBetween('FechaE', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $operacionesrep = $compras->union($ventas)->union($operaciones)
                ->orderBy('FechaE', 'asc')
                ->get();
        }

        return view('product-list', compact(
            'instancias',
            'sucursales',
            'codprod',
            'operacionesrep',
            'fechasreport',
            'ultimosProductos',
            'totalProductos',
        ));
    }

    public function existencias(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid  = session('comercialid') ;
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fksucursal    = (isset($request->fksucursal ))? $request->fksucursal : '';
        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)->orderBy('descrip','asc')
                         ->whereRaw("id in ($arraysucursales)")
                         ->get();
        $instancias    = Sainsta::selectRaw("Descrip as label, descrip, id, nivel, codinst , codalte, insPadre")
            ->where('comercial', $comercialid)
            ->orderBy('codalte','asc')
            ->get();

        $sucursales = Sasucursal::where("fk_comercial", $comercialid)
                      ->whereRaw("id in ($arraysucursales)")
                      ->orderBy('descrip');

        if($fksucursal)
            $sucursales = $sucursales->where('id',$fksucursal);

        $sucursales = $sucursales->get();

        return view('existenciasInstancias', compact( 'fksucursal', 'arraysucursales', 'allsucursales', 'sucursales', 'instancias', 'comercialid') );
    }

    public function existenciasphp(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $codinst     = $request->codinst;
        $justcodinst = $request->justcodinst;
        $fksucursal  = $request->fksucursal;

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte, insPadre")
            ->where('comercial',$comercial)
            ->orderBy('descrip','asc')
            ->get();
        $insPadre = 0;

        $sucursales = Sasucursal::where("fk_comercial", $comercial)
                                ->whereRaw("id in ($arraysucursales)");

        if($fksucursal)
            $sucursales = $sucursales->where('id',$fksucursal);

        $sucursales = $sucursales->get();

        $instanciaselected = '';
        foreach ($instancias as $instancia){
            if($instancia->codinst == $codinst){
                $instanciaselected = $instancia;
                $insPadre = $instancia->insPadre;
                break;
            }
        }
        return view('existenciasInstanciasphp',
                compact(
                    'fksucursal',
                    'insPadre',
                    'codinst',
                    'justcodinst',
                    'sucursales',
                    'instancias',
                    'instanciaselected',
                    'comercial')
        )->render();
    }

    public function json()
    {
        $comercial  = session('comercialid') ;
        $all = Saprod::where('comercial',$comercial)->with(['instancia'])->orderBy('descrip','asc')->get();
        $aux = [];
        $productos = [];
        $noimage = URL::asset('build/images/noimagen.jpg');
        foreach ($all as $item){
            $aux = [
                "id"            => "$item->id",
                "price"         => "$item->costod3",
                "exdecimal"     => "$item->exdecimal",
                "image"         => (isset($item->productImg))? '': $noimage,
                "productTitle"  => "$item->descrip",
                "category"      => $item->instancia->descrip
            ];

            array_push($productos,$aux);
        }
        return response()->json($productos );
    }

    public function productossucursales(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid = session('comercialid');

        if (!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $instancias = Sainsta::porComercial($comercialid)
            ->whereIn('nivel', [1 ]) // Niveles 1 y 2
            ->orderBy('descrip', 'asc')
            ->get();

        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)->whereRaw("id in ($arraysucursales)")->orderBy('descrip','asc')->get();

        $existenciaact = (isset($request->existenciaact ))? $request->existenciaact : '';
        $fksucursal    = (isset($request->fksucursal    ))? $request->fksucursal    : '';
        $codinst       = (isset($request->codinst       ))? $request->codinst       : '';
        $fechasreport  = $request->fechasreport;
        $fechasreport2 = (isset($request->fechasreport2))? $request->fechasreport2 :'';

        $fechasaux     = str_replace(' ', '', $fechasreport);
        $fechasaux2    = str_replace(' ', '', $fechasreport2);
        $fec1  = $fec2  = $fecha1 = $fecha2 = '';
        $d22   = $m22 = $y22 = $d12   = $m12 =$y12 = $fec12 = $fec22 = '';
        $listadoMesAnterior = collect();

        $itemventas    = [];
        $sucursales    = [];
        $sucursales2   = [];
        $itemventas2   = [];
        $cantidadprod  = [];
        $cantidadprod2 = [];
        $preciodprod   = [];
        $costodprod    = [];

        if (strpos($fechasaux, "to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        } else {
            if($fechasreport !=''){
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }
        }

        if($fec1 != ''){

            list($d1, $m1, $y1) = explode("/", $fec1);
            list($d2, $m2, $y2) = explode("/", $fec2);

            $fecha1 = $fec1;
            $fecha2 = $fec2;

            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";

            $listado = $this->obtenerVentasPeriodo($comercialid, $fec1, $fec2, $fksucursal,$codinst);

            $fec12 = $fec22 = '';
            if (strpos($fechasaux2, "to")) {
                list($fec12, $fec22) = explode("to", $fechasaux2);
            } else {
                if($fechasreport2 != ''){
                    list($d12, $m12, $y12) = explode("/", $fechasreport2);
                    $fec12 = "$d12/$m12/$y12";
                    $fechasreport2= "$fec12 to $fec12";
                }
            }

            if (strpos($fec12, "/")) {
                list($d12, $m12, $y12) = explode("/", $fec12);
                if(!$fec22)
                    $fec22= $fec12;
                list($d22, $m22, $y22) = explode("/", $fec22);
                $fec12 = "$y12-$m12-$d12";
                $fec22 = "$y22-$m22-$d22";
            }

        }

        if(  $fec1 != '' and $codinst){

            if ($fec22 != '') {
                $listadoMesAnterior = $this->obtenerVentasPeriodo($comercialid, $fec12, $fec22, $fksucursal, $codinst);
                list($sucursales2, $cantidadprod2, $itemventas2, $costodprod ) = $this->procesarDatosVentas($listadoMesAnterior, 0);
            }

            list($sucursales, $cantidadprod, $itemventas, $costodprod) = $this->procesarDatosVentas($listado, 1);

            asort($sucursales);

            if(!isset($sucursales)) $sucursales = [];

            if(!isset($cantidadprod))  $cantidadprod = [];
            if(!isset($cantidadprod2))  $cantidadprod2 = [];

            if(isset($cantidadprod) and count($cantidadprod) > 0)
                foreach($cantidadprod as $index => $val){
                    if(!isset($cantidadprod2[$index]))
                        $cantidadprod2[$index] = $val;
                }

            if(isset($sucursales2) and count($sucursales2) > 0){
                foreach($sucursales2 as $index => $val){
                    if(!isset($sucursales[$index])){
                        $sucursales[$index] = $val;
                    }
                }
            }

            if(isset($cantidadprod2) and count($cantidadprod2) > 0)
                foreach($cantidadprod2 as $index => $val){
                    if(!isset($cantidadprod[$index])){
                        $cantidadprod[$index] = $val;
                    }
                }

            foreach($itemventas2 as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas[$index][$index2])){
                        $itemventas[$index][$index2] = $arr;
                    }
                }
            }

            foreach($itemventas as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas2[$index][$index2])){
                        $itemventas2[$index][$index2] = $arr;
                    }
                }
            }

        }

        return view('productosSucursales', compact(
            'fecha1',
            'fecha2',
            'instancias',
            'fechasreport',
            'fechasreport2',
            'sucursales',
            'itemventas',
            'itemventas2',
            'codinst',
            'cantidadprod',
            'cantidadprod2',
            'fksucursal',
            'allsucursales',
            'existenciaact'
        ));
    }

    public function resultadosucursales(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid = session('comercialid');

        if (!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $instancias = Sainsta::porComercial($comercialid)
            ->whereIn('nivel', [1 ]) // Niveles 1 y 2
            ->where('tipoins',0)
            ->orderBy('descrip', 'asc')
            ->get();

        $allsucursales  = Sasucursal::where('fk_comercial', $comercialid)->whereRaw("id in ($arraysucursales)")->orderBy('descrip','asc')->get();
        $existenciaact  = (isset($request->existenciaact ))? $request->existenciaact : '';
        $fksucursal     = (isset($request->fksucursal    ))? $request->fksucursal    : '';
        $codinst        = (isset($request->codinst       ))? $request->codinst       : '';
        $fechasreport   = $request->fechasreport;
        $fechasreport2  = (isset($request->fechasreport2) )? $request->fechasreport2 :'';

        $fechasaux      = str_replace(' ', '', $fechasreport);
        $fechasaux2     = str_replace(' ', '', $fechasreport2);
        $fec1  = $fec2  = $fecha1 = $fecha2 = '';
        $d22   = $m22 = $y22 = $d12   = $m12 =$y12 = $fec12 = $fec22 = '';
        $listadoMesAnterior = collect();

        $itemventas    = [];
        $sucursales    = [];
        $sucursales2   = [];
        $itemventas2   = [];
        $cantidadprod  = [];
        $cantidadprod2 = [];
        $preciodprod   = [];
        $costodprod    = [];
        if (strpos($fechasaux, "to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        } else {
            if($fechasreport !=''){
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }
        }

        if($fec1 != ''){

            list($d1, $m1, $y1) = explode("/", $fec1);
            list($d2, $m2, $y2) = explode("/", $fec2);

            $fecha1 = $fec1;
            $fecha2 = $fec2;

            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";


            $listado = $this->obtenerVentasPeriodo($comercialid, $fec1, $fec2, $fksucursal, $codinst);

            $fec12 = $fec22 = '';
            if (strpos($fechasaux2, "to")) {
                list($fec12, $fec22) = explode("to", $fechasaux2);
            } else {
                if($fechasreport2 != ''){
                    list($d12, $m12, $y12) = explode("/", $fechasreport2);
                    $fec12 = "$d12/$m12/$y12";
                    $fechasreport2= "$fec12 to $fec12";
                }
            }

            if (strpos($fec12, "/")) {
                list($d12, $m12, $y12) = explode("/", $fec12);
                if(!$fec22)
                    $fec22= $fec12;
                list($d22, $m22, $y22) = explode("/", $fec22);
                $fec12 = "$y12-$m12-$d12";
                $fec22 = "$y22-$m22-$d22";
            }

        }

        if(  $fec1 != '' and $codinst){

            if ($fec22 != '') {
                $listadoMesAnterior = $this->obtenerVentasPeriodo($comercialid, $fec12, $fec22, $fksucursal, $codinst);
                list($sucursales2, $cantidadprod2, $itemventas2, $costodprod, $preciodprod) = $this->procesarDatosVentas($listadoMesAnterior, 0);
            }

            list($sucursales, $cantidadprod, $itemventas, $costodprod, $preciodprod) = $this->procesarDatosVentas($listado, 1);

            asort($sucursales);

            if(!isset($sucursales)) $sucursales = [];

            if(!isset($cantidadprod))  $cantidadprod = [];
            if(!isset($cantidadprod2))  $cantidadprod2 = [];

            if(isset($cantidadprod) and count($cantidadprod) > 0)
                foreach($cantidadprod as $index => $val){
                    if(!isset($cantidadprod2[$index]))
                        $cantidadprod2[$index] = $val;
                }

            if(isset($sucursales2) and count($sucursales2) > 0){
                foreach($sucursales2 as $index => $val){
                    if(!isset($sucursales[$index])){
                        $sucursales[$index] = $val;
                    }
                }
            }

            if(isset($cantidadprod2) and count($cantidadprod2) > 0)
                foreach($cantidadprod2 as $index => $val){
                    if(!isset($cantidadprod[$index])){
                        $cantidadprod[$index] = $val;
                    }
                }

            foreach($itemventas2 as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas[$index][$index2])){
                        $itemventas[$index][$index2] = $arr;
                    }
                }
            }

            foreach($itemventas as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas2[$index][$index2])){
                        $itemventas2[$index][$index2] = $arr;
                    }
                }
            }

        }

        return view('resultadosucursales', compact(
            'fecha1',
            'fecha2',
            'codinst',
            'fechasreport',
            'fechasreport2',
            'sucursales',
            'itemventas',
            'itemventas2',
            'cantidadprod',
            'costodprod',
            'preciodprod',
            'cantidadprod2',
            'fksucursal',
            'allsucursales',
            'instancias',
            'existenciaact'
        ));
    }

    private function obtenerVentasPeriodo($comercialid, $fechaInicio, $fechaFin, $fksucursal, $codinst)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $datainst = '';
        $codalte  = '';
        if(isset($codinst) and $codinst >0){
            $instancia = Sainsta::where('codinst', $codinst)->first();
            $codalte   = (isset($instancia->codalte))? $instancia->codalte : '';
        }

        $datainst = " descomp = 0 ";
        if($codalte !='')
            $datainst .= " and codalte like '$codalte%' ";

        $datos = Saitemfac::whereRaw("TipoFac in ('A','B')")
            ->selectRaw("fk_sucursal, CodItem, SUM(Cantidad*Signo) as salidas, SUM(Cantidad*costodoriginal*Signo) as costod, SUM(Cantidad*preciod*Signo) as preciodpro")
            ->with(['sucursal', 'producto.instancia' => function($q) use($datainst) {
                if($datainst != ''){
                    $q->whereRaw($datainst);
                }
                $q = $q->orderBy('codalte', 'asc');
            }])
            ->whereRaw("esserv = 0 and coditem<>'0101' and fk_sucursal in ($arraysucursales)")
            ->whereHas('sucursal.comercial', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->whereBetween('FechaE', [$fechaInicio . ' 00:00:00.00', $fechaFin . ' 23:58:22.00'])
            ->groupBy(['fk_sucursal', 'CodItem'])->orderBy('fk_sucursal');

        if($comercialid == 1 and $codalte != ''){
            $datos = $datos->whereRaw("CodItem in  (select y.codprod from saprod y, sainsta z where  z.codinst = y.codinst and z.codalte like '$codalte%')");
        }

        if(isset($fksucursal) and $fksucursal != '' and $fksucursal > 0){
            $datos =  $datos->where('fk_sucursal', $fksucursal);
        }


        $datos =  $datos->get();

        return $datos;

    }

    private function procesarDatosVentas($listado, $agruparsucu): array
    {
        $sucursales   = [];
        $itemventas   = [];
        $cantidadprod = [];
        $costodprod   = [];
        $preciodprod  = [];
        $productos    = [];

        if($agruparsucu == 1){

            if (isset($listado)) {
                foreach ($listado as $prodsuc) {
                    if (!isset($sucursales[$prodsuc->sucursal->id]))
                        $sucursales[$prodsuc->sucursal->id] = $prodsuc->sucursal->descrip;

                    if (!isset($cantidadprod[$prodsuc->CodItem . $prodsuc->sucursal->id]))
                        $cantidadprod[$prodsuc->CodItem . $prodsuc->sucursal->id] = 0;

                    if (!isset($costodprod[$prodsuc->CodItem . $prodsuc->sucursal->id]))
                        $costodprod[$prodsuc->CodItem . $prodsuc->sucursal->id] = 0;

                    if (!isset($preciodprod[$prodsuc->CodItem . $prodsuc->sucursal->id]))
                        $preciodprod[$prodsuc->CodItem . $prodsuc->sucursal->id] = 0;

                    if (!isset($productos[$prodsuc->CodItem]))
                        $productos[$prodsuc->CodItem] = ['codprod'=>$prodsuc->producto->codprod, 'existen'=>$prodsuc->producto->existen];

                    if(!isset($prodsuc->producto->instancia))
                        dd($prodsuc);

                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['descrip']   = $prodsuc->producto->descrip;
                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['exdecimal'] = $prodsuc->producto->exdecimal;
                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['existen'] = $prodsuc->producto->existen;

                    $cantidadprod[$prodsuc->CodItem . $prodsuc->sucursal->id] += $prodsuc->salidas;
                    $costodprod  [$prodsuc->CodItem . $prodsuc->sucursal->id] += $prodsuc->costod;
                    $preciodprod [$prodsuc->CodItem . $prodsuc->sucursal->id] += $prodsuc->preciodpro;
                }
            }

            return [$sucursales, $cantidadprod, $itemventas, $costodprod, $preciodprod];
        }else{


            if (isset($listado)) {
                foreach ($listado as $prodsuc) {

                    if (!isset($sucursales[$prodsuc->sucursal->id]))
                        $sucursales[$prodsuc->sucursal->id] = $prodsuc->sucursal->descrip;

                    if (!isset($cantidadprod[$prodsuc->CodItem ]))
                        $cantidadprod[$prodsuc->CodItem ] = 0;

                    if (!isset($costodprod[$prodsuc->CodItem]))
                        $costodprod[$prodsuc->CodItem ] = 0;

                    if (!isset($preciodprod[$prodsuc->CodItem]))
                        $preciodprod[$prodsuc->CodItem ] = 0;

                    if(!isset($prodsuc->producto->instancia))
                        dd($prodsuc);

                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['descrip']   = $prodsuc->producto->descrip;
                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['exdecimal'] = $prodsuc->producto->exdecimal;

                    $cantidadprod[$prodsuc->CodItem] += $prodsuc->salidas;
                    $costodprod  [$prodsuc->CodItem] += $prodsuc->costod;
                    $preciodprod [$prodsuc->CodItem] += $prodsuc->preciodpro;
                }
            }

            return [ $sucursales, $cantidadprod, $itemventas, $costodprod,$preciodprod];

        }
    }

    public function busquedaHomeProd(Request $request)
    {
        $busqueda = $request->busqueda;
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
            ->limit(60)
            ->get();

        // Obtener las sucursales del comercial
        $sucursales = Sasucursal::where('fk_comercial', $comercial)
            ->orderBy('descrip')
            ->get();

        // Para cada producto, obtener las existencias por sucursal
        foreach ($productos as $producto) {
            if(!isset($producto->existencias_por_sucursal))
                $producto->existencias_por_sucursal = [];

            $existencias = Saexis::where('codprod', $producto->codprod)
                ->whereIn('fk_sucursal', $sucursales->pluck('id'))
                ->where('existen','<>',0)
                ->with('deposito')
                ->get();

            $producto->existencias_por_sucursal = $existencias;
        }


        return view('layouts.ajaxbusqueda', compact('productos', 'sucursales'))->render();
    }

    public function saprodsucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $productos = $request->productos;
        $productos = json_decode($productos);

        if (isset($productos))
            foreach ($productos as $producto){
                $aux = Saprodsucursal::where(['codprod' => $producto->codprod, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Saprodsucursal();
                    $rel->codprod     = $producto->codprod;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function existenciasCelulares()
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
                        ->whereRaw("nivel=2 AND   tipoins=0 and codalte like 'S01.%' and comercial=$comercial")
                        ->orderBy('descrip','asc')->get();

        $instanciarr = $instancias->pluck('codinst');
        $instaccodin = implode(",", $instanciarr->toArray());

        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->whereRaw("id in ($arraysucursales)")->get();

        $query = DB::table('saprod as productos')
            ->join('sainsta as instancias', 'productos.codinst', '=', 'instancias.codinst')
            ->join('saexis as existencias', 'productos.codprod', '=', 'existencias.codprod')
            ->join('sasucursal as sucursales', 'existencias.fk_sucursal', '=', 'sucursales.id')
            ->select(
                'instancias.codinst',
                'existencias.fk_sucursal',
                DB::raw('SUM(existencias.existen) as total_cantidad')
            )
            ->where('productos.comercial', $comercial)
            ->whereRaw("   productos.codinst in ($instaccodin)")
            ->where('existencias.existen', '>', 0)
            ->groupBy('existencias.fk_sucursal', 'instancias.codinst')
            ->having('total_cantidad', '>', 0)
            ->orderBy('total_cantidad','desc')
            ->orderBy('instancias.codinst')
            ->orderBy('existencias.fk_sucursal')
            ->get();


        //$query = DB::select($consulta);

        $vectorsucursales = [];
        foreach ($sucursales as $sucursal){

         $vectorsucursales[$sucursal->id] = $sucursal->descrip;

        }
        $vectorinstancias = [];
        foreach ($instancias as $instancia){
            if(!isset($vectorinstancias[$instancia->codinst])){
                $vectorinstancias[$instancia->codinst] = $instancia->descrip;
            }
        }

        $arraysucursal = array();
        $arrayinstanci = array();
        $arraycantidad = array();


        foreach ($query as $item) {
            if(isset($vectorsucursales[$item->fk_sucursal])){
                if(!isset($arraysucursal[$item->fk_sucursal]))
                    $arraysucursal[$item->fk_sucursal] = $vectorsucursales[$item->fk_sucursal];

                if(!isset($arrayinstanci[$item->codinst]) and isset($vectorinstancias[$item->codinst]))
                    $arrayinstanci[$item->codinst] = $vectorinstancias[$item->codinst];

                if(!isset($arraycantidad[$item->codinst][$item->fk_sucursal]))
                    $arraycantidad[$item->codinst][$item->fk_sucursal] = 0;

                $arraycantidad[$item->codinst][$item->fk_sucursal] += $item->total_cantidad;
            }
        }


        return view('existenciasCelulares',
            compact(  'arraysucursal', 'arrayinstanci', 'arraycantidad') );
    }

    public function existenciasCelularesModelos(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $inspadre   = $request->inspadre;

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
            ->whereRaw("nivel=2 AND  codinst = $inspadre and  tipoins = 0 and codalte like 'S01.%'")
            ->orderBy('descrip','asc')->get();


        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->whereRaw("id in ($arraysucursales)")->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->whereRaw("id in ($arraysucursales)")->get();

        $query = DB::table('saprod as productos')
            ->join('sainsta as i', 'productos.codinst', '=', 'i.codinst')
            ->join('saexis as e', 'productos.codprod', '=', 'e.codprod')
            ->join('sasucursal as s', 'e.fk_sucursal', '=', 's.id')
            ->select(
                'productos.codprod',
                'productos.descrip',
                'e.fk_sucursal',
                DB::raw('SUM(e.existen) as total_cantidad')
            )
            ->where('productos.comercial', $comercial)
            ->whereRaw("e.fk_sucursal in ($sucursalIds) and productos.codinst in ($inspadre) ")
            ->where('e.existen', '>', 0)
            ->groupBy('e.fk_sucursal','productos.codprod','productos.descrip')
            ->having('total_cantidad', '>', 0)
            ->orderBy('e.fk_sucursal')
            ->get();

        //$query = DB::select($consulta);

        $vectorsucursales = [];
        foreach ($sucursales as $sucursal){
            if(!isset($vectorsucursales[$sucursal->id])){
                $vectorsucursales[$sucursal->id] = $sucursal->descrip;
            }
        }

        $vectorinstancias = [];
        foreach ($instancias as $instancia){
            if(!isset($vectorinstancias[$instancia->codinst])){
                $vectorinstancias[$instancia->codinst] = $instancia->descrip;
            }
        }

        $arraysucursal = array();
        $arrayinstanci = array();
        $arraycantidad = array();


        foreach ($query as $item) {
            if(!isset($arraysucursal[$item->fk_sucursal]))
                $arraysucursal[$item->fk_sucursal] = $vectorsucursales[$item->fk_sucursal];

            if(!isset($arrayinstanci[$item->codprod])  )
                $arrayinstanci[$item->codprod] =  $item->descrip ;

            if(!isset($arraycantidad[$item->codprod][$item->fk_sucursal]))
                $arraycantidad[$item->codprod][$item->fk_sucursal] = 0;

            $arraycantidad[$item->codprod][$item->fk_sucursal] += $item->total_cantidad;
        }

        asort($arrayinstanci);
        $ajax = ($request->ajax())? 1 : 0;
        $html = view('existenciasCelularesModelos', compact('ajax', 'inspadre', 'arraysucursal',  'arrayinstanci', 'arraycantidad') )->render();

        if ($ajax){
            return $html;
        }else{
            return  view('existenciasCelularesModelosPrint',compact('html'));
        }
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;

        $productos = Saprod::where('comercial',$comercial)
            ->whereRaw("codprod not in (select codprod from saprodsucursal where fk_sucursal=$sucursalid )")->limit(1000)->get();

        $servicios = Saserv::whereRaw("codserv not in (select codserv from saservsucursal where fk_sucursal=$sucursalid )")->get()->take(10);

        return response()->json(['success'=>'success', 'newproductos' => $productos, 'newservicios' => $servicios]);
    }

    public function productosinstsancias(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;
        $codinst     = $request->codinst;

        $sqlcostoinv = "SELECT a.preciodant, a.preciodpro, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from   saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and b.codubic = e.codubic
                                and c.fk_comercial = $comercialid
								and d.codinst = a.codinst
                                and a.codinst = $codinst
								and e.comercial = $comercialid
								and b.existen > 0
                        order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado]);

    }

    public function productosinstsanciascodalte(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;
        $codalte     = $request->codalte;
        $len         = strlen($codalte);

        $sqlcostoinv = "SELECT a.preciodant, a.preciodpro, a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from   saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and b.codubic   = e.codubic
                                and c.fk_comercial = $comercialid
								and a.comercial = $comercialid
								and d.comercial = $comercialid
								and e.comercial = $comercialid
								and d.codinst   = a.codinst
                                and left(d.codalte,$len) = '$codalte'
								and b.existen > 0
                                order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado, 'sqlcostoinv' => $sqlcostoinv]);

    }

    public function viewprodinstsanciascodalte(Request $request)
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $codalte     = $request->codalte;
        $busqueda    = $request->busqueda;
        $len         = strlen($codalte);

        $busqueda = str_replace("\"", "", $busqueda);
        $busqueda = str_replace("'",  "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector = explode(" ", $busqueda);

        if ($vector ) {
            $numerito = 0;
            $cadena   = '';
            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena  .= ' AND ';
                }
                $cadena  .= "(a.codprod like '%$value%' or a.descrip like '%$value%' or a.refere like '%$value%' or a.marca like '%$value%' or a.descrip2 like '%$value%')";
                $numerito++;
            }
        }


        if($cadena!='') $cadena = " and ($cadena) ";

        $sqlcostoinv = "SELECT a.preciodant, a.preciodpro, a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where a.codprod    = b.codprod
                                and b.fk_sucursal  = c.id
								and b.codubic      = e.codubic
                                and c.fk_comercial = $comercial
								and a.comercial    = $comercial
								and d.comercial    = $comercial
								and e.comercial    = $comercial
								$cadena
								and d.codinst      = a.codinst
                                and left(d.codalte,$len) = '$codalte'
								and b.existen <> 0
                                order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        $productos    = [];
        $deposito     = [];
        $existencias  = [];

        foreach($listado as $producto){

            if(!isset($productos[$producto->codprod]))
                $productos[$producto->codprod] = [];

            $productos[$producto->codprod]['descrip']    = $producto->descrip;
            $productos[$producto->codprod]['preciodpro'] = $producto->preciodpro;
            $productos[$producto->codprod]['preciod']    = $producto->preciod;

            if(!isset($deposito[$producto->codubic]))
                $deposito[$producto->codubic] = $producto->deposito;

            if(!isset($existencias[$producto->codprod][$producto->codubic]))
                $existencias[$producto->codprod][$producto->codubic] = 0;

            $existencias[$producto->codprod][$producto->codubic] = $producto->existen;
        }

        return view('productosallinstsancias', compact('productos', 'deposito', 'existencias') )->render();


    }

    public function listprodubic(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function listprodubiccompany(Request $request)
    {
        $codprod    = $request->codprod;
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::with('deposito')
            ->whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function listprodubicinv(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function create()
    {
        $comercialid  = session('comercialid') ;
        if(!$comercialid)
            $comercialid  = 1;

        $comercial    = Sacomercial::find($comercialid);
        $match        = $comercial->match;
//concat( repeat('&nbsp;',((nivel-1)*4)), Descrip )
        $instancias = Sainsta::selectRaw("descrip as label, descrip, id, nivel, codinst ")
            ->with(['padre'])
            ->where('comercial', $match)
            ->orderBy('codalte','asc')->get();

        $last   = 0;
        $product = Saprod::orderBy('id','desc')->first();
        if(isset($product) and $product->codprod != '')
            $last = $product->codprod;

        return view('product-create', compact('instancias','last') );
    }

    public function checkcodprod($codprod)
    {
        $check   = 1;
        $comercial = session('comercialid') ;

        $comercial    = Sacomercial::find($comercial);
        $match        = $comercial->match;

        if($codprod != '')
            $product = Saprod::where(['codprod' => $codprod, 'comercial' => $match])->first();

        if(isset($product) and $product->codprod != '')
            $check = 0;

        return response()->json(['check' => $check ]);
    }

    public function validarCodigo(Request $request)
    {
        $comercial = session('comercialid') ;

        $existe = Saprod::where('codprod', $request->codigo)->where('comercial', $comercial)->exists();

        return response()->json(['existe' => $existe]);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'codinst' => 'required',
            'codprod' => 'required|unique:saprod,codprod,NULL,id,codinst,' . $request->codinst,
            'descrip' => 'required',
        ], [
            'codprod.unique' => 'El código ya existe en esta instancia',
        ]);

        try {

            $comercial = session('comercialid') ;

            $comercial    = Sacomercial::find($comercial);
            $match        = $comercial->match;

            $comerciales = Sacomercial::where('match',$match)->get();

            foreach ($comerciales as $comercial){
                $newprod = new Saprod();
                $newprod->fill($request->all());
                $newprod->codprod   = substr($request->codprod,0,15);

                if(isset($request->preciodpro) and $request->preciodpro >0) {
                    $newprod->preciodant  = 0;
                    $newprod->preciod     = $request->preciodpro;
                    $newprod->preciodpro  = $request->preciodpro;
                }
                if(isset($request->costod3) and $request->costod3 >0) {
                    $newprod->costod  = $request->costod3;
                    $newprod->costod2 = $request->costod3;
                    $newprod->costod3 = $request->costod3;
                }
                $newprod->comercial = $comercial->id;
                $newprod->save();
            }
            return redirect()->route('productos.index');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $producto   = Saprod::find($id);

        $comercialid = session('comercialid') ;

        $comercial    = Sacomercial::find($comercialid);
        $match        = $comercial->match;


        $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
            ->with(['padre'])
            ->where('comercial',$match)
            ->orderBy('codalte','asc')->get();

        return view('product-edit', compact('instancias','producto', 'id'));
    }

    public function update(Request $request, $id)
    {
        $comercialid = session('comercialid') ;

        $comercial    = Sacomercial::find($comercialid);
        $match        = $comercial->match;

        $producto  = Saprod::find($id);
        $producto->fill($request->all());

        if($comercialid == 1 or $comercialid== 3 or $comercialid== 4){
             $producto->esexento = 1;  //// luego ver como manejamos esto
        }

        if(isset($request->preciod)   ) {
            $preciod = $request->preciod;
            $coma = substr_count($preciod, ',');
            $punto = substr_count($preciod, '.');

            if ($coma > 0 and $punto > 0) {
                $preciod = str_replace(".", '', $preciod);
                $preciod = str_replace(",", '.', $preciod);
            }
            if ($coma > 0 and !$punto)
                $preciod = str_replace(",", '.', $preciod);
            $producto->preciod = $preciod;
        }
        /////////////////////////////////////////////
        if(isset($request->preciodpro)) {
            $preciodpro = $request->preciodpro;
            $coma = substr_count($preciodpro, ',');
            $punto = substr_count($preciodpro, '.');

            if ($coma > 0 and $punto > 0) {
                $preciodpro = str_replace(".", '', $preciodpro);
                $preciodpro = str_replace(",", '.', $preciodpro);
            }
            if ($coma > 0 and !$punto)
                $preciodpro = str_replace(",", '.', $preciodpro);
            $producto->preciodpro = $preciodpro;
        }
        /////////////////////////////////////////////
        if(isset($request->preciodant)) {
            $preciodant = $request->preciodant;
            $coma = substr_count($preciodant, ',');
            $punto = substr_count($preciodant, '.');

            if ($coma > 0 and $punto > 0) {
                $preciodant = str_replace(".", '', $preciodant);
                $preciodant = str_replace(",", '.', $preciodant);
            }
            if ($coma > 0 and !$punto)
                $preciodant = str_replace(",", '.', $preciodant);
            $producto->preciodant = $preciodant;
        }
        ////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod) ) {
            $costod = $request->costod;

            $coma = strpos($costod, ',');
            $punto = strpos($costod, '.');

            if ($coma > 0 and $punto > 0) {
                $costod = str_replace(".", '', $costod);
                $costod = str_replace(",", '.', $costod);
            }
            if ($coma > 0 and !$punto)
                $costod = str_replace(",", '.', $costod);

            $producto->costod = $costod;
        }
        ////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod2)) {
            $costod2 = $request->costod2;
            $coma = substr_count($costod2, ',');
            $punto = substr_count($costod2, '.');

            if ($coma > 0 and $punto > 0) {
                $costod2 = str_replace(".", '', $costod2);
                $costod2 = str_replace(",", '.', $costod2);
            }
            if ($coma > 0 and !$punto)
                $costod3 = str_replace(",", '.', $costod2);
            $producto->costod2 = $costod2;
        }
        ////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod3)) {
            $costod3 = $request->costod3;
            $coma = substr_count($costod3, ',');
            $punto = substr_count($costod3, '.');

            if ($coma > 0 and $punto > 0) {
                $costod3 = str_replace(".", '', $costod3);
                $costod3 = str_replace(",", '.', $costod3);
            }
            if ($coma > 0 and !$punto)
                $costod3 = str_replace(",", '.', $costod3);
            $producto->costod3 = $costod3;
        }
        ////////////////////////////////////////////////////////////////////////////

        if(!isset($request->exdecimal))
            $producto->exdecimal = 0;

        if(!$request->activo)
            $producto->activo    = 0;

        $producto->save();

        $codprod  = $producto->codprod;


        $otrosprod = Saprod::where(['codprod'=>$codprod, 'comercial' => $match])->get();
        foreach ($otrosprod as $otro){
            $otro->descrip  = $request->descrip;
            $otro->descrip2 = $request->descrip2;
            $otro->descrip3 = $request->descrip3;
            $otro->descrip4 = $request->descrip4;
            $otro->marca    = $request->marca;
            $otro->codinst  = $request->codinst;
            $otro->refere   = $request->refere;
            $otro->save();
        }

        $prodsucursal = Saprodsucursal::with('producto')->where('codprod', $producto->codprod)->get();
        if($prodsucursal)
            foreach ($prodsucursal as $item){
                if($item->producto->comercial == $match)
                    $item->delete();
            }

        $comerciales = Sacomercial::where('match', $match)->get();

        foreach ($comerciales as $comercial){

            $product = Saprod::where(['codprod' => $codprod, 'comercial' => $comercial->id])
                ->first();

            if(isset($product) and isset($product->codprod) and $product->codprod != ''){
                    //
            }else{
                $newprod = new Saprod();
                $newprod->fill($request->all());
                $newprod->codprod   = $codprod;
                $newprod->preciod   = 0;
                $newprod->preciodpro= 0;
                $newprod->costod    =  0;
                if($comercial->id == 1 or $comercial->id == 2 or $comercial->id == 3){  $newprod->esexento = 1; }else{$newprod->esexento = 0;}
                $newprod->costod2   =  0;
                $newprod->costod3   =  0;
                $newprod->comercial = $comercial->id;
                $newprod->save();
            }
        }

        return redirect()->route('productos.edit',$id);
    }

    public function destroy($id)
    {
        //
    }
}
