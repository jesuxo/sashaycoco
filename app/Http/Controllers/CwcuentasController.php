<?php

namespace App\Http\Controllers;

use App\Models\Cwbancos;
use App\Models\Cwcuentas;
use App\Models\Cwcuentasucursal;
use Illuminate\Http\Request;

class CwcuentasController extends Controller
{
    public function index()
    {

    }

    public function json()
    {

    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $cuentas   = $request->cuentas;
        $cuentas   = json_decode($cuentas);

        if(isset($cuentas))
            foreach ($cuentas as $cuenta){
                $aux = Cwcuentas::where(['id' => $cuenta->id])->first();
                if(!$aux){
                    $new = new Cwcuentas();
                    $new->id           = ($cuenta->id)           ?$cuenta->id        : '';
                    $new->numero       = ($cuenta->numero   !='')?$cuenta->numero    : '';
                    $new->descrip      = ($cuenta->descrip  !='')?$cuenta->descrip   : '';
                    $new->nivel        = ($cuenta->nivel     > 0)?$cuenta->nivel     : 0;
                    $new->numpadre     = ($cuenta->numpadre !='')?$cuenta->numpadre  : '';
                    $new->detalle      = ($cuenta->detalle   > 0)?$cuenta->detalle   : 0;
                    $new->banco        = ($cuenta->banco     > 0)?$cuenta->banco     : 0;
                    $new->noeliminar   = ($cuenta->noeliminar> 0)?$cuenta->noeliminar: 0;
                    $new->save();

                    if($cuenta->banco > 0){
                        $newb = new Cwbancos();

                        $newb->id           =  $cuenta->datosbanco->id;
                        $newb->descrip      = ($cuenta->datosbanco->descrip  !='')?$cuenta->datosbanco->descrip   : '';
                        $newb->fksucursal   =  $sucursalid;
                        $newb->fk_cuenta    =  $cuenta->id;
                        $newb->recibetransf = ($cuenta->datosbanco->recibetransf !='')?$cuenta->datosbanco->recibetransf : '';
                        $newb->telefono     = ($cuenta->datosbanco->telefono     !='')?$cuenta->datosbanco->telefono     : '';
                        $newb->abrev        = ($cuenta->datosbanco->abrev        !='')?$cuenta->datosbanco->abrev        : '';
                        $newb->sbs          = ($cuenta->datosbanco->sbs       > 0)?$cuenta->datosbanco->sbs       : 0;
                        $newb->sdolares     = ($cuenta->datosbanco->sdolares  > 0)?$cuenta->datosbanco->sdolares  : 0;
                        $newb->seuros       = ($cuenta->datosbanco->seuros    > 0)?$cuenta->datosbanco->seuros    : 0;
                        $newb->spesos       = ($cuenta->datosbanco->spesos    > 0)?$cuenta->datosbanco->spesos    : 0;
                        $newb->save();
                    }

                    $rel              = new Cwcuentasucursal();
                    $rel->fk_cuenta   = $cuenta->id;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }else{

                    $aux->descrip      = ($cuenta->descrip  !='')?$cuenta->descrip   : '';
                    $aux->save();

                    if($aux->banco){
                        $banco = Cwbancos::where(['fksucursal'=>$sucursalid, 'fk_cuenta'=>$aux->id])->first();
                        $banco->descrip = $cuenta->descrip;
                        $banco->save();
                    }

                    $aux = Cwcuentasucursal::where(['fk_cuenta' => $cuenta->id, 'fk_sucursal'=>$sucursalid])->first();
                    if(!$aux){
                        $rel              = new Cwcuentasucursal();
                        $rel->fk_cuenta   = $cuenta->id;
                        $rel->fk_sucursal = $sucursalid;
                        $rel->save();
                    }
                }
            }

        $cuentas = Cwcuentas::whereRaw("id not in (select fk_cuenta from cwcuentasucursal where fk_sucursal=$sucursalid )")->get();

        return response()->json(['success'=>'success', 'cuentas' => $cuentas]);
    }

    public function cwcuentasucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $cuentas = $request->cuentas;
        $cuentas = json_decode($cuentas);

        if (isset($cuentas))
            foreach ($cuentas as $cuenta){
                $aux = Cwcuentasucursal::where(['fk_cuenta' => $cuenta->id, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Cwcuentasucursal();
                    $rel->fk_cuenta   = $cuenta->id;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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
    }
}
