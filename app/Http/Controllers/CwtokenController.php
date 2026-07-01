<?php

namespace App\Http\Controllers;

use App\Models\Cwtoken;
use App\Models\Cwtokenfailed;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CwtokenController extends Controller
{
    // Generar token automático
    private function generarTokenAleatorio()
    {
        return strtoupper(Str::random(8) . '-' . rand(100, 999));
    }

    // Validar formato de token
    private function validarTokenFormato($token)
    {
        // Permitir letras mayúsculas, números, guiones y guiones bajos
        return preg_match('/^[A-Z0-9_-]+$/', $token);
    }

    public function reportetokens(Request $request)
    {
        if (!Auth::user() || !auth()->user()->can('menu_token')) {
            return redirect()->to('index');
        }

        $busquedatoken = $request->busquedatoken ?? '';
        $fksucursal = $request->fksucursal ?? '';
        $estado = $request->estado ?? 'todos';

        $tokens = Cwtoken::with('sucursal')
            ->orderByDesc('id');

        // Filtro por búsqueda
        if ($busquedatoken) {
            $busquedatoken = str_replace('*', ' ', $busquedatoken);
            $vector = explode(" ", $busquedatoken);
            $tokens->where(function($q) use ($vector) {
                foreach ($vector as $item) {
                    $q->where('token', 'like', "%$item%")
                        ->orWhere('codusua', 'like', "%$item%")
                        ->orWhere('obs', 'like', "%$item%");
                }
            });
        }

        // Filtro por sucursal
        if ($fksucursal) {
            $tokens->where('fksucursal', $fksucursal);
        }

        // Filtro por estado
        if ($estado === 'pendientes') {
            $tokens->pendientes();
        } elseif ($estado === 'usados') {
            $tokens->usados();
        }

        $tokens = $tokens->paginate(50);

        // Estadísticas
        $pendientes = Cwtoken::pendientes()->count();
        $usados = Cwtoken::usados()->count();

        $sucursales = Sasucursal::where('fk_comercial', session('comercialid', 1))->get();

        return view('reporteTokens', compact(
            'tokens', 'busquedatoken', 'fksucursal', 'estado',
            'sucursales', 'pendientes', 'usados'
        ));
    }

    public function store(Request $request)
    {
        if (!Auth::user() || !auth()->user()->can('menu_token')) {
            return redirect()->route('reportetokens')->with('error', 'No autorizado');
        }

        $token = trim(str_replace(' ', '', $request->token ?? ''));

        // Validaciones
        if (empty($token)) {
            return redirect()->route('reportetokens')->with('error', 'El token no puede estar vacío');
        }

        if (!$this->validarTokenFormato($token)) {
            return redirect()->route('reportetokens')->with('error', 'El token solo puede contener letras mayúsculas, números, guiones y guiones bajos');
        }

        $exists = Cwtoken::where('token', $token)->exists();
        if ($exists) {
            return redirect()->route('reportetokens')->with('error', 'Este token ya existe');
        }

        try {
            $new = new Cwtoken();
            $new->token = strtoupper($token);
            $new->obs = $request->obs ?? '';
            $new->fksucursal = $request->fksucursal ?? 0;
            $new->save();

            return redirect()->route('reportetokens')->with('success', 'Token creado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('reportetokens')->with('error', 'Error al crear el token');
        }
    }

    public function generarTokenAuto(Request $request)
    {
        if (!Auth::user() || !auth()->user()->can('menu_token')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $token = $this->generarTokenAleatorio();

        try {
            $new = new Cwtoken();
            $new->token = $token;
            $new->obs = $request->obs ?? '';
            $new->fksucursal = $request->fksucursal ?? 0;
            $new->save();

            return response()->json([
                'success' => true,
                'token' => $token,
                'id' => $new->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function apiCheck(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal ?? '');
        $token = $request->token ?? '';
        $codusua = $request->codusua ?? '';

        if (empty($token)) {
            return response()->json(['error' => 'Token requerido'], 400);
        }

        $aux = Cwtoken::where('token', $token)
            ->where('status', 0)
            ->first();

        if (!$aux) {
            // Buscar si existe pero ya usado
            $usado = Cwtoken::where('token', $token)->where('status', 1)->first();
            if ($usado) {
                $this->registrarIntentoFallido($token, $codusua, 'token_ya_usado');
                return response()->json(['error' => 'Token ya utilizado']);
            }

            $this->registrarIntentoFallido($token, $codusua, 'token_no_encontrado');
            return response()->json(['error' => 'Token inválido']);
        }

        // Verificar si ha pasado más de 7 días
        $creado = Carbon::parse($aux->created_at);
        if ($creado->diffInDays(Carbon::now()) > 7) {
            return response()->json(['error' => 'Token expirado (más de 7 días)']);
        }

        try {
            $aux->status = 1;
            $aux->codusua = $codusua;
            $aux->save();
            return response()->json(['success' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar el token'], 500);
        }
    }

    private function registrarIntentoFallido($token, $codusua, $motivo)
    {
        try {
            $aux = new Cwtokenfailed();
            $aux->tokenfailed = $token;
            $aux->codusua = $codusua;
            $aux->save();
        } catch (\Exception $e) {
            // Silently fail
        }
    }

    public function newtoken(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal ?? '');
        $obs        = $request->obs ?? '';
        $codusua    = $request->codusua ?? '';

        try {
            $aux = new Cwtoken();
            $aux->token      = $this->generarTokenAleatorio();
            $aux->codusua    = strtoupper($codusua);
            $aux->obs        = $obs;
            $aux->fksucursal = $sucursalid;
            $aux->save();

            return response()->json(['success'=>'success']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function tokenupdate(Request $request)
    {
        if (!Auth::user() || !auth()->user()->can('menu_token')) {
            return redirect()->route('reportetokens')->with('error', 'No autorizado');
        }

        $token = trim(str_replace(' ', '', $request->token ?? ''));
        $tokenid = $request->tokenid ?? 0;

        if (empty($token)) {
            return redirect()->route('reportetokens')->with('error', 'El token no puede estar vacío');
        }

        if (!$this->validarTokenFormato($token)) {
            return redirect()->route('reportetokens')->with('error', 'Formato de token inválido');
        }

        // Verificar que no exista otro token con el mismo valor
        $exists = Cwtoken::where('token', $token)->where('id', '!=', $tokenid)->exists();
        if ($exists) {
            return redirect()->route('reportetokens')->with('error', 'Ya existe un token con ese valor');
        }

        try {
            $tokenobj = Cwtoken::findOrFail($tokenid);
            $tokenobj->token = strtoupper($token);
            $tokenobj->obs = $request->obs ?? $tokenobj->obs;
            $tokenobj->save();

            return redirect()->route('reportetokens')->with('success', 'Token actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('reportetokens')->with('error', 'Error al actualizar el token');
        }
    }

    public function destroy($id)
    {
        if (!Auth::user() || !auth()->user()->can('menu_token_eliminar')) {
            return response()->json(['deleted' => 0]);
        }

        try {
            $token = Cwtoken::findOrFail($id);
            $token->delete();
            return response()->json(['deleted' => 1]);
        } catch (\Exception $e) {
            return response()->json(['deleted' => 0]);
        }
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user() || !auth()->user()->can('menu_token_eliminar')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['error' => 'No se seleccionaron tokens'], 400);
        }

        try {
            Cwtoken::whereIn('id', $ids)->delete();
            return response()->json(['deleted' => count($ids)]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        if (!Auth::user() || !auth()->user()->can('menu_token')) {
            return redirect()->to('index');
        }

        $tokens = Cwtoken::with('sucursal')
            ->when($request->estado === 'pendientes', function($q) {
                $q->pendientes();
            })
            ->when($request->estado === 'usados', function($q) {
                $q->usados();
            })
            ->when($request->fksucursal, function($q) use ($request) {
                $q->where('fksucursal', $request->fksucursal);
            })
            ->get();

        $filename = 'tokens_' . Carbon::now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($tokens) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Token', 'Estado', 'Usuario', 'Sucursal', 'Observación', 'Creado']);

            foreach ($tokens as $token) {
                fputcsv($file, [
                    $token->id,
                    $token->token,
                    $token->status_text,
                    $token->codusua ?? '-',
                    $token->sucursal->descrip ?? '-',
                    $token->obs ?? '-',
                    $token->created_at ? Carbon::parse($token->created_at)->format('d/m/Y H:i') : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
