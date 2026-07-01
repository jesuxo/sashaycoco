<?php
// app/Exports/PlantillaProductosExport.php

namespace App\Exports;

use App\Models\Sainsta;
use App\Models\Sacomercial;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class PlantillaProductosExport implements WithMultipleSheets
{
    use Exportable;

    protected $comercialId;
    protected $match;

    public function __construct()
    {
        $this->comercialId = session('comercialid', 1);
        $comercial = Sacomercial::find($this->comercialId);
        $this->match = $comercial ? $comercial->match : $this->comercialId;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Hoja 1: Plantilla de productos
        $sheets[] = new PlantillaProductosSheet($this->comercialId, $this->match);

        // Hoja 2: Listado de instancias
        $sheets[] = new InstanciasReferenciaSheet($this->match);

        // Hoja 3: Instrucciones
        $sheets[] = new InstruccionesSheet();

        return $sheets;
    }
}
