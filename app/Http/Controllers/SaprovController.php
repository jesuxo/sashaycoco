<?php

namespace App\Http\Controllers;

use App\Models\Cwcxcprv;
use App\Models\Cwviajemoto;
use App\Models\Sainsta;
use App\Models\Saprov;
use App\Models\Saprovsucursal;
use App\Models\Saprod;
use App\Models\Saexis;
use App\Models\Sacomp;
use App\Models\Saitemcom;
use App\Models\Saitemfac;
use App\Models\Sasucursal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaprovController extends Controller
{

    public function buscarPredictivo(Request $request)
    {
        $term = $request->get('term', '');

        $query = Saprov::select('codprov', 'descrip', 'id3', 'telef')
            ->orderBy('descrip', 'asc');

        if ($term && strlen($term) >= 1) {
            $query->where(function($q) use ($term) {
                $q->where('descrip', 'LIKE', "%{$term}%")
                    ->orWhere('id3', 'LIKE', "%{$term}%")
                    ->orWhere('codprov', 'LIKE', "%{$term}%");
            });
        }

        $proveedores = $query->get();

        return response()->json($proveedores);
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $proveedores   = $request->proveedores;
        $proveedores   = json_decode($proveedores);

        if(isset($proveedores))
            foreach ($proveedores as $proveedor){

                $aux = Saprov::where(['codprov' => $proveedor->codprov])->first();
                if(!$aux){
                    $new = new Saprov();


                    $new->id3           = ($proveedor->id3)       ?$proveedor->id3        : '';
                    $new->fax           = ($proveedor->fax)       ?$proveedor->fax        : '';
                    $new->clase         = ($proveedor->clase)     ?$proveedor->clase      : '';
                    $new->telef         = ($proveedor->telef)     ?$proveedor->telef      : '';
                    $new->movil         = ($proveedor->movil)     ?$proveedor->movil      : '';
                    $new->email         = ($proveedor->email)     ?$proveedor->email      : '';
                    $new->direc1        = ($proveedor->direc1)    ?$proveedor->direc1     : '';
                    $new->direc2        = ($proveedor->direc2)    ?$proveedor->direc2     : '';
                    $new->activo        = ($proveedor->activo)    ?$proveedor->activo     : 0;
                    $new->codprov       = $proveedor->codprov;
                    $new->tipoprv       = ($proveedor->tipoprv)   ?$proveedor->tipoprv    : 0;
                    $new->tipoid3       = ($proveedor->tipoid3)   ?$proveedor->tipoid3    : 0;
                    $new->tipoid        = ($proveedor->tipoid)    ?$proveedor->tipoid     : 0;
                    $new->descrip       = ($proveedor->descrip)   ?$proveedor->descrip    : '';
                    $new->represent     = ($proveedor->represent) ?$proveedor->represent  : '';
                    $new->zipcode       = ($proveedor->zipcode)   ?$proveedor->zipcode    : '';
                    $new->blockdesc     = ($proveedor->blockdesc) ?$proveedor->blockdesc  : 0;
                    $new->observa       = ($proveedor->observa)   ?$proveedor->observa    : '';

                    $new->save();

                    $rel              = new Saprovsucursal();
                    $rel->codprov     = $proveedor->codprov;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }else{
                    $aux = Saprovsucursal::where(['codprov' => $proveedor->codprov, 'fk_sucursal'=>$sucursalid])->first();
                    if(!$aux){
                        $rel              = new Saprovsucursal();
                        $rel->codprov     = $proveedor->codprov;
                        $rel->fk_sucursal = $sucursalid;
                        $rel->save();
                    }
                }
            }

        $proveedores = Saprov::whereRaw("codprov not in (select codprov from saprovsucursal where fk_sucursal=$sucursalid )")->get();


        return response()->json(['success'=>'success', 'newproveedores' => $proveedores]);
    }

    public function saprovsucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $proveedores = $request->proveedores;
        $proveedores = json_decode($proveedores);

        if (isset($proveedores))
            foreach ($proveedores as $proveedor){
                $aux = Saprovsucursal::where(['codprov' => $proveedor->codprov, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Saprovsucursal();
                    $rel->codprov     = $proveedor->codprov;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function json()
    {
        $user     = User::where('id',auth()->user()->id)->with(['sucursales.sucursal.saprovsucursales.proveedor'])->first();
        $proveedores = $sucursales = $aux = $all = [];
        foreach ($user->sucursales as $rel){
            array_push($sucursales, $rel->sucursal);
            if(isset($rel->sucursal->Saprovsucursales)){
                foreach ($rel->sucursal->Saprovsucursales as $relproveedor){
                    array_push($proveedores, $relproveedor->proveedor);
                }
            }
        }

        foreach ($proveedores as $item){
            list($fecha,$hora) = explode(" ",$item->created_at);
            list($y,$m,$d) = explode("-",$fecha);
            $aux = [
                "id"        => "$item->id",
                "codprov"   => "$item->codprov",
                "descrip"   => "$item->descrip",
                "telef"     => "$item->telef"." "."$item->movil",
                "date"      => "$y-$m-$d",
                "datelabel" => "$d/$m/$y"
            ];

            array_push($all, $aux);
        }
        return response()->json($all);

    }

    public function productosPanel($codprov, Request $request)
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        // Obtener fechas del request o usar valores por defecto (últimos 3 meses)
        $fecha_desde = $request->fecha_desde ?? date('Y-m-d', strtotime('-3 months'));
        $fecha_hasta = $request->fecha_hasta ?? date('Y-m-d');
        $orden       = $request->orden ?? 'vendido_desc';
        $filtro      = $request->filtro ?? 'todos';

        // Obtener información del proveedor
        $proveedor = Saprov::where('codprov', $codprov)->first();

        if (!$proveedor) {
            return redirect()->route('proveedores.index')->with('error', 'Proveedor no encontrado');
        }

        $comprasData = $this->getComprasProveedor($codprov, $request,10);
        $compras = $comprasData['compras'];

        // Obtener todos los productos de este proveedor
        $productos = $this->getProductosProveedor($codprov, $fecha_desde, $fecha_hasta, $orden, $filtro);

        // Calcular KPI generales
        $kpi = $this->calcularKPIProveedor($codprov, $fecha_desde, $fecha_hasta);

        // Top productos más vendidos
        $top_productos = $this->getTopProductos($codprov, $fecha_desde, $fecha_hasta, 10);

        $sucursales = Sasucursal::where('fk_comercial', $comercial)->get();

        return view('proveedores.productos-panel', compact(
            'proveedor',
            'fecha_desde',
            'fecha_hasta',
            'orden',
            'filtro',
            'productos',
            'kpi',
            'top_productos',
            'compras',
            'sucursales' // Agregar esto
        ));
    }

    /**
     * Obtener productos del proveedor con estadísticas
     */
    private function getProductosProveedor($codprov, $fecha_desde, $fecha_hasta, $orden, $filtro = 'todos')
    {
        $comercial = session('comercialid');
        if (!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        // Obtener todos los códigos de productos que ha comprado este proveedor
        $codigos_productos = Saitemcom::where('codprov', $codprov)->whereRaw("fk_sucursal in ($sucursalIds)")
            ->whereIn('tipocom', ['H', 'I'])
            ->distinct()
            ->pluck('coditem')
            ->toArray();

        if (empty($codigos_productos)) {
            return collect([]);
        }

        // Obtener productos con sus datos básicos
        $query = Saprod::whereIn('codprod', $codigos_productos)
            ->where('comercial', $comercial)
            ->select('codprod', 'descrip', 'marca', 'preciod', 'costod', 'costod2', 'costod3', 'existen', 'codinst');

        // Aplicar filtro de productos con stock
        if ($filtro == 'con_stock') {
            $query->where('existen', '>', 0);
        } elseif ($filtro == 'sin_stock') {
            $query->where('existen', '<=', 0);
        }

        $productos = $query->get();

        // Pre-cargar información de seriales para todos los productos
        $serialesPorProducto = [];
        foreach ($productos as $productoModel) {
            $serialesPorProducto[$productoModel->codprod] = $this->getSerialesCompraPorProducto(
                $codprov,
                $productoModel->codprod,
                $sucursalIds
            );
        }

        $productosArray = [];

        foreach ($productos as $productoModel) {
            $producto = new \stdClass();
            $producto->codprod = $productoModel->codprod;
            $producto->descrip = $productoModel->descrip;
            $producto->marca = $productoModel->marca;
            $producto->preciod = $productoModel->preciod;
            $producto->costod = $productoModel->costod;
            $producto->costod2 = $productoModel->costod2;
            $producto->costod3 = $productoModel->costod3;
            $producto->existen = $productoModel->existen;
            $producto->codinst = $productoModel->codinst;

            // ========== TOTAL COMPRAS ==========
            $producto->total_compras = Saitemcom::where('coditem', $producto->codprod)
                ->whereRaw("fk_sucursal in ($sucursalIds)")
                ->where('codprov', $codprov)
                ->whereIn('tipocom', ['H', 'I'])
                ->whereBetween('fechae', [$fecha_desde, $fecha_hasta])
                ->select(DB::raw('SUM(preciod * cantidad * signo) as total'))
                ->value('total') ?? 0;

            // ========== VENTAS FILTRADAS POR PROVEEDOR ==========
            $infoSerial = $this->getProductoSerialInfo($producto->codprod, $comercial);

            if ($infoSerial['usa_serial']) {
                // Productos CON serial: solo contar ventas de seriales comprados a este proveedor
                $serialesCompra = $serialesPorProducto[$producto->codprod] ?? collect([]);

                if ($serialesCompra->isNotEmpty()) {
                    $itemsVenta = $this->getItemsVentaPorSeriales(
                        $producto->codprod,
                        $serialesCompra,
                        $fecha_desde,
                        $fecha_hasta,
                        $sucursalIds
                    );

                    $producto->unidades_vendidas = $itemsVenta->where('signo', '>', 0)->sum('cantidad');
                    $producto->total_ventas = $itemsVenta->sum(function ($item) {
                        return $item->costodoriginal * $item->cantidad * $item->signo;
                    });

                    // Calcular última venta para días sin venta
                    $ultimaVentaItem = $itemsVenta->where('signo', '>', 0)->sortByDesc('FechaE')->first();
                    if ($ultimaVentaItem) {
                        $fecha_ultima_venta = Carbon::parse($ultimaVentaItem->FechaE);
                        $producto->dias_sin_venta = $fecha_ultima_venta->diffInDays(now());
                    } else {
                        $producto->dias_sin_venta = null;
                    }
                } else {
                    $producto->unidades_vendidas = 0;
                    $producto->total_ventas = 0;
                    $producto->dias_sin_venta = null;
                }
            } else {
                // Productos SIN serial: no podemos distinguir, se cuentan todas las ventas
                $producto->unidades_vendidas = Saitemfac::where('CodItem', $producto->codprod)
                    ->whereRaw("fk_sucursal in ($sucursalIds)")
                    ->whereIn('TipoFac', ['A', 'B'])
                    ->where('signo', '>', 0)
                    ->whereBetween('FechaE', [$fecha_desde, $fecha_hasta])
                    ->select(DB::raw('SUM( cantidad * signo) as total'))
                    ->value('total') ?? 0;

                $producto->total_ventas = Saitemfac::where('CodItem', $producto->codprod)
                    ->whereRaw("fk_sucursal in ($sucursalIds)")
                    ->whereIn('TipoFac', ['A', 'B'])
                    ->whereBetween('FechaE', [$fecha_desde, $fecha_hasta])
                    ->select(DB::raw('SUM(costodoriginal * cantidad * signo) as total'))
                    ->value('total') ?? 0;

                // Calcular última venta para días sin venta
                $ultima_venta = Saitemfac::where('CodItem', $producto->codprod)
                    ->whereRaw("fk_sucursal in ($sucursalIds)")
                    ->whereIn('TipoFac', ['A', 'B'])
                    ->where('signo', '>', 0)
                    ->orderBy('FechaE', 'desc')
                    ->first();

                if ($ultima_venta) {
                    $fecha_ultima_venta = Carbon::parse($ultima_venta->FechaE);
                    $producto->dias_sin_venta = $fecha_ultima_venta->diffInDays(now());
                } else {
                    $producto->dias_sin_venta = null;
                }
            }

            // ========== EXISTENCIA ACTUAL TOTAL ==========
            $producto->existencia_actual = Saexis::where('codprod', $producto->codprod)
                ->whereRaw("fk_sucursal in ($sucursalIds)")
                ->sum('existen');

            // ========== ÚLTIMA COMPRA ==========
            $ultima_compra = Saitemcom::where('coditem', $producto->codprod)
                ->where('codprov', $codprov)
                ->with('compra')
                ->whereIn('tipocom', ['H'])
                ->whereRaw("fk_sucursal in ($sucursalIds)")
                ->orderBy('fechae', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if ($ultima_compra) {
                $producto->ultima_compra_fecha = $ultima_compra->fechae;
                $producto->ultima_compra_dias = Carbon::parse($ultima_compra->fechae)->diffInDays(now());
                $producto->ultima_compra_documento = $ultima_compra->numerod;
                $producto->ultima_compra_id = $ultima_compra->compra ? $ultima_compra->compra->id : null;
                $producto->ultima_compra_cantidad = $ultima_compra->cantidad;
                $producto->ultima_compra_precio = $ultima_compra->preciod;
            } else {
                $producto->ultima_compra_fecha = null;
                $producto->ultima_compra_dias = null;
                $producto->ultima_compra_documento = null;
                $producto->ultima_compra_id = null;
                $producto->ultima_compra_cantidad = null;
                $producto->ultima_compra_precio = null;
            }

            // ========== EXISTENCIAS POR SUCURSAL ==========
            $producto->existencias_por_sucursal = [];
            foreach ($sucursales as $sucursal) {
                $existencia = Saexis::where('codprod', $producto->codprod)
                    ->where('fk_sucursal', $sucursal->id)
                    ->sum('existen');

                $producto->existencias_por_sucursal[$sucursal->id] = [
                    'nombre' => $sucursal->descrip,
                    'existencia' => $existencia
                ];
            }

            // ========== ROTACIÓN ==========
            $producto->rotacion = $producto->existencia_actual > 0
                ? $producto->total_ventas / $producto->existencia_actual
                : 0;

            // ========== DÍAS DE STOCK ==========
            $dias_periodo = Carbon::parse($fecha_desde)->diffInDays(Carbon::parse($fecha_hasta)) + 1;
            $venta_diaria_promedio = $dias_periodo > 0 ? $producto->unidades_vendidas / $dias_periodo : 0;
            $producto->dias_stock = $venta_diaria_promedio > 0
                ? $producto->existencia_actual / $venta_diaria_promedio
                : 0;

            // ========== MARGEN DE CONTRIBUCIÓN ==========
            $producto->margen = $producto->costod > 0
                ? (($producto->preciod - $producto->costod) / $producto->costod) * 100
                : 0;

            $productosArray[] = $producto;
        }

        // Convertir a colección
        $productos = collect($productosArray);

        // Ordenar según criterio
        switch ($orden) {
            case 'vendido_desc':
                $productos = $productos->sortByDesc('total_ventas');
                break;
            case 'vendido_asc':
                $productos = $productos->sortBy('total_ventas');
                break;
            case 'comprado_desc':
                $productos = $productos->sortByDesc('total_compras');
                break;
            case 'comprado_asc':
                $productos = $productos->sortBy('total_compras');
                break;
            case 'existencia_desc':
                $productos = $productos->sortByDesc('existencia_actual');
                break;
            case 'existencia_asc':
                $productos = $productos->sortBy('existencia_actual');
                break;
            case 'rotacion_desc':
                $productos = $productos->sortByDesc('rotacion');
                break;
            case 'rentabilidad_desc':
                $productos = $productos->sortByDesc('margen');
                break;
            case 'unidades_desc':
                $productos = $productos->sortByDesc('unidades_vendidas');
                break;
            default:
                $productos = $productos->sortByDesc('total_ventas');
        }

        // También pasar las sucursales a la vista
        view()->share('sucursales', $sucursales);

        return $productos->values();
    }

    private function getProductoSerialInfo($codprod, $comercial)
    {
        $producto = Saprod::where('codprod', $codprod)
            ->where('comercial', $comercial)
            ->first();

        if (!$producto) {
            return ['usa_serial' => false, 'codinst' => null];
        }

        $instancia = Sainsta::where('codinst', $producto->codinst)
            ->where('comercial', $comercial)
            ->first();

        $usaSerial = $instancia && $instancia->desseri == 1;

        return [
            'usa_serial' => $usaSerial,
            'codinst'    => $producto->codinst
        ];
    }

    private function getSerialesCompraPorProducto($codprov, $codprod, $sucursalIds)
    {
        $seriales = DB::table('saitemcom as ic')
            ->join('saseprcom as sc', function($join) {
                $join->on('ic.tipocom', '=', 'sc.tipocom')
                    ->on('ic.numerod' , '=', 'sc.numerod')
                    ->on('ic.coditem' , '=', 'sc.coditem');
            })
            ->where('ic.codprov', $codprov)
            ->where('ic.coditem', $codprod)
            ->whereIn('ic.tipocom', ['H'])
            ->whereRaw("ic.fk_sucursal in ($sucursalIds)")
            ->whereNotNull('sc.nroserial')
            ->select('sc.nroserial')
            ->distinct()
            ->pluck('sc.nroserial');

        return $seriales;
    }

    private function getItemsVentaPorSeriales($codprod, $seriales, $fecha_desde, $fecha_hasta, $sucursalIds)
    {
        if ($seriales->isEmpty()) {
            return collect([]);
        }

        $array = DB::table('saitemfac as ifac')
            ->join('saseprfac as sf', function ($join) {
                $join->on('ifac.TipoFac', '=', 'sf.TipoFac')
                    ->on('ifac.NumeroD', '=', 'sf.NumeroD')
                    ->on('ifac.NroLinea', '=', 'sf.NroLinea')
                    ->on('ifac.CodItem', '=', 'sf.CodItem');
            })
            ->whereIn('sf.NroSerial', $seriales)
            ->where('ifac.CodItem', $codprod)
            ->whereIn('ifac.TipoFac', ['A', 'B'])
            ->whereBetween('ifac.FechaE', [$fecha_desde, $fecha_hasta])
            ->whereRaw("ifac.fk_sucursal in ($sucursalIds)")
            ->select(
                'ifac.id',
                'ifac.cantidad',
                'ifac.costodoriginal',
                'ifac.signo',
                'ifac.FechaE'
            )
            ->get();

        return  $array;
    }

    /**
     * Calcular KPI generales del proveedor
     */
    private function calcularKPIProveedor($codprov, $fecha_desde, $fecha_hasta)
    {
        $serialesCompra = [];
        $comercial = session('comercialid');
        if (!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        // Obtener todos los códigos de productos que ha comprado este proveedor
        $codigos_productos = Saitemcom::where('codprov', $codprov)
            ->whereRaw("fk_sucursal in ($sucursalIds)")
            ->whereIn('tipocom', ['H', 'I'])
            ->distinct()
            ->pluck('coditem')
            ->toArray();

        if (empty($codigos_productos)) {
            return [
                'total_productos' => 0,
                'total_compras' => 0,
                'total_ventas' => 0,
                'valor_inventario' => 0,
                'existencia_total' => 0,
                'rotacion' => 0,
                'compras_30dias' => 0,
                'ventas_30dias' => 0,
            ];
        }

        // Total de productos
        $total_productos = count($codigos_productos);

        // Total de compras en el período
        $total_compras = Saitemcom::where('codprov', $codprov)
            ->whereRaw("fk_sucursal in ($sucursalIds)")
            ->whereIn('tipocom', ['H', 'I'])
            ->whereBetween('fechae', [$fecha_desde, $fecha_hasta])
            ->select(DB::raw('SUM(preciod * cantidad * signo) as total'))
            ->value('total') ?? 0;

        // ========== TOTAL DE VENTAS FILTRADAS POR PROVEEDOR ==========
        $total_ventas = 0;
        foreach ($codigos_productos as $codprod) {
            $infoSerial = $this->getProductoSerialInfo($codprod, $comercial);

            if ($infoSerial['usa_serial']) {
                if(!isset($serialesCompra[$codprod]))
                    $serialesCompra[$codprod] = $this->getSerialesCompraPorProducto($codprov, $codprod, $sucursalIds);

                if ($serialesCompra[$codprod]->isNotEmpty()) {

                    $ventasProducto = DB::table('saitemfac as ifac')
                        ->join('saseprfac as sf', function ($join) {
                            $join->on('ifac.TipoFac', '=', 'sf.TipoFac')
                                ->on('ifac.NumeroD', '=', 'sf.NumeroD')
                                ->on('ifac.NroLinea', '=', 'sf.NroLinea')
                                ->on('ifac.CodItem', '=', 'sf.CodItem');
                        })
                        ->whereIn('sf.NroSerial', $serialesCompra[$codprod])
                        ->where('ifac.CodItem', $codprod)
                        ->whereIn('ifac.TipoFac', ['A'])
                        ->whereRaw("ifac.fk_sucursal in ($sucursalIds) and date_format(ifac.FechaE,'%Y-%m-%d') between '$fecha_desde' and '$fecha_hasta'")
                        ->select(DB::raw('SUM(ifac.costodoriginal * ifac.cantidad * ifac.signo) as total'))
                        ->get();
                    $total_ventas += $ventasProducto[0]->total;
                }
            } else {
                // Productos sin serial: sumamos todas las ventas
                $ventasProducto = Saitemfac::where('CodItem', $codprod)
                    ->whereRaw("fk_sucursal in ($sucursalIds)")
                    ->whereIn('TipoFac', ['A', 'B'])
                    ->whereBetween('FechaE', [$fecha_desde, $fecha_hasta])
                    ->select(DB::raw('SUM(costodoriginal * cantidad * signo) as total'))
                    ->value('total') ?? 0;

                $total_ventas += $ventasProducto;
            }
        }

        // Valor total del inventario
        $valor_inventario = Saprod::whereIn('codprod', $codigos_productos)
            ->where('comercial', $comercial)
            ->select(DB::raw('SUM(existen * preciodpro) as total'))
            ->value('total') ?? 0;

        // Existencia total
        $existencia_total = Saprod::whereIn('codprod', $codigos_productos)
            ->where('comercial', $comercial)
            ->sum('existen');

        // Rotación de inventario
        $dias_periodo = Carbon::parse($fecha_desde)->diffInDays(Carbon::parse($fecha_hasta)) + 1;
        $venta_diaria_promedio = $dias_periodo > 0 ? $total_ventas / $dias_periodo : 0;
        $rotacion = $venta_diaria_promedio > 0 && $existencia_total > 0
            ? $existencia_total / $venta_diaria_promedio
            : 0;

        // Compras y ventas últimos 30 días
        $fecha_30dias = '2026-03-16';//now()->subDays(30)->format('Y-m-d');
        $hoy = '2026-03-16';//now()->format('Y-m-d');

        $compras_30dias = Saitemcom::where('codprov', $codprov)
            ->whereRaw("fk_sucursal in ($sucursalIds)")
            ->whereIn('tipocom', ['H', 'I'])
            ->whereBetween('fechae', [$fecha_30dias, $hoy])
            ->select(DB::raw('SUM(cantidad * preciod * signo) as total'))
            ->value('total') ?? 0;

        // Ventas últimos 30 días (filtradas por proveedor)
        $ventas_30dias = 0;
        foreach ($codigos_productos as $codprod) {
            $infoSerial = $this->getProductoSerialInfo($codprod, $comercial);

            if ($infoSerial['usa_serial']) {
                if(!isset($serialesCompra[$codprod]))
                    $serialesCompra[$codprod] = $this->getSerialesCompraPorProducto($codprov, $codprod, $sucursalIds);

                if ($serialesCompra[$codprod]->isNotEmpty()) {
                    $ventasProducto = DB::table('saitemfac as ifac')
                        ->join('saseprfac as sf', function ($join) {
                            $join->on('ifac.TipoFac', '=', 'sf.TipoFac')
                                ->on('ifac.NumeroD', '=', 'sf.NumeroD')
                                ->on('ifac.NroLinea', '=', 'sf.NroLinea')
                                ->on('ifac.CodItem', '=', 'sf.CodItem');
                        })
                        ->whereIn('sf.NroSerial', $serialesCompra[$codprod])
                        ->where('ifac.CodItem', $codprod)
                        ->whereIn('ifac.TipoFac', ['A'])
                        ->whereRaw("ifac.fk_sucursal in ($sucursalIds) and date_format(ifac.FechaE,'%Y-%m-%d') between '$fecha_desde' and '$fecha_hasta'")
                        ->select(DB::raw('SUM(ifac.costodoriginal * ifac.cantidad * ifac.signo) as total'))
                        ->get();

                    $ventas_30dias += $ventasProducto[0]->total;
                }
            } else {
                $ventasProducto = Saitemfac::where('CodItem', $codprod)
                    ->whereRaw("fk_sucursal in ($sucursalIds)")
                    ->whereIn('TipoFac', ['A', 'B'])
                    ->whereBetween('FechaE', [$fecha_30dias, $hoy])
                    ->select(DB::raw('SUM(costodoriginal * cantidad * signo) as total'))
                    ->value('total') ?? 0;

                $ventas_30dias += $ventasProducto;
            }
        }

        return [
            'total_productos' => $total_productos,
            'total_compras' => $total_compras,
            'total_ventas' => $total_ventas,
            'valor_inventario' => $valor_inventario,
            'existencia_total' => $existencia_total,
            'rotacion' => $rotacion,
            'compras_30dias' => $compras_30dias,
            'ventas_30dias' => $ventas_30dias,
        ];
    }

    /**
     * Obtener top productos más vendidos
     */
    private function getTopProductos($codprov, $fecha_desde, $fecha_hasta, $limite = 10)
    {
        $comercial = session('comercialid');
        if (!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        // Obtener todos los códigos de productos que ha comprado este proveedor
        $codigos_productos = Saitemcom::where('codprov', $codprov)
            ->whereRaw("fk_sucursal in ($sucursalIds)")
            ->whereIn('tipocom', ['H', 'I'])
            ->distinct()
            ->pluck('coditem')
            ->toArray();

        if (empty($codigos_productos)) {
            return collect([]);
        }

        $productosModels = Saprod::whereIn('codprod', $codigos_productos)
            ->where('comercial', $comercial)
            ->select('codprod', 'descrip', 'preciod', 'costod', 'existen', 'codinst')
            ->get();

        // Pre-cargar información de seriales para todos los productos
        $serialesPorProducto = [];
        foreach ($productosModels as $productoModel) {
            $serialesPorProducto[$productoModel->codprod] = $this->getSerialesCompraPorProducto(
                $codprov,
                $productoModel->codprod,
                $sucursalIds
            );
        }

        $productosArray = [];

        foreach ($productosModels as $productoModel) {
            $producto = new \stdClass();
            $producto->codprod = $productoModel->codprod;
            $producto->descrip = $productoModel->descrip;
            $producto->preciod = $productoModel->preciod;
            $producto->costod = $productoModel->costod;
            $producto->existen = $productoModel->existen;
            $producto->codinst = $productoModel->codinst;

            // ========== VENTAS FILTRADAS POR PROVEEDOR ==========
            $infoSerial = $this->getProductoSerialInfo($producto->codprod, $comercial);

            if ($infoSerial['usa_serial']) {
                // Productos CON serial: solo contar ventas de seriales comprados a este proveedor
                $serialesCompra = $serialesPorProducto[$producto->codprod] ?? collect([]);

                if ($serialesCompra->isNotEmpty()) {
                    $itemsVenta = $this->getItemsVentaPorSeriales(
                        $producto->codprod,
                        $serialesCompra,
                        $fecha_desde,
                        $fecha_hasta,
                        $sucursalIds
                    );

                    $producto->unidades_vendidas = $itemsVenta->where('signo', '>', 0)->sum('cantidad');
                    $producto->monto_ventas = $itemsVenta->sum(function ($item) {
                        return $item->costodoriginal * $item->cantidad;
                    });
                } else {
                    $producto->unidades_vendidas = 0;
                    $producto->monto_ventas = 0;
                }
            } else {
                // Productos SIN serial: no podemos distinguir, se cuentan todas las ventas
                $producto->unidades_vendidas = Saitemfac::where('CodItem', $producto->codprod)
                    ->whereRaw("fk_sucursal in ($sucursalIds)")
                    ->whereIn('TipoFac', ['A', 'B'])
                    ->where('signo', '>', 0)
                    ->whereBetween('FechaE', [$fecha_desde, $fecha_hasta])
                    ->select(DB::raw('SUM(  cantidad * signo) as cantidad') )
                    ->value('cantidad') ?? 0;

                $producto->monto_ventas = Saitemfac::where('CodItem', $producto->codprod)
                    ->whereRaw("fk_sucursal in ($sucursalIds)")
                    ->whereIn('TipoFac', ['A', 'B'])
                    ->where('signo', '>', 0)
                    ->whereBetween('FechaE', [$fecha_desde, $fecha_hasta])
                    ->select(DB::raw('SUM(costodoriginal * signo) as total'))
                    ->value('total') ?? 0;
            }

            $producto->rotacion = $producto->existen > 0 ? $producto->unidades_vendidas / $producto->existen : 0;

            // Calcular días de stock
            $dias_periodo = Carbon::parse($fecha_desde)->diffInDays(Carbon::parse($fecha_hasta)) + 1;
            $venta_diaria = $dias_periodo > 0 ? $producto->unidades_vendidas / $dias_periodo : 0;
            $producto->dias_stock = $venta_diaria > 0 ? $producto->existen / $venta_diaria : 0;

            // Calcular margen
            $producto->margen = $producto->costod > 0
                ? (($producto->preciod - $producto->costod) / $producto->costod) * 100
                : 0;

            $productosArray[] = $producto;
        }

        $productos = collect($productosArray);

        return $productos->where('unidades_vendidas', '>', 0)
            ->sortByDesc('unidades_vendidas')
            ->take($limite)
            ->values();
    }

    /**
     * Obtener productos con stock bajo
     */
    private function getStockBajo($codprov, $limite = 10)
    {

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        // Obtener todos los códigos de productos que ha comprado este proveedor
        $codigos_productos = Saitemcom::where('codprov', $codprov)->whereRaw("fk_sucursal in ($sucursalIds)")
            ->whereIn('tipocom', ['H', 'I'])
            ->distinct()
            ->pluck('coditem')
            ->toArray();

        if (empty($codigos_productos)) {
            return collect([]);
        }

        return Saprod::whereIn('codprod', $codigos_productos)->where('comercial',$comercial)
            ->select('codprod', 'descrip', 'existen')
            ->where('existen', '<', 10) // Stock menor a 10 unidades
            ->orderBy('existen', 'asc')
            ->limit($limite)
            ->get();
    }

    /**
     * Obtener productos más rentables
     */
    private function getProductosRentables($codprov, $limite = 10)
    {

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        // Obtener todos los códigos de productos que ha comprado este proveedor
        $codigos_productos = Saitemcom::where('codprov', $codprov)->whereRaw("fk_sucursal in ($sucursalIds)")
            ->whereIn('tipocom', ['H', 'I'])
            ->distinct()
            ->pluck('coditem')
            ->toArray();

        if (empty($codigos_productos)) {
            return collect([]);
        }

        return Saprod::whereIn('codprod', $codigos_productos)->where('comercial',$comercial)
            ->select(
                'codprod',
                'descrip',
                'costod as costo',
                'preciod as precio',
                DB::raw('(preciod - costod) as ganancia'),
                DB::raw('((preciod - costod) / costod) * 100 as margen')
            )
            ->where('costod', '>', 0)
            ->where('preciod', '>', 0)
            ->orderBy('margen', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Listado principal de proveedores
     */
    public function index(Request $request)
    {
        $busqueda = (isset($request->busqueda))? $request->busqueda :  '';
        $codprov  = (isset($request->codprov))? $request->codprov :  '';
        $tab      = (isset($request->tab))? $request->tab :  'tab5';

        $compras         = [];
        $proveedores     = [];
        $proveedor       = null;
        $pagosPendientes = [];
        $pagosRealizados = [];
        $resumenPagos    = [];

        // Variables para el tab de análisis
        $compras_30dias      = 0;
        $ventas_30dias       = 0;
        $total_productos_stock = 0;
        $valor_inventario    = 0;
        $rotacion_inventario = 0;
        $top_productos       = [];
        $comprasData         = [];
        $totales_compras     = 0;
        $stock_bajo          = [];
        $productos_rentables = [];

        if($busqueda != '') {
            $busqueda = str_replace("\"", "", $busqueda);
            $busqueda = str_replace("'", "", $busqueda);
            $busqueda = str_replace("*", " ", $busqueda);
            $vector = explode(" ", $busqueda);

            if ($vector) {
                $numerito = 0;
                $cadena = '';
                foreach ($vector as $value) {
                    if ($numerito > 0) {
                        $cadena .= ' AND ';
                    }
                    $cadena .= "(codprov like '%$value%' or descrip like '%$value%' or id3 like '%$value%')";
                    $numerito++;
                }
            }

            $proveedores = Saprov::whereRaw($cadena)->orderBy('descrip', 'asc')->limit(60)->get();
        }

        // Si hay código seleccionado, cargar datos del proveedor
        if ($codprov != '') {
            $proveedor = Saprov::where('codprov', $codprov)->first();

            if ($proveedor) {
                // Cargar pagos pendientes de este proveedor (viajes donde paga)
                $pagosPendientes = Cwviajemoto::with(['viaje', 'cliente'])
                    ->where('proveedor_paga', true)
                    ->where('proveedor_codprov', $proveedor->codprov)
                    ->where('estado_conciliacion', 'pendiente')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Cargar pagos realizados (conciliados)
                $pagosRealizados = Cwviajemoto::with(['viaje', 'cliente'])
                    ->where('proveedor_paga', true)
                    ->where('proveedor_codprov', $proveedor->codprov)
                    ->where('estado_conciliacion', 'conciliado')
                    ->orderBy('fecha_conciliacion', 'desc')
                    ->limit(20)
                    ->get();

                // Resumen de pagos por mes
                $resumenPagos = Cwviajemoto::select(
                    DB::raw('YEAR(created_at) as anio'),
                    DB::raw('MONTH(created_at) as mes'),
                    DB::raw('SUM(monto_esperado_cliente) as total_pendiente'),
                    DB::raw('SUM(CASE WHEN estado_conciliacion = "conciliado" THEN monto_real_cliente ELSE 0 END) as total_pagado')
                )
                    ->where('proveedor_paga', true)
                    ->where('proveedor_codprov', $proveedor->codprov)
                    ->groupBy('anio', 'mes')
                    ->orderBy('anio', 'desc')
                    ->orderBy('mes', 'desc')
                    ->limit(6)
                    ->get();

                // === NUEVO: Cargar datos para el tab de análisis ===
                $fecha_30dias = now()->subDays(30)->format('Y-m-d');
                $hoy = now()->format('Y-m-d');

                $kpi = $this->calcularKPIProveedor($codprov, $fecha_30dias, $hoy);

                $compras_30dias = $kpi['compras_30dias'];
                $ventas_30dias = $kpi['ventas_30dias'];
                $total_productos_stock = $kpi['total_productos'];
                $valor_inventario = $kpi['valor_inventario'];
                $rotacion_inventario = $kpi['rotacion'];

                $top_productos = $this->getTopProductos($codprov, $fecha_30dias, $hoy, 10);
                $stock_bajo = $this->getStockBajo($codprov, 10);
                $productos_rentables = $this->getProductosRentables($codprov, 10);
            }

            if ($tab == 'tab5') {
                $comprasData = $this->getComprasProveedor($codprov, $request);
                $compras = $comprasData['compras'];
                $totales_compras = $comprasData['totales'];
            } else {
                $compras = collect([]);
                $totales_compras = ['unidades' => 0, 'monto' => 0];
            }
        }



        return view('proveedores.index', compact(
            'proveedores',
            'proveedor',
            'busqueda',
            'codprov',
            'tab',
            'pagosPendientes',
            'pagosRealizados',
            'resumenPagos',
            'compras_30dias',
            'ventas_30dias',
            'total_productos_stock',
            'valor_inventario',
            'rotacion_inventario',
            'top_productos',
            'stock_bajo',
            'productos_rentables',
            'compras',
            'totales_compras'
        ));
    }

    /**
     * Obtener resumen general de cuentas por pagar de todos los proveedores
     */
    public function getResumenGeneralCuentasPorPagar(Request $request)
    {
        $soloVencidos = $request->get('solovencidos', false);
        $fechavence = $request->get('fechavence', '');

        $query = Cwcxcprv::where('signo', 1)
            ->whereRaw('(monto - abonado) != 0')
            ->select(
                'codprov',
                DB::raw('SUM(monto) as monto'),
                DB::raw('SUM(abonado) as abonado'),
                DB::raw('SUM(monto - abonado) as deuda')
            )
            ->groupBy('codprov');

        // Filtrar solo vencidos
        if ($soloVencidos) {
            $hoy = date('Y-m-d');
            $query->whereExists(function($q) use ($hoy) {
                $q->select(DB::raw(1))
                    ->from('cwcxcprv as c2')
                    ->whereRaw('c2.codprov = cwcxcprv.codprov')
                    ->where('c2.signo', 1)
                    ->whereDate('c2.fechav', '<', $hoy);
            });
        }

        // Filtrar por fecha de vencimiento
        if ($fechavence) {
            list($dv, $mv, $yv) = explode("/", $fechavence);
            $fechaVenceFilter = "$yv-$mv-$dv";
            $query->whereExists(function($q) use ($fechaVenceFilter) {
                $q->select(DB::raw(1))
                    ->from('cwcxcprv as c2')
                    ->whereRaw('c2.codprov = cwcxcprv.codprov')
                    ->where('c2.signo', 1)
                    ->whereDate('c2.fechav', '>=', $fechaVenceFilter);
            });
        }

        $resumen = $query->having('deuda', '>', 0)
            ->orderBy('deuda', 'desc')
            ->get();

        // Agregar nombre del proveedor
        foreach ($resumen as $item) {
            $proveedor = Saprov::where('codprov', $item->codprov)->first();
            $item->empresa = $proveedor ? $proveedor->descrip : $item->codprov;
        }

        $totales = [
            'monto' => $resumen->sum('monto'),
            'abonado' => $resumen->sum('abonado'),
            'deuda' => $resumen->sum('deuda')
        ];

        return response()->json([
            'success' => true,
            'data' => $resumen,
            'totales' => $totales
        ]);
    }

    public function getCuentasPorPagar($codprov, Request $request)
    {
        $soloResumen  = (isset($request->soloResumen) )? $request->soloResumen  : '';
        $solovencidos = (isset($request->solovencidos))? $request->solovencidos : '';
        $fechavence   = (isset($request->fechavence))  ? $request->fechavence   : '';

        $query = Cwcxcprv::where('codprov', $codprov)
            ->where('signo', 1)
            ->whereRaw('(monto - abonado) != 0');

        // Filtrar solo vencidos
        if ($solovencidos) {
            $hoy = date('Y-m-d');
            $query->whereDate('fechav', '<', $hoy);
        }

        // Filtrar por fecha de vencimiento
        if ($fechavence) {
            list($dv, $mv, $yv) = explode("/", $fechavence);
            $fechaVenceFilter = "$yv-$mv-$dv";
            $query->whereDate('fechav', '>=', $fechaVenceFilter);
        }

        if ($soloResumen) {
            // Resumen por proveedor
            $resumen = $query->select(
                'codprov',
                DB::raw('SUM(monto) as monto'),
                DB::raw('SUM(abonado) as abonado'),
                DB::raw('SUM(monto - abonado) as deuda')
            )->groupBy('codprov')->first();

            if ($resumen) {
                // Obtener nombre del proveedor
                $proveedor = Saprov::where('codprov', $codprov)->first();
                $resumen->empresa = $proveedor ? $proveedor->descrip : $codprov;
            }

            return response()->json([
                'success'     => true,
                'soloResumen' => true,
                'data'        => $resumen
            ]);
        } else {
            // Detalle de documentos
            $cuentas = $query->select(
                'id',
                'numerod',
                'concepto',
                'monto',
                'abonado',
                DB::raw('(monto - abonado) as deuda'),
                DB::raw("DATE_FORMAT(fecha, '%d/%m/%Y') as fecha2"),
                DB::raw("DATE_FORMAT(fechav, '%d/%m/%Y') as fechav2"),
                DB::raw("DATE_FORMAT(fechav, '%Y-%m-%d') as fechav")
            )->orderBy('fecha', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $totalMonto   = $cuentas->sum('monto');
            $totalAbonado = $cuentas->sum('abonado');
            $totalDeuda   = $cuentas->sum('deuda');

            return response()->json([
                'success'       => true,
                'soloResumen'   => false,
                'data'          => $cuentas,
                'total_monto'   => $totalMonto,
                'total_abonado' => $totalAbonado,
                'total_deuda'   => $totalDeuda
            ]);
        }
    }

    public function show($id)
    {
        //
    }

    /**
     * Obtener compras del proveedor
     */
    public function getComprasProveedor($codprov, Request $request, $limite = 50)
    {
        $comercial = session('comercialid') ?: 1;

        $sucursales = Sasucursal::where('fk_comercial', $comercial)->pluck('id')->toArray();
        $sucursales = implode(',', $sucursales);

        $query = Sacomp::selectRaw('id,codprov,numerod,tipocom,fk_sucursal,fechae')
                ->where('codprov', $codprov)
                ->with(['sucursal'])
                ->whereIn('tipocom', ['H', 'I'])
                ->whereRaw("fk_sucursal in ($sucursales)" )
                ->orderBy('fechae', 'desc');

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fechae', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fechae', '<=', $request->fecha_hasta);
        }

            $compras = $query->limit($limite)->get();


        // Calcular totales
        $totales = [
            'unidades' => 0,
            'monto' => 0
        ];

        foreach ($compras as $compra) {
            $totalUnidades = 0;
            $totalMonto    = 0;

            $signo = $compra->tipocom == 'I' ? -1 : 1;

            foreach ($compra->items as $item) {
                $totalUnidades += $item->cantidad * $signo;
                $totalMonto    += $item->preciod  * $item->cantidad * $signo;
            }

            $compra->total_unidades_calculado = $totalUnidades;
            $compra->total_monto_calculado    = $totalMonto;

            $totales['unidades'] += $totalUnidades * $signo;
            $totales['monto']    += $totalMonto    * $signo;
        }

        return [
            'compras' => $compras,
            'totales' => $totales
        ];
    }


    public function debug($codprov, $codprod)
    {
        $comercial = session('comercialid') ?: 1;

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        // 1. Verificar seriales comprados a este proveedor
        $serialesCompra = DB::table('saitemcom as ic')
            ->join('saseprcom as sc', function($join) {
                $join->on('ic.tipocom', '=', 'sc.tipocom')
                    ->on('ic.numerod', '=', 'sc.numerod')
                    ->on('ic.coditem', '=', 'sc.coditem');
            })
            ->where('ic.codprov', $codprov)
            ->where('ic.coditem', $codprod)
            ->whereIn('ic.tipocom', ['H', 'I'])
            ->whereRaw("ic.fk_sucursal in ($sucursalIds)")
            ->whereNotNull('sc.nroserial')
            ->select('sc.nroserial')
            ->distinct()
            ->get();


        // 2. Verificar ventas de esos seriales
        $serialesList = $serialesCompra->pluck('nroserial')->toArray();

        if (!empty($serialesList)) {
            $ventas = DB::table('saitemfac as ifac')
                ->join('saseprfac as sf', function ($join) {
                    $join->on('ifac.TipoFac', '=', 'sf.TipoFac')
                        ->on('ifac.NumeroD', '=', 'sf.NumeroD')
                        ->on('ifac.NroLinea', '=', 'sf.NroLinea')
                        ->on('ifac.CodItem', '=', 'sf.CodItem');
                })
                ->whereIn('sf.NroSerial', $serialesList)
                ->where('ifac.CodItem', $codprod)
                ->whereIn('ifac.TipoFac', ['A', 'B'])
                ->whereRaw("ifac.fk_sucursal in ($sucursalIds)")
                ->select(
                    'ifac.id',
                    'ifac.NumeroD',
                    'ifac.TipoFac',
                    'ifac.cantidad',
                    'ifac.costodoriginal',
                    'ifac.signo',
                    'ifac.FechaE',
                    'sf.NroSerial'
                )
                ->get();

        }

        // 3. Verificar ventas totales del producto (sin filtrar)
        $ventasTotales = Saitemfac::where('CodItem', $codprod)
            ->whereRaw("fk_sucursal in ($sucursalIds)")
            ->whereIn('TipoFac', ['A', 'B'])
            ->sum('cantidad');



        return response()->json([
            'seriales_compra' => $serialesCompra,
            'ventas_filtradas' => $ventas ?? [],
            'ventas_totales' => $ventasTotales
        ]);
    }


}
