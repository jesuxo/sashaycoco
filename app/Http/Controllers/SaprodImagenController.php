<?php

namespace App\Http\Controllers;

use App\Models\Sacomercial;
use App\Models\Saprod;
use App\Models\SaprodImagen;
use App\Models\Saprodsucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class SaprodImagenController extends Controller
{
    // Directorio donde se guardarán las imágenes
    protected $uploadPath = 'uploads/productos';

    /**
     * Obtener todas las imágenes de un producto
     */
    public function getImagenes($codprod)
    {
        $comercial = session('comercialid');

        $producto = Saprod::where('codprod', $codprod)
            ->where('comercial', $comercial)
            ->first();

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $imagenes = $producto->imagenes()->get();

        // Agregar URLs completas
        $imagenes->each(function($img) {
            $img->url = asset($img->ruta);
        });

        $principal = $producto->imagenPrincipal;
        if ($principal) {
            $principal->url = asset($principal->ruta);
        }

        $thumbnail = $producto->thumbnail;
        if ($thumbnail) {
            $thumbnail->url = asset($thumbnail->ruta);
        }

        $icono = $producto->icono;
        if ($icono) {
            $icono->url = asset($icono->ruta);
        }

        return response()->json([
            'success'          => true,
            'imagenes'         => $imagenes,
            'imagen_principal' => $principal,
            'thumbnail'        => $thumbnail,
            'icono'            => $icono
        ]);
    }

    /**
     * Subir una imagen para un producto
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codprod' => 'required|string',
            'imagen'  => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'tipo'    => 'in:principal,secundaria,thumbnail,icono',
            'orden'   => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $comercialid = session('comercialid') ;

        $comercial   = Sacomercial::find($comercialid);
        $match       = $comercial->match;

        $codprod     = $request->codprod;
        $tipo        = $request->tipo ?? 'secundaria';
        $orden       = $request->orden ?? 0;

        $producto = Saprod::where('codprod', $codprod)
            ->where('comercial', $comercialid)
            ->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'error'   => 'Producto no encontrado'
            ], 404);
        }

        // Si es principal, quitar la principal anterior
        if ($tipo === 'principal') {
            SaprodImagen::where('codprod', $codprod)
                ->where('comercial', $comercialid)
                ->where('tipo', 'principal')
                ->update(['activo' => 0]);
        }

        // Si es thumbnail o icono, quitar el anterior del mismo tipo
        if (in_array($tipo, ['thumbnail', 'icono'])) {
            SaprodImagen::where('codprod', $codprod)
                ->where('comercial', $comercialid)
                ->where('tipo', $tipo)
                ->update(['activo' => 0]);
        }

        $file           = $request->file('imagen');
        $nombreOriginal = $file->getClientOriginalName();
        $extension      = strtolower($file->getClientOriginalExtension());

        $nombreArchivo = time() . '_' . uniqid($codprod . '_') . '.' . $extension;

        $path = public_path($this->uploadPath);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $rutaCompleta = $path . '/' . $nombreArchivo;

        $this->comprimirImagenProducto($file->getPathname(), $rutaCompleta, $extension, $tipo);

        $imagenModel = SaprodImagen::create([
            'codprod'         => $codprod,
            'comercial'       => $comercialid,
            'nombre_original' => $nombreOriginal,
            'nombre_archivo'  => $nombreArchivo,
            'ruta'            => $this->uploadPath . '/' . $nombreArchivo,
            'tipo'            => $tipo,
            'orden'           => $orden,
            'activo'          => 1
        ]);

        $prodsucursal = Saprodsucursal::with('producto')->where('codprod', $producto->codprod)->get();
        if($prodsucursal)
            foreach ($prodsucursal as $item){
                if($item->producto->comercial == $match)
                    $item->delete();
            }

        return response()->json([
            'success' => true,
            'message' => 'Imagen subida correctamente',
            'imagen'  => $imagenModel,
            'url'     => asset($this->uploadPath . '/' . $nombreArchivo)
        ]);
    }

    /**
     * Subir múltiples imágenes secundarias
     */
    public function uploadMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codprod'    => 'required|string',
            'imagenes.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $comercialid = session('comercialid') ;

        $comercial   = Sacomercial::find($comercialid);
        $match       = $comercial->match;
        $codprod     = $request->codprod;

        $producto = Saprod::where('codprod', $codprod)
            ->where('comercial', $comercialid)
            ->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'error'   => 'Producto no encontrado'
            ], 404);
        }

        $imagenesSubidas = [];
        $errores = [];

        $path = public_path($this->uploadPath);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        foreach ($request->file('imagenes') as $index => $file) {
            try {
                $nombreOriginal = $file->getClientOriginalName();
                $extension      = strtolower($file->getClientOriginalExtension());
                $nombreArchivo  = time() . '_' . uniqid($codprod . '_') . '_' . $index . '.' . $extension;

                $rutaCompleta   = $path . '/' . $nombreArchivo;

                $this->comprimirImagenProducto($file->getPathname(), $rutaCompleta, $extension, 'secundaria');

                $imagenModel = SaprodImagen::create([
                    'codprod'         => $codprod,
                    'comercial'       => $comercialid,
                    'nombre_original' => $nombreOriginal,
                    'nombre_archivo'  => $nombreArchivo,
                    'ruta'            => $this->uploadPath . '/' . $nombreArchivo,
                    'tipo'            => 'secundaria',
                    'orden'           => $index,
                    'activo'          => 1
                ]);

                $imagenesSubidas[] = $imagenModel;

            } catch (\Exception $e) {
                $errores[] = "Error al procesar {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        $prodsucursal = Saprodsucursal::with('producto')->where('codprod', $producto->codprod)->get();
        if($prodsucursal)
            foreach ($prodsucursal as $item){
                if($item->producto->comercial == $match)
                    $item->delete();
            }

        return response()->json([
            'success'  => count($imagenesSubidas) > 0,
            'message'  => count($imagenesSubidas) . ' imágenes subidas correctamente',
            'imagenes' => $imagenesSubidas,
            'errores'  => $errores
        ]);
    }

    /**
     * Comprimir imagen de producto según el tipo
     */
    private function comprimirImagenProducto($rutaOrigen, $rutaDestino, $extension, $tipo = 'secundaria')
    {
        try {
            $img = Image::make($rutaOrigen);

            $maxWidth = 1200;
            $maxHeight = 1200;
            $calidad = 80;

            switch ($tipo) {
                case 'principal':
                    $maxWidth = 600;
                    $maxHeight = 600;
                    $calidad = 85;
                    break;
                case 'thumbnail':
                    $maxWidth = 300;
                    $maxHeight = 300;
                    $calidad = 75;
                    break;
                case 'icono':
                    $maxWidth = 100;
                    $maxHeight = 100;
                    $calidad = 70;
                    break;
                case 'secundaria':
                    $maxWidth = 800;
                    $maxHeight = 800;
                    $calidad = 80;
                    break;
                default:
                    $maxWidth = 800;
                    $maxHeight = 800;
                    $calidad = 80;
            }

            $anchoOriginal = $img->width();
            $altoOriginal = $img->height();

            if ($anchoOriginal > $maxWidth || $altoOriginal > $maxHeight) {
                $img->resize($maxWidth, $maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $img->save($rutaDestino, $calidad);

            $tamano = filesize($rutaDestino);
            if ($tamano > 500000) {
                $img->save($rutaDestino, $calidad - 10);
            }

        } catch (\Exception $e) {
            \Log::warning('Error al comprimir imagen de producto: ' . $e->getMessage());
            copy($rutaOrigen, $rutaDestino);
        }
    }

    /**
     * ELIMINAR una imagen
     */
    public function destroy($id)
    {
        $comercial = session('comercialid');

        $imagen = SaprodImagen::where('id', $id)
            ->where('comercial', $comercial)
            ->first();

        if (!$imagen) {
            return response()->json([
                'success' => false,
                'error' => 'Imagen no encontrada'
            ], 404);
        }

        $rutaCompleta = public_path($imagen->ruta);
        if (file_exists($rutaCompleta)) {
            unlink($rutaCompleta);
        }

        $imagen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada correctamente'
        ]);
    }

    /**
     * Establecer una imagen como principal
     */
    public function setPrincipal($id)
    {

        $comercialid = session('comercialid') ;

        $comercial   = Sacomercial::find($comercialid);
        $match       = $comercial->match;

        $imagen      = SaprodImagen::where('id', $id)
                        ->where('comercial', $comercialid)
                        ->first();

        if (!$imagen) {
            return response()->json([
                'success' => false,
                'error'   => 'Imagen no encontrada'
            ], 404);
        }

        // Desactivar la principal anterior
        SaprodImagen::where('codprod', $imagen->codprod)
            ->where('comercial', $comercialid)
            ->where('tipo', 'principal')
            ->update(['tipo' => 'secundaria']);

        // Establecer esta como principal
        $imagen->tipo   = 'principal';
        $imagen->activo = 1;
        $imagen->save();

        $prodsucursal = Saprodsucursal::with('producto')->where('codprod', $imagen->codprod)->get();
        if($prodsucursal)
            foreach ($prodsucursal as $item){
                if($item->producto->comercial == $match)
                    $item->delete();
            }

        return response()->json([
            'success' => true,
            'message' => 'Imagen establecida como principal',
            'imagen'  => $imagen
        ]);
    }

    /**
     * Establecer una imagen como thumbnail
     */
    public function setThumbnail($id)
    {
        $comercial = session('comercialid');

        $imagen = SaprodImagen::where('id', $id)
            ->where('comercial', $comercial)
            ->first();

        if (!$imagen) {
            return response()->json([
                'success' => false,
                'error'   => 'Imagen no encontrada'
            ], 404);
        }

        // No puede ser thumbnail si es principal
        if ($imagen->tipo === 'principal') {
            return response()->json([
                'success' => false,
                'error'   => 'La imagen principal no puede ser thumbnail'
            ], 400);
        }

        // Desactivar thumbnail anterior
        SaprodImagen::where('codprod', $imagen->codprod)
            ->where('comercial', $comercial)
            ->where('tipo', 'thumbnail')
            ->update(['tipo' => 'secundaria']);

        $imagen->tipo   = 'thumbnail';
        $imagen->activo = 1;
        $imagen->save();

        return response()->json([
            'success' => true,
            'message' => 'Imagen establecida como thumbnail',
            'imagen'  => $imagen
        ]);
    }

    /**
     * Establecer una imagen como icono
     */
    public function setIcono($id)
    {
        $comercial = session('comercialid');

        $imagen = SaprodImagen::where('id', $id)
            ->where('comercial', $comercial)
            ->first();

        if (!$imagen) {
            return response()->json([
                'success' => false,
                'error'   => 'Imagen no encontrada'
            ], 404);
        }

        // No puede ser icono si es principal
        if ($imagen->tipo === 'principal') {
            return response()->json([
                'success' => false,
                'error'   => 'La imagen principal no puede ser icono'
            ], 400);
        }

        // Desactivar icono anterior
        SaprodImagen::where('codprod', $imagen->codprod)
            ->where('comercial', $comercial)
            ->where('tipo', 'icono')
            ->update(['tipo' => 'secundaria']);

        $imagen->tipo   = 'icono';
        $imagen->activo = 1;
        $imagen->save();

        return response()->json([
            'success' => true,
            'message' => 'Imagen establecida como icono',
            'imagen'  => $imagen
        ]);
    }

    /**
     * ACTUALIZAR TIPO de una imagen (método PUT)
     */
    public function updateTipo(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tipo' => 'required|in:principal,secundaria,thumbnail,icono'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $comercial = session('comercialid');
        $nuevoTipo = $request->tipo;

        $imagen = SaprodImagen::where('id', $id)
            ->where('comercial', $comercial)
            ->first();

        if (!$imagen) {
            return response()->json([
                'success' => false,
                'error'   => 'Imagen no encontrada'
            ], 404);
        }

        // Si es principal, desactivar la principal anterior
        if ($nuevoTipo === 'principal') {
            SaprodImagen::where('codprod', $imagen->codprod)
                ->where('comercial', $comercial)
                ->where('tipo', 'principal')
                ->where('id', '!=', $id)
                ->update(['tipo' => 'secundaria']);
        }

        // Si es thumbnail o icono, desactivar los anteriores del mismo tipo
        if (in_array($nuevoTipo, ['thumbnail', 'icono'])) {
            SaprodImagen::where('codprod', $imagen->codprod)
                ->where('comercial', $comercial)
                ->where('tipo', $nuevoTipo)
                ->where('id', '!=', $id)
                ->update(['tipo' => 'secundaria']);
        }

        // Si la imagen era principal y ahora cambia a otro tipo, no hay problema
        $imagen->tipo   = $nuevoTipo;
        $imagen->activo = 1;
        $imagen->save();

        return response()->json([
            'success' => true,
            'message' => 'Tipo de imagen actualizado correctamente',
            'imagen'  => $imagen
        ]);
    }

    /**
     * Actualizar el orden de las imágenes
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ordenes'         => 'required|array',
            'ordenes.*.id'    => 'required|integer|exists:saprod_imagenes,id',
            'ordenes.*.orden' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $comercial = session('comercialid');

        foreach ($request->ordenes as $item) {
            SaprodImagen::where('id', $item['id'])
                ->where('comercial', $comercial)
                ->update(['orden' => $item['orden']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden actualizado correctamente'
        ]);
    }

    /**
     * Obtener la imagen principal de un producto (para el carrito)
     */
    public function getImagenPrincipal($codprod)
    {
        $comercial = session('comercialid');

        $imagen = SaprodImagen::where('codprod', $codprod)
            ->where('comercial', $comercial)
            ->where('tipo', 'principal')
            ->first();

        if ($imagen) {
            return response()->json([
                'success' => true,
                'imagen'  => $imagen,
                'url'     => asset($imagen->ruta)
            ]);
        }

        // Si no hay principal, buscar cualquier imagen activa
        $imagen = SaprodImagen::where('codprod', $codprod)
            ->where('comercial', $comercial)
            ->where('activo', 1)
            ->orderBy('orden')
            ->first();

        if ($imagen) {
            return response()->json([
                'success' => true,
                'imagen'  => $imagen,
                'url'     => asset($imagen->ruta),
                'es_alternativa' => true
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No hay imágenes para este producto'
        ], 404);
    }
}
