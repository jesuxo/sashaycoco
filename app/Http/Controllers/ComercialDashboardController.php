<?php
// app/Http/Controllers/ComercialDashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sacomercial;

class ComercialDashboardController extends Controller
{
    public function index($comercialId)
    {
        $user = Auth::user();

        // Verificar que el comercial pertenece a una sucursal del usuario
        $sucursales = $user->sucursales()->where('fk_comercial', $comercialId)->get();

        if ($sucursales->isEmpty()) {
            abort(403, 'No tienes acceso a este comercial');
        }

        $comercial = Sacomercial::findOrFail($comercialId);

        // Guardar el comercial actual en sesión
        session(['comercialdata' => $comercial]);

        return view('comercial.dashboard', compact('comercial', 'sucursales'));
    }

    public function cambiarComercial($comercialId)
    {
        $user = Auth::user();

        // Verificar que el usuario tiene acceso a este comercial
        if (!$user->tieneAccesoAComercial($comercialId)) {
            return redirect()->back()->with('error', 'No tienes acceso a este comercial');
        }

        // Obtener el comercial
        $comercial = Sacomercial::findOrFail($comercialId);

        // Guardar en sesión
        session(['comercialdata' => $comercial]);
        session(['comercialid' => $comercial->id]);

        // Redirigir a la página anterior o al dashboard
        return redirect()->back()->with('success', 'Comercial cambiado a: ' . $comercial->descrip);
    }

    public function getComercialesDisponibles()
    {
        $user = Auth::user();
        $comerciales = $user->getComercialesAcceso();

        return response()->json($comerciales);
    }
}
