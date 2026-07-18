<?php

namespace App\Http\Controllers;

use App\Models\NewSaexis;
use App\Models\Saprod;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class NewSaexisController extends Controller
{
    public function newexistencias(Request $request)
    {
        $productos = $request->productos;
        $productos = json_decode($productos);

        try{
            if(isset($productos)){
                foreach ($productos as $prd){

                    if(isset($prd->codprod)){
                        $existen = NewSaexis::where(['codprod'=>  $prd->codprod, 'codubic'=> $prd->codubic, 'fk_sucursal'=> $prd->fk_sucursal])->first();

                        if(isset($existen->id)){
                            $existen->existen = $prd->existen;
                            $existen->save();
                        }else{
                            $existen = new NewSaexis();
                            $existen->existen = $prd->existen;
                            $existen->codprod = $prd->codprod;
                            $existen->codubic = $prd->codubic;
                            $existen->fk_sucursal = $prd->fk_sucursal;
                            $existen->save();
                        }
                        $sucursal   = Sasucursal::find($prd->fk_sucursal);
                        $comercial  = $sucursal->fk_comercial;

                        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
                        $auxsucu    = [];

                        foreach ($allsucursa as $sucu){
                            array_push( $auxsucu, $sucu->id);
                        }
                        $auxsucu = implode(',' , $auxsucu);

                        $existencias = NewSaexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='".$prd->codprod."' and existen <> 0")
                            ->orderBy('codubic')->get();

                        $sumaexisten = 0;
                        foreach ($existencias as $existencia){
                            $sumaexisten += $existencia->existen;
                        }

                        $saprod   = Saprod::where(['codprod'=>  $prd->codprod, 'comercial'=> $comercial])->first();

                        if(isset($saprod) and isset($saprod->codprod)){
                            $saprod->newexisten = $sumaexisten;
                            $saprod->save();
                        }else{

                        }
                    }
                }
            }

            return response()->json(['success' => 'success', 'updated' => 1], 200);
        }catch (\Exception $e){

            return response()->json(['error' => 'error'], 304);
        }
    }
}
