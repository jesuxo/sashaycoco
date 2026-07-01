<?php

namespace App\Http\Controllers;

use App\Models\Saacxcw;
use Illuminate\Http\Request;

class SaacxcwController extends Controller
{
    public function index()
    {
        //
    }

    public function cuentaxcobrar(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $cuentasporcobrar = $request->cuentasporcobrar;
        $cuentasporcobrar = json_decode($cuentasporcobrar);

        if(isset($cuentasporcobrar)){
            foreach ($cuentasporcobrar as $cxc){

                if(isset($cxc->NroUnico)){
                    $record = Saacxcw::where(['NroUnico'=>  $cxc->NroUnico, 'fk_sucursal'=> $sucursalid])->first();

                    if(!isset($record->id))
                        $record = new Saacxcw();

                    $aux = (array) $cxc;
                    $record->fill($aux) ;

                    $record->fk_sucursal = $sucursalid ;

                    $record->save();
                }
            }
        }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Saacxcw $Saacxcw)
    {
        //
    }

    public function edit(Saacxcw $Saacxcw)
    {
        //
    }

    public function update(Request $request, Saacxcw $Saacxcw)
    {
        //
    }

    public function destroy(Saacxcw $Saacxcw)
    {
        //
    }
}
