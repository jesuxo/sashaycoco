<?php

namespace App\Http\Controllers;

use App\Models\Saacxc;
use App\Models\Saclie;
use App\Models\Sacliesucursal;
use App\Models\Safact;
use App\Models\Saprod;
use App\Models\Saprov;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaclieController extends Controller
{

    public function index(Request $request)
    {
        $datetime = Carbon::now()->isoFormat('DD-MM-YYYY');
        $tab      = (isset($request->tab)     )? $request->tab      : 'tab1';
        $busqueda = (isset($request->busqueda))? $request->busqueda : '';
        $codclie  = (isset($request->codclie) )? $request->codclie  : '';
        $clientes = [];
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
                    $cadena .= "(codclie like '%$value%' or descrip like '%$value%' or id3 like '%$value%')";
                    $numerito++;
                }
            }

            $clientes = Saclie::whereRaw($cadena)->orderBy('descrip', 'asc')->limit(60)->get();
        }
        if($codclie != '' and $busqueda == '') {
            $clientes = Saclie::where('codclie',$codclie)->get();
        }

        $cliente  = '' ;
        $listado  = [];
        $listadoc = [];
        if(isset($clientes) and count($clientes) == 1 and !$codclie) {
            $cliente = $clientes[0];
            $codclie = $clientes[0]->codclie;
        }

        if($codclie != ''){
            $cliente  = Saclie::where('codclie',$codclie)->first();

            if($tab == 'tab1'){
                $ventas = Safact::selectRaw("fk_sucursal as fk_sucu, nrounico,descrip,numerod, tipofac, codclie,
                            ((dolares-vuelto_dolares)*Signo) as dolares,
                            (pesos*Signo) as pesos,
                            (peso_tranf*Signo) as peso_tranf ,
                            (euros*Signo) as euros,
                            (dolar_transf*Signo) as transf,
                            ((cancele-efectivosumado-vuelto_cancele)*Signo) as cancele,
                             ((vuelto_cancele)*Signo) as vuelto_cancele,

                            (mtotax*Signo) as mtotax,
                            (TGravable*Signo) as montobase,
                            ((cancelt-tarjetasumado)*Signo) as cancelt,
                            (texento*Signo) as texentofact,
                            (igtf_cancele*Signo) as igtf_cancele,
                            (igtf_cancelt*Signo) as igtf_cancelt ,
                            (igtf_dolares*Signo) as igtf_dolares  ,
                            (igtf_pesos*Signo) as igtf_pesos,
                            (igtf_dolar_transf*Signo) as igtf_transf,
                             (igtf_monto*Signo) as igtf_monto ,
                             date_format(FechaT,'%d/%m/%Y') as fecha,

                             ((mtototal*Signo)/tasa_dolar) as mtototal,
                             (((contado+credito)*Signo)/tasa_dolar) as totalventa,
                             (((cancelaUSD)*Signo)) as cancelaUSD,
                             ((credito*Signo)/tasa_dolar) as credito,
                             ((contado*Signo)/tasa_dolar) as contado
                            ")
                    ->whereRaw(" TipoFac in('A','B','Z','W') and codclie ='$codclie'  ")
                    ->orderBy('FechaT','desc')
                    ->limit(200)
                    ->get();

                if(isset($ventas))
                    foreach ($ventas as $venta) {

                        if(!isset($listado[$venta->nrounico]['fk_sucu']))
                            $listado[$venta->nrounico]['fk_sucu'] ='';
                        $listado[$venta->nrounico]['fk_sucu'] = $venta->fk_sucu;

                        if(!isset($listado[$venta->nrounico]['codclie']))
                            $listado[$venta->nrounico]['codclie'] ='';
                        $listado[$venta->nrounico]['codclie'] = $venta->codclie;

                        if(!isset($listado[$venta->nrounico]['fecha']))
                            $listado[$venta->nrounico]['fecha'] ='';
                        $listado[$venta->nrounico]['fecha'] = $venta->fecha;

                        if(!isset($listado[$venta->nrounico]['numerod']))
                            $listado[$venta->nrounico]['numerod'] ='';
                        $listado[$venta->nrounico]['numerod'] = $venta->numerod;

                        if(!isset($listado[$venta->nrounico]['tipofac']))
                            $listado[$venta->nrounico]['tipofac'] ='';
                        $listado[$venta->nrounico]['tipofac'] = $venta->tipofac;

                        if(!isset($listado[$venta->nrounico]['dolares']))
                            $listado[$venta->nrounico]['dolares'] =0;
                        $listado[$venta->nrounico]['dolares'] = $venta->dolares;

                        if(!isset($listado[$venta->nrounico]['cliente']))
                            $listado[$venta->nrounico]['cliente'] ='';
                        $listado[$venta->nrounico]['cliente'] =$venta->descrip;

                        if(!isset($listado[$venta->nrounico]['pesos']))
                            $listado[$venta->nrounico]['pesos'] =0;
                        $listado[$venta->nrounico]['pesos']   = $venta->pesos;

                        if(!isset($listado[$venta->nrounico]['peso_tranf']))
                            $listado[$venta->nrounico]['peso_tranf'] =0;
                        $listado[$venta->nrounico]['peso_tranf'] = $venta->peso_tranf;

                        if(!isset($listado[$venta->nrounico]['euros']))
                            $listado[$venta->nrounico]['euros'] =0;
                        $listado[$venta->nrounico]['euros']   = $venta->euros;

                        if(!isset($listado[$venta->nrounico]['transf']))
                            $listado[$venta->nrounico]['transf'] =0;
                        $listado[$venta->nrounico]['transf']  = $venta->transf;

                        if(!isset($listado[$venta->nrounico]['cancele']))
                            $listado[$venta->nrounico]['cancele'] =0;
                        $listado[$venta->nrounico]['cancele'] = $venta->cancele;

                        if(!isset($listado[$venta->nrounico]['vuelto_cancele']))
                            $listado[$venta->nrounico]['vuelto_cancele'] =0;
                        $listado[$venta->nrounico]['vuelto_cancele'] = $venta->vuelto_cancele;

                        if(!isset($listado[$venta->nrounico]['cancelt']))
                            $listado[$venta->nrounico]['cancelt'] =0;
                        $listado[$venta->nrounico]['cancelt'] = $venta->cancelt;

                        if(!isset($listado[$venta->nrounico]['credito']))
                            $listado[$venta->nrounico]['credito'] =0;
                        $listado[$venta->nrounico]['credito'] = $venta->credito;

                        if(!isset($listado[$venta->nrounico]['cancelaUSD']))
                            $listado[$venta->nrounico]['cancelaUSD'] =0;
                        $listado[$venta->nrounico]['cancelaUSD'] = $venta->cancelaUSD;

                        if(!isset($listado[$venta->nrounico]['totalventa']))
                            $listado[$venta->nrounico]['totalventa'] =0;
                        $listado[$venta->nrounico]['totalventa'] = $venta->totalventa;
                    }

            }

            if($tab == 'tab2'){

                    $cobranzas = Saacxc::selectRaw(" codusua, dolar_tranf as transf, dolares, codclie,
                 (cancele - (dolares*tasadolar)) as cancele, document, nrounico, euros,cancelausd, monto,
                 (cancelt - (dolar_tranf*tasadolar)) as cancelt,  tasadolar, pesos, peso_tranf, tasapeso,
                 date_format(FechaT, '%d/%m/%Y') as fecha, codvend,  numerod, tipocxc, montodolares, fk_sucursal")
                    ->with([ 'cliente', 'sucursalcli'])
                    ->whereRaw("codclie = '$codclie' and montodolares >0")
                    ->orderBy('fechat','asc')
                    ->get();


                if(isset($cobranzas))
                    foreach ($cobranzas as $cobranza) {

                        if(!isset($listadoc[$cobranza->nrounico]['tipocxc']))
                            $listadoc[$cobranza->nrounico]['tipocxc'] ='';
                        $listadoc[$cobranza->nrounico]['tipocxc'] =$cobranza->tipocxc;

                        if(!isset($listadoc[$cobranza->nrounico]['document']))
                            $listadoc[$cobranza->nrounico]['document'] ='';
                        $listadoc[$cobranza->nrounico]['document'] = $cobranza->document;

                        if(!isset($listadoc[$cobranza->nrounico]['fk_sucu']))
                            $listadoc[$cobranza->nrounico]['fk_sucu'] ='';
                        $listadoc[$cobranza->nrounico]['fk_sucu'] = $cobranza->fk_sucursal;

                        if(!isset($listadoc[$cobranza->nrounico]['sucu']))
                            $listadoc[$cobranza->nrounico]['sucu'] ='';
                        $listadoc[$cobranza->nrounico]['sucu'] = $cobranza->sucursal->descrip;

                        if(!isset($listadoc[$cobranza->nrounico]['codclie']))
                            $listadoc[$cobranza->nrounico]['codclie'] ='';
                        $listadoc[$cobranza->nrounico]['codclie'] = $cobranza->codclie;

                        if(!isset($listadoc[$cobranza->nrounico]['numerod']))
                            $listadoc[$cobranza->nrounico]['numerod'] ='';
                        $listadoc[$cobranza->nrounico]['numerod'] = $cobranza->numerod;

                        if(!isset($listadoc[$cobranza->nrounico]['fecha']))
                            $listadoc[$cobranza->nrounico]['fecha'] =0;
                        $listadoc[$cobranza->nrounico]['fecha']   = $cobranza->fecha;

                        if(!isset($listadoc[$cobranza->nrounico]['cancelausd']))
                            $listadoc[$cobranza->nrounico]['cancelausd'] =0;
                        $listadoc[$cobranza->nrounico]['cancelausd'] = $cobranza->cancelausd;

                        if(!isset($listadoc[$cobranza->nrounico]['tasadolar']))
                            $listadoc[$cobranza->nrounico]['tasadolar'] =0;
                        $listadoc[$cobranza->nrounico]['tasadolar'] = $cobranza->tasadolar;

                        if(!isset($listadoc[$cobranza->nrounico]['monto']))
                            $listadoc[$cobranza->nrounico]['monto'] =0;
                        $listadoc[$cobranza->nrounico]['monto'] = $cobranza->monto;

                        if(!isset($listadoc[$cobranza->nrounico]['montodolares']))
                            $listadoc[$cobranza->nrounico]['montodolares'] =0;
                        $listadoc[$cobranza->nrounico]['montodolares'] = $cobranza->montodolares;

                    }
            }

        }

        return view('clientes', compact('codclie', 'tab', 'busqueda', 'datetime', 'clientes', 'cliente', 'listado', 'listadoc'));
    }

    public function buscarclienteajax(Request $request)
    {
        $cdcd          = (isset($request->cdcd)         )? $request->cdcd          : '';
        $fkbanco       = (isset($request->fkbanco)      )? $request->fkbanco       : '';
        $buscarcliente = (isset($request->buscarcliente))? $request->buscarcliente : '';

        $buscarcliente = str_replace("*", " ", $buscarcliente);
        $buscarcliente = str_replace("\"", "", $buscarcliente);
        $buscarcliente = str_replace("'", "",  $buscarcliente);

        $cadena   = '';
        $numerito = 0;
        $clientes = [];
        if($buscarcliente != '') {
            $vector = explode(" ", $buscarcliente);

            if ($vector) {
                foreach ($vector as $value) {
                    if ($numerito > 0) {
                        $cadena .= ' AND ';
                    }
                    $cadena .= "(codclie like '%$value%' or descrip like '%$value%' or id3 like '%$value%'  )";

                    $numerito++;
                }
            }
            if ($cadena) $cadena = " and ($cadena)";

            $clientes = Saclie::whereRaw("activo = 1 $cadena")->limit(50)->get();
        }
        return view('layouts.buscarclientes', compact('clientes', 'fkbanco', 'cdcd', 'buscarcliente'))->render();
    }

    public function updatecliente(Request $request)
    {
        $LimiteCred  = (isset($request->LimiteCred)) ? $request->LimiteCred : 0;

        $escredito = 0;
        if($LimiteCred > 0){
            $escredito = 1;
        }

        $cliente = Saclie::where('codclie',$request->codclie)->first();
        $cliente->descrip    = (isset($request->descrip)  )? $request->descrip : '';
        $cliente->email      = (isset($request->email)    )? $request->email   : '';
        $cliente->id3        = (isset($request->id3)      )? $request->id3     : '';
        $cliente->clase      = (isset($request->clase)    )? $request->clase   : '';
        $cliente->telef      = (isset($request->telef)    )? $request->telef   : '';
        $cliente->represent  = (isset($request->represent))? $request->represent : '';
        $cliente->movil      = (isset($request->movil)    )? $request->movil   : '';
        $cliente->fax        = (isset($request->fax)      )? $request->fax     : '';
        $cliente->direc1     = (isset($request->direc1)   )? $request->direc1  : '';
        $cliente->direc2     = (isset($request->direc2)   )? $request->direc2  : '';
        $cliente->DescripExt = (isset($request->descrip)  )? $request->descrip : '';
        $cliente->escredito  = $escredito;
        $cliente->LimiteCred = $LimiteCred;
        $cliente->porcIncrementa = (isset($request->porcIncrementa)  )? $request->porcIncrementa : 0;
        $cliente->save();

        $busqueda = $request->codclie;

        $sacliesucursal = Sacliesucursal::where('codclie', $request->codclie)->get();
        if(isset($sacliesucursal) and count($sacliesucursal) > 0)
            foreach ($sacliesucursal as $item){
                $item->delete();
            }

        return redirect()->route('buscarclientes', [$busqueda]);
    }

    public function json()
    {
        $user     = User::where('id',auth()->user()->id)->with(['sucursales.sucursal.sacliesucursales.cliente'])->first();
        $clientes = $sucursales = $aux = $all = [];
        foreach ($user->sucursales as $rel){
            array_push($sucursales, $rel->sucursal);
            if(isset($rel->sucursal->sacliesucursales)){
                foreach ($rel->sucursal->sacliesucursales as $relcliente){
                    array_push($clientes,$relcliente->cliente);
                }
            }
        }

        foreach ($clientes as $item){
            list($fecha,$hora) = explode(" ",$item->created_at);
            list($y,$m,$d) = explode("-",$fecha);
            $aux = [
                "id"        => "$item->id",
                "codclie"   => "$item->codclie",
                "descrip"   => "$item->descrip",
                "telef"     => "$item->telef"." "."$item->movil",
                "date"      => "$y-$m-$d",
                "datelabel" => "$d/$m/$y"
            ];

            array_push($all, $aux);
        }
        return response()->json($all);

    }

    public function sacliesucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $clientes = $request->clientes;
        $clientes = json_decode($clientes);

        if (isset($clientes))
            foreach ($clientes as $cliente){
                $aux = Sacliesucursal::where(['codclie' => $cliente->codclie, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Sacliesucursal();
                    $rel->codclie     = $cliente->codclie;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $clientes   = $request->clientes;
        $clientes   = json_decode($clientes);

        if(isset($clientes))
            foreach ($clientes as $cliente){
                $aux = Saclie::where(['codclie' => $cliente->codclie])->first();
                if(!$aux){
                    $new = new Saclie();
                    $new->id3           = (isset($cliente->id3))       ?$cliente->id3        : '';
                    $new->fax           = (isset($cliente->fax))       ?$cliente->fax        : '';
                    $new->clase         = (isset($cliente->clase))     ?$cliente->clase      : '';
                    $new->telef         = (isset($cliente->telef))     ?$cliente->telef      : '';
                    $new->movil         = (isset($cliente->movil))     ?$cliente->movil      : '';
                    $new->email         = (isset($cliente->email))     ?$cliente->email      : '';
                    $new->direc1        = (isset($cliente->direc1))    ?$cliente->direc1     : '';
                    $new->direc2        = (isset($cliente->direc2))    ?$cliente->direc2     : '';
                    $new->direc3        = (isset($cliente->direc3))    ?$cliente->direc3     : '';
                    $new->activo        = (isset($cliente->activo))    ?$cliente->activo     : 0;
                    $new->codclie       = $cliente->codclie;
                    $new->tipocli       = (isset($cliente->tipocli))   ?$cliente->tipocli    : 0;
                    $new->TipoID3       = (isset($cliente->TipoID3))   ?$cliente->TipoID3    : 0;
                    $new->descrip       = (isset($cliente->descrip))   ?$cliente->descrip    : '';
                    $new->represent     = (isset($cliente->represent)) ?$cliente->represent  : '';
                    $new->escredito     = (isset($cliente->escredito)) ?$cliente->escredito  : 0;
                    $new->DescripExt    = (isset($cliente->DescripExt))?$cliente->DescripExt : '';
                    $new->LimiteCred    = (isset($cliente->LimiteCred))?$cliente->LimiteCred : 0;
                    $new->Observaciones = (isset($cliente->Observaciones)) ?$cliente->Observaciones   : '';
                    $new->porcIncrementa= (isset($cliente->porcIncrementa))?$cliente->porcIncrementa  : 0;

                    $new->save();

                    $rel              = new Sacliesucursal();
                    $rel->codclie     = $cliente->codclie;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }else{
                    $aux = Sacliesucursal::where(['codclie' => $cliente->codclie, 'fk_sucursal'=>$sucursalid])->first();
                    if(!$aux){
                        $rel              = new Sacliesucursal();
                        $rel->codclie     = $cliente->codclie;
                        $rel->fk_sucursal = $sucursalid;
                        $rel->save();
                    }
                }
            }

        $clientes = Saclie::whereRaw("codclie not in (select codclie from sacliesucursal where fk_sucursal=$sucursalid )")->limit('300')->get();

        $prodfalt = Saprod::whereRaw("codprod not in (select codprod from saprodsucursal where fk_sucursal=$sucursalid )")->limit('30')->get();
        $prodflag = 0;
        if(isset($prodfalt) and count($prodfalt)>0){
            $prodflag = 1;
        }

        $provfalt = Saprov::whereRaw("codprov not in (select codprov from saprovsucursal where fk_sucursal=$sucursalid )")->limit('1')->get();
        $provflag = 0;
        if(isset($provfalt) and count($provfalt)>0){
            $provflag = 1;
        }

        return response()->json(['success'=>'success', 'newclientes' => $clientes, 'prodflag' => $prodflag, 'provflag' => $provflag]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        $LimiteCred  = (isset($request->LimiteCred)) ? $request->LimiteCred : 0;

        $escredito = 0;
        if($LimiteCred > 0){
            $escredito = 1;
        }

        $newCliente = new Saclie();
        $newCliente->fill($request->all());
        $newCliente->DescripExt = $request->descrip;
        $newCliente->escredito  = $escredito;
        $newCliente->save();

        $busqueda = $request->codclie;

        return redirect()->route('buscarclientes', [$busqueda]);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
