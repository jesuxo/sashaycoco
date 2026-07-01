<?php

namespace App\Http\Controllers;

use App\Models\Cwtransferencia;
use Illuminate\Support\Facades\Storage;

class ImagenController extends Controller
{
    public function transferencia($id)
    {
        $transf = Cwtransferencia::find($id);

        if (!$transf || !$transf->imagen) {
            abort(404);
        }

        $path = storage_path('app/public/transferencias/' . $transf->imagen);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
