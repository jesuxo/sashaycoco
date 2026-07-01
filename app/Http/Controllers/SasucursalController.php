<?php

namespace App\Http\Controllers;

use App\Models\Cwbancos;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SasucursalController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function getBancos(Request $request)
    {
        $fksucursal = $request->fksucursal;

        $bancos = Cwbancos::whereRaw("recibetransf = 1 and  activo = 1 and (bs=1 or dolares = 1 or pesos =1) ")
            ->orderBy('descrip')
            ->get();

        $html = '<select class="form-control" name="bancosucursal" id="bancosucursal" required>';
        $html .= '<option value="">Seleccione un banco</option>';

        foreach ($bancos as $banco) {
            $html .= '<option value="' . $banco->id . '">' . $banco->descrip . '</option>';
        }

        $html .= '</select>';
        $html .= '<div class="invalid-feedback">Debe seleccionar un banco</div>';

        return $html;
    }

    public function bancos(Request $request)
    {
        $bancos = Cwbancos::whereRaw("recibetransf = 1 and  activo = 1 and (bs=1 or dolares = 1 or pesos =1) ")->orderBy('descrip','asc')->get();
        return view('partials.sucursal_banco',compact('bancos'))->render();
    }

    public function store(Request $request)
    {
        $comercial  = $request->comercial;
        $comercial  = str_replace("4000","",$comercial);

        $new = new Sasucursal();
        $new->fill($request->all());
        $new->fk_comercial = $comercial;
        $new->save();
        $lastid = $new->id;
        return response()->json(['id' => $lastid]);
    }

    public function show(Sasucursal $sasucursal)
    {
        //
    }

    public function edit(Sasucursal $sasucursal)
    {
        //
    }

    public function update(Request $request, Sasucursal $sasucursal)
    {
        //
    }

    public function destroy(Sasucursal $sasucursal)
    {
        //
    }
}
