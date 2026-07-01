<?php

namespace App\Http\Controllers;

use App\Models\Saitemopi;
use App\Models\Saopei;
use App\Models\Saprod;
use App\Models\Saprodsucursal;
use App\Models\Sasepropi;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SaopeiController extends Controller
{

    public function documento(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $operaciones = $request->operaciones;
        $operaciones = json_decode($operaciones);

        $sucursal      = Sasucursal::find($sucursalid);
        $allsucursales = Sasucursal::where("fk_comercial", $sucursal->fk_comercial)->get();

        if(isset($operaciones)){
            foreach ($operaciones as $ope){

                if(isset($ope->nrounico)){
                    $record = Saopei::where(['nrounico'=>  $ope->nrounico, 'fk_sucursal'=> $sucursalid])->first();

                    if(!isset($record->id)){
                        $record = new Saopei();

                        if( isset($ope->allitems)){
                            foreach ($ope->allitems as $allitem){
                                $newitem = new Saitemopi();
                                $auxitem = (array) $allitem;
                                $newitem->fill($auxitem);
                                $newitem->tipoopi = $ope->tipoopi;
                                $newitem->fk_sucursal = $sucursalid ;
                                $newitem->save();

                                $saprod   = Saprod::where(['codprod'=>  $allitem->CodItem, 'comercial'=> $sucursal->fk_comercial])->first();
                                if(isset($saprod) and isset($saprod->codprod) and isset($allitem->preciodpro) and $allitem->preciodpro >0){
                                    $saprod->preciod    = (isset($allitem->preciod))?   $allitem->preciod   : 0;
                                    $saprod->preciodant = (isset($allitem->preciodant))?$allitem->preciodant: 0;
                                    $saprod->preciodpro = (isset($allitem->preciodpro))?$allitem->preciodpro: 0;
                                    $saprod->save();
                                }
                                foreach($allsucursales as $current){
                                    $sucursalprods = Saprodsucursal::where(['codprod' => $allitem->CodItem, 'fk_sucursal' => $current->id])->get();
                                    foreach ($sucursalprods as $sucursalprod) {
                                        $sucursalprod->delete();
                                    }
                                }

                            }
                        }

                        if( isset($ope->seriales)){
                            foreach ($ope->seriales as $serial){
                                $newseri = new Sasepropi();
                                $auxitem = (array) $serial;
                                $newseri->fill($auxitem);
                                $newseri->tipoopi = $ope->tipoopi;
                                $newseri->fk_sucursal = $sucursalid ;
                                $newseri->save();
                            }
                        }
                    }else{
                        $record = Saopei::find($record->id);
                    }

                    $aux = (array) $ope;
                    $record->fill($aux) ;
                    $record->fk_sucursal = $sucursalid ;
                    $record->tipoopi = $ope->tipoopi;
                    $record->save();
                }
            }
        }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

    public function descargado(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $idoperacio = $request->idoperacio;

        $saopei = Saopei::find($idoperacio);
        if(isset($saopei)){
            $saopei->descargar = 0;
            $saopei->save();
        }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

    public function descargar(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);

        $depositos  = explode(',', $request->depositos);

        $auxsaopei['saopei']    = [];
        $auxsaopei['saitemopi'] = [];
        $auxsaopei['sasepropi'] = [];

        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        if(isset($depositos) and count($depositos) > 0)
            foreach ($depositos as $deposito){
                if($deposito != ''){
                    $saopei =  Saopei::selectRaw('id, NumeroD, fk_sucursal, tipoopi,
                                                  UsoInterno, CodUbic2, CodOper, CodEsta, Autori, Respon,
                                                  UsoMat,Notas1,Notas2,Notas3')->whereRaw("codubic2 = '$deposito'
                                                 and descargar = 1
                                                 and fk_sucursal<> $sucursalid
                                                 and fk_sucursal in ($auxsucu)")->first();

                    if($saopei){
                        array_push($auxsaopei['saopei'] ,$saopei);

                        $saitemopi = Saitemopi::where(['numerod'=>$saopei->NumeroD, 'fk_sucursal'=>$saopei->fk_sucursal, 'tipoopi'=>$saopei->tipoopi])->get();

                        if(isset($saitemopi))
                            foreach ($saitemopi as $item){
                                array_push($auxsaopei['saitemopi'],$item);
                            }

                        $sasepropi = Sasepropi::where(['numerod'=>$saopei->NumeroD, 'fk_sucursal'=>$saopei->fk_sucursal, 'tipoopi'=>$saopei->tipoopi])->get();

                        if(isset($sasepropi))
                            foreach ($sasepropi as $item){
                                array_push($auxsaopei['sasepropi'],$item);
                            }
                    }
                }
            }



        return response()->json(['success'=>'success', 'auxsaopei' => $auxsaopei]);
    }
}
