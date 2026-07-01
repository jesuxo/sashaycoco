<?php

namespace App\Http\Controllers;

use App\Models\Sadepo;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SadepoController extends Controller
{
    public function index()
    {
        $comercialid = session('comercialid');
        $depositos  = Sadepo::where('comercial',$comercialid)->get();
        return view('depositos-list-view', compact('depositos') );
    }

    public function list(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;

        $depositos = Sadepo::where('comercial',$comercialid)->orderBy('codubic','desc')->get();
        return response()->json(['success'=>'success', 'depositos' => $depositos], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $comercialid = session('comercialid');
        $deposito = Sadepo::where(['codubic' => $request->codubic, 'comercial' => $comercialid])->first();

        if(!isset($deposito->codubic)){
            $newDepo = new Sadepo();
            $newDepo->fill($request->all());
            $newDepo->exhibicion = ($request->exhibicion)? 1: 0;
            $newDepo->servicio   = ($request->servicio)? 1: 0;
            $newDepo->venta      = ($request->venta)? 1: 0;
            $newDepo->activo     = 1;
            $newDepo->comercial  = $comercialid;
            $newDepo->save();
            return response()->json(['success'=>'success' ]);
        }else{
            return response()->json(['error'=>'error' ]);
        }

    }

    public function json()
    {
        $comercialid = session('comercialid');
        $all = Sadepo::where('comercial', $comercialid)->orderBy('descrip', 'asc')->get();
        $depositos = [];

        foreach ($all as $item) {
            $depositos[] = [
                "id"         => $item->id,
                "codubic"    => $item->codubic,
                "descrip"    => $item->descrip,
                "exhibicion" => $item->exhibicion == 1 ? "1" : "0",
                "venta"      => $item->venta      == 1 ? "1" : "0",
                "servicio"   => $item->servicio   == 1 ? "1" : "0",
                "activo"     => $item->activo ? "Active" : "Inactive"
            ];
        }

        return response()->json($depositos);
    }

    public function show(Sadepo $sadepo)
    {
        //
    }

    public function edit(Sadepo $sadepo)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $deposito = Sadepo::find($id);
        $deposito->descrip    = $request->descrip;
        $deposito->exhibicion = ($request->exhibicion == 1 )? 1: 0;
        $deposito->venta      = ($request->venta      == 1 )? 1: 0;
        $deposito->servicio   = ($request->servicio   == 1 )? 1: 0;
        $deposito->activo     = ($request->activo=='Active')? 1: 0;
        $deposito->save();

        response()->json(['success'=>'success',"actializado"=>111]);
    }

    public function destroy($id)
    {
        $deposito = Sadepo::find($id);
        $deposito->delete();
        response()->json(['success'=>'success']);
    }
}
