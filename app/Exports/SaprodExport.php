<?php

namespace App\Exports;

use App\Models\Saprod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class SaprodExport implements FromCollection, WithHeadings
{
    private $codalte;

    // Constructor que acepta el parámetro del departamento
    public function __construct($codalte = null)
    {
        $this->codalte = $codalte;
    }

    public function headings(): array
    {
        return [
            'codprod',
            'descrip',
            'precio1',
            'precio2',
            'precio3',
            'costo',
            'costopro',
            'referencia',
            'marca',
            'existencia',
            'codinst'
        ];
    }

    public function collection()
    {
        $comercial  = session('comercialid') ;
        $codalte    = $this->codalte;

        return Saprod::selectRaw("
        codprod,descrip,costod as precio1, costod2 as precio2, costod3 as precio3, preciod as costo, preciodpro as costopro,
        refere as referencia, marca,  existen, codinst  ")
            ->where("comercial",$comercial)
            ->whereRaw(" codinst in (select codinst from sainsta where codalte like '$codalte%')")
            ->orderBy('marca')->get();

    }
}
