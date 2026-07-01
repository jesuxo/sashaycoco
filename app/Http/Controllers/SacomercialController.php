<?php

namespace App\Http\Controllers;

use App\Models\Sacomercial;
use Illuminate\Http\Request;

class SacomercialController extends Controller
{
    public function index()
    {

    }

    public function list(Request $request)
    {
        $comerciales = Sacomercial::orderBy('id','asc')->get();
        return response()->json(['success'=>'success', 'comerciales' => $comerciales], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
         //
    }

    public function json()
    {
        $all = Sacomercial::orderBy('id','asc')->get();
        $comerciales = [];
        foreach ($all as $item){
            $aux = [
                "id"            => "$item->id",
                "descrip"       => "$item->descrip",
                "short"         => "$item->short"
            ];
            array_push($comerciales,$aux);
        }
        return response()->json($comerciales );
    }

    public function show(Sacomercial $sacomercial)
    {
        //
    }

    public function edit(Sacomercial $sacomercial)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $comercial = Sacomercial::find($id);
        $comercial->delete();
        response()->json(['success'=>'success']);
    }
}
