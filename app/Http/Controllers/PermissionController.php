<?php
namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    // Mostrar formulario
    public function showForm($id = null)
    {
        $users = User::all();
        $permissions = Permission::all();
        $user  = '';
        if(isset($id) and $id > 0)
            $user = User::find($id);

        return view('permissions.assign', compact('user', 'id', 'users', 'permissions'));
    }

    // Asignar permisos
    public function assign(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'required|array',
        ]);

        $user = User::find($request->user_id);
        $user->syncPermissions($request->permissions); // Sincroniza permisos (elimina los anteriores)

        return redirect()->route('permissions.assign', $user->id)
            ->with('success', 'Permisos asignados correctamente a ' . $user->email);
    }

    // Crear nuevo permiso
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name, 'guard_name' => 'web']);

        return back()->with('success', 'Permiso creado exitosamente!');
    }

    public function revokePermission(User $user, $permiso)
    {
        $user->revokePermissionTo($permiso);

        return back()->with('success', 'Permiso revocado correctamente');
    }
}
