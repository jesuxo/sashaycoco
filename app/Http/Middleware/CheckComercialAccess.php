<?php
// app/Http/Middleware/CheckComercialAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckComercialAccess
{
    public function handle(Request $request, Closure $next)
    {
        $comercialId = $request->route('comercialId');
        $user = Auth::user();

        // Verificar que el usuario tiene acceso a este comercial
        $tieneAcceso = $user->sucursales()
            ->where('fk_comercial', $comercialId)
            ->exists();

        if (!$tieneAcceso) {
            abort(403, 'No tienes acceso a este comercial');
        }

        return $next($request);
    }
}
