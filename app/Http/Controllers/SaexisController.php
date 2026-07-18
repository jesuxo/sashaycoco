<?php

namespace App\Http\Controllers;

use App\Models\Newsaexis;
use App\Models\Saprod;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SaexisController extends Controller
{
    public function existencias(Request $request)
    {
        $productos = $request->productos;
        $productos = json_decode($productos);

        try{
            if(isset($productos)){
                foreach ($productos as $prd){

                    if(isset($prd->codprod)){
                        $existen = Newsaexis::where(['codprod'    => $prd->codprod,
                                                  'codubic'    => $prd->codubic,
                                                  'fk_sucursal'=> $prd->fk_sucursal])
                                            ->first();

                        if(isset($existen->id)){
                            $existen->existen = $prd->existen;
                            $existen->save();
                        }else{
                            $existen = new Newsaexis();
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

                        $existencias = Newsaexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='".$prd->codprod."' and existen <> 0")
                                                ->orderBy('codubic')
                                                ->get();

                        $sumaexisten = 0;
                        foreach ($existencias as $existencia){
                            $sumaexisten += $existencia->existen;
                        }

                        $saprod   = Saprod::where(['codprod'=>  $prd->codprod, 'comercial'=> $comercial])->first();

                        if(isset($saprod) and isset($saprod->codprod)){
                            $saprod->existen = $sumaexisten;
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
