<?php

namespace App\Http\Controllers;

use App\Models\Sainsta;
use App\Models\Saprod;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SainstaController extends Controller
{
    public function index()
    {
        $comercial = session('comercialid');

        $instanciaspadre = Sainsta::where('comercial', $comercial)
            ->selectRaw("CONCAT(repeat('&nbsp;', (nivel)*4), ' ', descrip) as label, descrip, id, nivel")
            ->orderBy('codalte')
            ->get();

        return view('sub-categories', compact('instanciaspadre'));
    }

    public function json()
    {
        $comercial = session('comercialid');

        // El global scope ya filtra por comercial automáticamente
        $all = Sainsta::with(['padre', 'hijos', 'productos', 'servicios'])
            ->orderBy('codalte', 'asc')
            ->get();

        $instancias = [];
        foreach ($all as $item) {
            $instancias[] = [
                "id" => $item->id,
                "subcategory" => $item->descrip,
                "category" => $item->padre ? $item->padre->descrip : '',
                "desseri" => $item->desseri ?? 0,
                "nivel" => $item->nivel,
                "hijos" => $item->hijos()->count() > 0,
                "productos" => $item->productos()->count() > 0,
                "servicios" => $item->servicios()->count() > 0
            ];
        }

        return response()->json($instancias);
    }

    public function store(Request $request)
    {
        try {
            $comercial = session('comercialid');

            if (!$comercial) {
                return response()->json(['error' => 'No hay una comercial activa'], 400);
            }

            $insPadre = $request->insPadre;
            $nivel = 1;
            $padreid = 0;
            $codalte = '';

            if ($insPadre and $insPadre != '0' and $insPadre != 'Seleccione') {
                $padre = Sainsta::where('comercial', $comercial)
                    ->where('descrip', $insPadre)
                    ->first();

                if ($padre) {
                    $nivel = $padre->nivel + 1;
                    $padreid = $padre->codinst;
                    $codalte = $padre->codalte;
                }
            }

            // Verificar si ya existe una instancia con el mismo nombre en esta comercial
            $existe = Sainsta::where('comercial', $comercial)
                ->where('descrip', strtoupper($request->descrip))
                ->exists();

            if ($existe) {
                return response()->json(['error' => 'Ya existe una instancia con esta descripción'], 400);
            }

            $new = new Sainsta();
            $new->descrip = strtoupper($request->descrip);
            $new->insPadre = $padreid;
            $new->nivel = $nivel;
            $new->comercial = $comercial;
            $new->desseri = $request->desseri ?? 0;
            $new->codinst = 0;
            $new->codalte = '';
            $new->save();

            $new->codinst = $new->id;
            if (!$codalte) {
                $new->codalte = (string) $new->id;
            } else {
                $new->codalte = $codalte . $new->id;
            }
            $new->save();

            return response()->json([
                'success' => true,
                'id' => $new->id,
                'subcategory' => $new->descrip,
                'category' => $insPadre ?? '',
                'desseri' => $new->desseri,
                'nivel' => $new->nivel
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $comercial = session('comercialid');

            if (!$comercial) {
                return response()->json(['error' => 'No hay una comercial activa'], 400);
            }

            $sainsta = Sainsta::where('comercial', $comercial)->find($id);

            if (!$sainsta) {
                return response()->json(['error' => 'Instancia no encontrada'], 404);
            }

            $insPadre = $request->insPadre;
            $codaltepadre = '';
            $padreid = 0;
            $nivel = 1;

            if ($insPadre and $insPadre != '0' and $insPadre != 'Seleccione') {
                $padre = Sainsta::where('comercial', $comercial)
                    ->where('descrip', $insPadre)
                    ->first();

                if ($padre) {
                    // Evitar que se pueda asignar como padre a sí mismo o a un descendiente
                    if ($padre->id == $id) {
                        return response()->json(['error' => 'No se puede asignar la misma instancia como padre'], 400);
                    }

                    $nivel = $padre->nivel + 1;
                    $padreid = $padre->codinst;
                    $codaltepadre = $padre->codalte;
                }
            }

            // Verificar si ya existe otra instancia con el mismo nombre
            $existe = Sainsta::where('comercial', $comercial)
                ->where('descrip', strtoupper($request->descrip))
                ->where('id', '!=', $id)
                ->exists();

            if ($existe) {
                return response()->json(['error' => 'Ya existe una instancia con esta descripción'], 400);
            }

            $sainsta->descrip = strtoupper($request->descrip);
            $sainsta->insPadre = $padreid;
            $sainsta->nivel = $nivel;
            $sainsta->desseri = $request->desseri ?? 0;

            if ($codaltepadre) {
                $sainsta->codalte = $codaltepadre . $sainsta->id;
            }

            $sainsta->save();

            return response()->json(['success' => true, 'id' => $sainsta->id]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $comercial = session('comercialid');

            if (!$comercial) {
                return response()->json(['error' => 'No hay una comercial activa'], 400);
            }

            $sainsta = Sainsta::where('comercial', $comercial)->find($id);

            if (!$sainsta) {
                return response()->json(['error' => 'Instancia no encontrada'], 404);
            }

            // Verificar si tiene hijos (excluyendo otras comerciales)
            if ($sainsta->hijos()->count() > 0) {
                return response()->json([
                    'error' => 'No se puede eliminar porque tiene instancias hijas asociadas'
                ], 400);
            }

            // Verificar si tiene productos (solo de esta comercial)
            if ($sainsta->productos()->count() > 0) {
                return response()->json([
                    'error' => 'No se puede eliminar porque tiene productos asociados'
                ], 400);
            }

            // Verificar si tiene servicios (solo de esta comercial)
            if ($sainsta->servicios()->count() > 0) {
                return response()->json([
                    'error' => 'No se puede eliminar porque tiene servicios asociados'
                ], 400);
            }

            $sainsta->delete();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }

    public function lastprod($codinst)
    {
        $comercial = session('comercialid');

        $product = Saprod::where('comercial', $comercial)
            ->orderBy('id', 'desc')
            ->first();

        $last = isset($product) and $product->codprod != '' ? $product->codprod : '';

        return response()->json(['last' => $last]);
    }

    public function list(Request $request)
    {
        $comercial = $request->comercial;
        $comercial = str_replace("4000", "", $comercial);

        if (!$comercial || $comercial == 0) {
            $fk_sucursal = $request->sucursal;
            $fk_sucursal = str_replace("300", "", $fk_sucursal);
            $sucursal = Sasucursal::find($fk_sucursal);
            $comercial = $sucursal ? $sucursal->fk_comercial : null;
        }

        if (!$comercial) {
            return response()->json(['error' => 'No se pudo determinar la comercial'], 400);
        }

        $instancias = Sainsta::where('comercial', $comercial)
            ->orderBy('codinst', 'desc')
            ->get();

        return response()->json(['success' => 'success', 'instancias' => $instancias], 200);
    }

    public function listComercial(Request $request)
    {
        $comercialid = str_replace("700", "", $request->comercial);

        if (!$comercialid) {
            return response()->json(['error' => 'Comercial no válida'], 400);
        }

        $instancias = Sainsta::where('comercial', $comercialid)
            ->orderBy('codinst', 'desc')
            ->get();

        return response()->json(['success' => 'success', 'instancias' => $instancias], 200);
    }
}
