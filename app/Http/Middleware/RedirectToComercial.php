<?php
// app/Http/Middleware/RedirectToComercial.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectToComercial
{
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario está autenticado
        if (Auth::check()) {
            $user = Auth::user();

            // Verificar si tiene sucursales asignadas
            if ($user->tieneSucursalesAsignadas()) {
                $comercial = $user->getComercialPrincipal();

                if ($comercial) {
                    // Guardar el comercial en la sesión para uso posterior
                    session(['comercialdata' => $comercial]);

                    // Si está en la raíz o en dashboard, redirigir al comercial
                    if ($request->is('/') || $request->is('dashboard') || $request->is('home')) {
                        return redirect()->route('comercial.dashboard', ['comercialId' => $comercial->id]);
                    }
                } else {
                    // Si tiene sucursales pero no comercial configurado
                    session(['error_comercial' => 'El comercial asociado a tu sucursal no está configurado']);
                }
            } else {
                // Si no tiene sucursales asignadas
                session(['error_sucursales' => 'No tienes sucursales asignadas. Contacta al administrador.']);

                // Si está en la raíz, mostrar vista de error
                if ($request->is('/') || $request->is('dashboard') || $request->is('home')) {
                    return redirect()->route('sin.asignacion');
                }
            }
        }

        return $next($request);
    }
}
