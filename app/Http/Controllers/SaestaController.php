<?php

namespace App\Http\Controllers;

use App\Models\Saesta;
use Illuminate\Http\Request;

class SaestaController extends Controller
{
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
        $sucursalid = str_replace("300","",$request->sucursal);
        $estacion = $request->estacion;
        $estacion = json_decode($estacion);
        $estacion = $estacion[0];

         try{
            $data = new Saesta();
            $data->codesta     = $estacion->codesta;
            $data->descrip     = $estacion->descrip;
            $data->cobranza    = $estacion->cobranza;
            $data->fk_sucursal = $sucursalid;
            $data->facturacion = $estacion->facturacion;

            $data->save();

        }  catch (\Exception $e){
            return response()->json(['error' => 'error'], 304);
        }

        return response()->json(['success'=>'success'], 200);

    }

    public function show(Saesta $saesta)
    {
        //
    }

    public function edit(Saesta $saesta)
    {
        //
    }

    public function update(Request $request, Saesta $saesta)
    {
        //
    }

    public function destroy(Saesta $saesta)
    {
        //
    }
}
