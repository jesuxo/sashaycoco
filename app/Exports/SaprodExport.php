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

        $saprod     = Saprod::selectRaw("
        codprod,descrip,costod as precio1, costod2 as precio2, costod3 as precio3, preciod as costo,
        refere as referencia, marca,  existen, codinst  ")
            ->where("comercial", $comercial);

        if($codalte !='todo'){
            $saprod = $saprod->whereRaw(" codinst in (select codinst from sainsta where codalte like '$codalte%')");
        }

        $saprod = $saprod->orderBy('marca')->get();

        return $saprod;

    }
}
