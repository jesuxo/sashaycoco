<?php

namespace App\Http\Controllers;

use App\Models\Sacomp;
use App\Models\Saitemcom;
use App\Models\Saprod;
use App\Models\Saprodsucursal;
use App\Models\Saseprcom;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SacompController extends Controller
{

    public function documentoAjax(Request $request)
    {
        $id = $request->id;

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());


        $compra = Sacomp::with(['sucursal'])->whereRaw("fk_sucursal in ($sucursalIds)")
            ->where('id', $id)
            ->first();

        if (!$compra) {
            return response()->json(['success' => false, 'message' => 'Compra no encontrada']);
        }

        $items = Saitemcom::where('tipocom', $compra->tipocom)->whereRaw("fk_sucursal in ($sucursalIds)")
            ->where('numerod', $compra->numerod)
            ->where('codprov', $compra->codprov)
            ->get();

        return response()->json([
            'success' => true,
            'compra' => $compra,
            'items' => $items
        ]);
    }

    public function documento(Request $request)
    {
        $sucursalid    = str_replace("300","",$request->sucursal);
        $compras       = $request->compras;
        $compras       = json_decode($compras);
        $sucursal      = Sasucursal::find($sucursalid);
        $allsucursales = Sasucursal::where("fk_comercial", $sucursal->fk_comercial)->get();

            if(isset($compras)){
                foreach ($compras as $com){

                    if(isset($com->nrounico)){
                        $record = Sacomp::where(['nrounico'=>  $com->nrounico, 'fk_sucursal'=> $sucursalid])->first();

                        $additems = 0;
                        if(!isset($record->id)){
                            $record = new Sacomp();
                            $additems = 1;
                        }else{
                            $record = Sacomp::find($record->id);
                        }

                        $aux = (array) $com;
                        $record->fill($aux) ;
                        $record->fk_sucursal = $sucursalid ;

                        if($additems and isset($com->allitems)){
                            foreach ($com->allitems as $allitem){
                                $newitem = new Saitemcom();
                                $auxitem = (array) $allitem;
                                $newitem->fill($auxitem);
                                $newitem->fk_sucursal = $sucursalid ;
                                $newitem->save();

                                $saprod   = Saprod::where(['codprod'=>  $allitem->coditem, 'comercial'=> $sucursal->fk_comercial])->first();
                                if(isset($saprod) and isset($saprod->codprod)){
                                    $saprod->costod     = (isset($allitem->costod))?    $allitem->costod    : 0;
                                    $saprod->costod2    = (isset($allitem->costod2))?   $allitem->costod2   : 0;
                                    $saprod->costod3    = (isset($allitem->costod3))?   $allitem->costod3   : 0;
                                    $saprod->preciod    = (isset($allitem->preciod))?   $allitem->preciod   : 0;
                                 //   $saprod->preciod2   = (isset($allitem->preciod2))?  $allitem->preciod2  : 0;
                                    $saprod->preciodant = (isset($allitem->preciodant))?$allitem->preciodant: 0;
                                    $saprod->preciodpro = (isset($allitem->preciodpro))?$allitem->preciodpro: 0;
                                    $saprod->save();

                                    foreach($allsucursales as $current){
                                        $sucursalprods = Saprodsucursal::where(['codprod' => $allitem->coditem, 'fk_sucursal' => $current->id])->get();
                                        foreach ($sucursalprods as $sucursalprod) {
                                            $sucursalprod->delete();
                                        }
                                    }
                                }


                            }
                        }

                        if($additems and isset($com->seriales)){
                            foreach ($com->seriales as $seriales){
                                $newser = new Saseprcom();
                                $auxser = (array) $seriales;
                                $newser->fill($auxser);
                                $newser->fk_sucursal = $sucursalid ;
                                $newser->save();
                            }
                        }

                        $record->save();
                    }
                }
            }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

    public function reportecompra(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $status       = (isset($request->status)  )? $request->status   : '';
        $busqueda     = (isset($request->busqueda))? $request->busqueda : '';
        $fechasreport = $request->fechasreport;

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to")){
            list($fec1, $fec2) = explode("to",$fechasaux);
        }else {
            if($fechasreport != '') {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = '';
        $fecha2 = '';

        if($fec1 !=''){

            $fecha1 = $fec1;
            $fecha2 = $fec2;

            list($d1,$m1,$y1) = explode("/",$fec1);
            list($d2,$m2,$y2) = explode("/",$fec2);

            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";

        }

        $cadena = '';
        if($busqueda) {
            $busqueda = str_replace('*',' ',$busqueda);
            $vector = explode(" ",$busqueda);
            $i=0;
            foreach ($vector as $item){
                if($i>0)
                    $cadena .=" and ";
                $cadena .=" (
                      numerod    like '%$item%'
                      or notas1  like '%$item%'
                      or notas2  like '%$item%'
                      or descrip like '%$item%'
                      or codprov like '%$item%'
                      or date_format(fechat,'%d/%m%Y') ='$item'
                       )";
                $i++;
            }
        }

        $compras = Sacomp::with(['sucursal.comercial','items']) //,'seriales'
            ->whereHas('sucursal.comercial', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            });

        if($cadena!='')
            $compras = $compras->whereRaw(" ( $cadena ) ");

        if(isset($fec1) and $fec1 !='')
            $compras = $compras->whereBetween('created_at', [$fec1.' 00:00:00.00', $fec2.' 23:58:22.00']);

        //if($status !=''){
            /*if($status == 2)
                $compras = $compras->where('status', $status)->whereRaw(" tipocom in ('U') ")->limit(50);
            if($status != 2)
                $compras = $compras->whereRaw(" tipocom in ('U','Y') ")->where('status', $status)->limit(500);*/
       //}else{
            $compras = $compras->whereRaw(" tipocom in ('H','I') ")->limit(50);
       // }

        $compras = $compras->orderByDesc('id')->get();

        /*if(isset($compras) and count($compras) > 0)
           foreach($compras as $index => $compra){
               $seriales = 0;
              foreach ($compra->seriales as $item){
                   $seriales += 1;
               }
               if($seriales == 0){
                   $compra->status = 0;
                   $compra->save();
               }
            }
        */
        return view('reporteCompras', compact( 'fechasreport',  'status', 'busqueda', 'comercialid', 'compras', 'fecha1', 'fecha2'));
    }

    public function documentoSacomp(Request $request)
    {
        $ajax = 0;
        $id   = $request->id;

        if($id == null or !is_numeric($id)){
            return response()->redirectTo('index');
        }

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $allsucursales = Sasucursal::where("fk_comercial", $comercial)->get();
        $mysucursales = [];
        foreach ($allsucursales as $sucursal) {
            $mysucursales[$sucursal->id] = $sucursal->descrip;
        }

        $documento = Sacomp::where('id', $id)
            ->with(['items.producto.instancia'])
            ->first();
        if($documento->status == 2){
            $documento->status= 1;
            $documento->save();
        }

        $numerod = $documento->numerod;
        $tipocom = $documento->tipocom;

        return view('documentoCompra', compact('ajax', 'numerod', 'tipocom', 'documento'));
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Sacomp $sacomp)
    {
        //
    }

    public function edit(Sacomp $sacomp)
    {
        //
    }

    public function update(Request $request, Sacomp $sacomp)
    {
        //
    }

    public function destroy(Sacomp $sacomp)
    {
        //
    }
}
