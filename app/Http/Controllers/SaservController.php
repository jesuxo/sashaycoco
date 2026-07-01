<?php

namespace App\Http\Controllers;

use App\Models\Saserv;
use App\Models\Saservsucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SaservController extends Controller
{
    public function index()
    {

    }

    public function json()
    {

    }

    public function saservsucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $servicios = $request->servicios;
        $servicios = json_decode($servicios);

        if (isset($servicios))
            foreach ($servicios as $servicio){
                $aux = Saservsucursal::where(['codserv' => $servicio->codserv, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Saservsucursal();
                    $rel->codserv     = $servicio->codserv;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);

        $servicios = Saserv::whereRaw("codserv not in (select codserv from saservsucursal where fk_sucursal=$sucursalid )")->get();

        return response()->json(['success'=>'success', 'newservicios' => $servicios]);
    }

    public function create()
    {

    }

    public function checkcodserv($codserv)
    {
        $check   = 0;
        if($codserv != '')
            $serv = Saserv::where(['codserv' => $codserv])->first();
        if(isset($serv) and $serv->codserv != '')
            $check = 0;
        else
            $check = 1;

        return response()->json(['check' => $check ]);
    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {
        //
    }
}
