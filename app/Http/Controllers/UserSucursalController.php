<?php
// app/Http/Controllers/UserSucursalController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sasucursal;
use App\Models\Usersucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserSucursalController extends Controller
{
    public function index()
    {
        if(Auth::user()->id != 1)
            return response()->redirectTo('/');
        $usuarios = User::all();
        $sucursales = Sasucursal::with('comercial')->orderBy('fk_comercial','asc')->get();
        return view('usersucursal.index', compact('usuarios', 'sucursales'));
    }

    public function getUsersConSucursales()
    {
        $usuarios = User::with('sucursales.comercial')->get();
        return response()->json($usuarios);
    }

    public function getAllSucursales()
    {
        $sucursales =  Sasucursal::with('comercial')->orderBy('fk_comercial','asc')->get();
        return response()->json($sucursales);
    }

    public function getSucursalesAsignadasPorUsuario($userId)
    {
        $usuario = User::with('sucursales.comercial')->find($userId);
        $sucursales = $usuario->sucursales;
        return view('usersucursal.partials.sucursales-asignadas', compact('sucursales', 'userId'));
    }

    public function getUsuariosPorSucursal($sucursalId)
    {
        $sucursal = Sasucursal::with('users')->find($sucursalId);
        $usuarios = $sucursal->users;
        //$sucursalId = $sucursalId; // Para pasarlo a la vista
        return view('usersucursal.partials.usuarios-por-sucursal', compact('usuarios', 'sucursalId'));
    }

    public function asignarSucursal(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'sucursal_id' => 'required|exists:sasucursal,id'
        ]);

        $exists = Usersucursal::where('fk_user', $request->user_id)
            ->where('fk_sucursal', $request->sucursal_id)
            ->exists();

        if (!$exists) {
            Usersucursal::create([
                'fk_user' => $request->user_id,
                'fk_sucursal' => $request->sucursal_id
            ]);
            return response()->json(['success' => true, 'message' => 'Sucursal asignada correctamente']);
        }

        return response()->json(['success' => false, 'message' => 'La sucursal ya está asignada a este usuario']);
    }

    public function quitarSucursal(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'sucursal_id' => 'required|exists:sasucursal,id'
        ]);

        Usersucursal::where('fk_user', $request->user_id)
            ->where('fk_sucursal', $request->sucursal_id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Sucursal removida correctamente']);
    }
}
