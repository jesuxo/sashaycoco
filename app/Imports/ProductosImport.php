<?php
// app/Imports/ProductosImport.php

namespace App\Imports;

use App\Models\Saprod;
use App\Models\Sainsta;
use App\Models\Sacomercial;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductosImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $comercialId;
    protected $match;
    protected $instanciasMap;
    protected $errores = [];
    protected $procesados = 0;
    protected $fallidos = 0;

    public function __construct()
    {
        $this->comercialId = session('comercialid', 1);
        $comercial         = Sacomercial::find($this->comercialId);
        $this->match       = $comercial ? $comercial->match : $this->comercialId;

        // Crear mapa de instancias por código y nombre
        $this->instanciasMap = $this->buildInstanciasMap();
    }

    private function buildInstanciasMap()
    {
        $instancias = Sainsta::where('comercial', $this->match)
            ->get();

        $map = [];
        foreach ($instancias as $instancia) {
            // Mapear por codinst
            $map['codigo_' . $instancia->codinst] = $instancia;
            // Mapear por nombre (sin espacios extras)
            $map['nombre_' . trim(strtolower($instancia->descrip))] = $instancia;
        }

        return $map;
    }

    function clear($data){
        $data = str_replace('/', '', $data);
        $data = str_replace("'", '', $data);
        $data = str_replace(" ", '', $data);
        $data = str_replace('"', '', $data);
        return $data;
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            $comerciales = Sacomercial::where('match', $this->match)->get();

            foreach ($rows as $index => $row) {

                $codprodclear = $this->clear($row['codigo']);

                try {
                    // Validar datos mínimos requeridos
                    if (empty($codprodclear) || empty($row['nombre']) || empty($row['instancia_codigo'])  ) {
                        $this->errores[] = "Fila " . ($index + 2) . ": Faltan datos requeridos (código, nombre e instancia)";
                        $this->fallidos++;
                        continue;
                    }

                    // Buscar la instancia
                    $instancia = null;

                    // Buscar por código de instancia
                    if (!empty($row['instancia_codigo'])) {
                        $instancia = Sainsta::where('codinst', $row['instancia_codigo'])
                            ->where('comercial', $this->match)
                            ->first();
                    }

                    if (!$instancia) {
                        $this->errores[] = "Fila " . ($index + 2) . ": Instancia no encontrada (Código: {$row['instancia_codigo']} )";
                        $this->fallidos++;
                        continue;
                    }

                    // Verificar si el producto ya existe
                    $productoExistente = Saprod::where('codprod', substr($codprodclear, 0, 15))
                        ->where('comercial', $this->comercialId)
                        ->first();

                    if ($productoExistente) {
                        $this->errores[] = "Fila " . ($index + 2) . ": El producto con código {$codprodclear} ya existe";
                        $this->fallidos++;
                        continue;
                    }

                    // Crear producto para cada comercial del match
                    foreach ($comerciales as $comercial) {
                        $producto             = new Saprod();
                        $producto->codprod    = substr($codprodclear, 0, 15);
                        $producto->descrip    = substr($row['nombre'],0,40);
                        $producto->descrip2   = $row['descripcion2'] ?? null;
                        $producto->descrip3   = $row['descripcion3'] ?? null;
                        $producto->marca      = $row['marca'] ?? null;
                        $producto->esexento   = ($this->comercialId == 1 or $this->comercialId == 2 or $this->comercialId == 6 )? 1 : 0;
                        $producto->refere     = $row['referencia'] ?? null;
                        $producto->codinst    = $instancia->codinst;
                        $producto->unidad     = substr($row['unidad'],0,3) ?? null;
                        $producto->preciod    = floatval($row['costo'] ?? 0);
                        $producto->costod     = floatval($row['precio_venta'] ?? 0);
                        $producto->costod2    = floatval($row['precio_venta'] ?? 0);
                        $producto->costod3    = floatval($row['precio_venta'] ?? 0);
                        $producto->activo     = isset($row['activo']) ? (strtolower($row['activo']) == 'si' ? 1 : 0) : 1;
                        $producto->comercial  = $comercial->id;
                        $producto->save();
                    }

                    $this->procesados++;

                } catch (\Exception $e) {
                    $this->errores[] = "Fila " . ($index + 2) . ": Error - " . $e->getMessage();
                    $this->fallidos++;
                    Log::error("Error importando producto: " . $e->getMessage(), ['row' => $row]);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errores[] = "Error general: " . $e->getMessage();
            Log::error("Error en importación masiva: " . $e->getMessage());
            throw $e;
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getErrores()
    {
        return $this->errores;
    }

    public function getProcesados()
    {
        return $this->procesados;
    }

    public function getFallidos()
    {
        return $this->fallidos;
    }
}
