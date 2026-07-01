<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Sobrescribir el método redirectTo después del login
     */
    protected function redirectTo()
    {
        $user = Auth::user();
        if ($user->tieneSucursalesAsignadas()) {
            $comercial = $user->getComercialPrincipal();

            if ($comercial) {
                session(['comercialid' => $comercial->id]);
                session(['comercialdata' => $comercial]);
                return route('index');
            }

            return '/sin-asignacion';
        }

        return '/sin-asignacion';
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
