<?php

namespace App\Imports;

use App\Models\Saprod;
use App\Models\Saprodsucursal;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SaprodUpdate implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $comercial  = session('comercialid') ;

        foreach ($rows as $row)
        {
            $saprod = Saprod::where(['codprod'=> $row['codprod'], 'comercial' => $comercial])->first();
            if($saprod){
                $saprod->activo = 1;
                if(isset($row['costo'])     and $row['costo']   > 0 )  $saprod->preciod    = $row['costo'];
                if(isset($row['costopro'])  and $row['costopro']> 0 )  $saprod->preciodpro = $row['costopro'];
                if(isset($row['precio1'])   and $row['precio1'] > 0 )  $saprod->costod     = $row['precio1'];
                if(isset($row['precio2'])   and $row['precio2'] > 0 )  $saprod->costod2    = $row['precio2'];
                if(isset($row['precio3'])   and $row['precio3'] > 0 )  $saprod->costod3    = $row['precio3'];
                if(isset($row['descrip'])   and $row['descrip'] !='' ) $saprod->descrip    = $row['descrip'];
                if(isset($row['marca'])     and $row['marca']   !='' ) $saprod->marca      = $row['marca'];
                if(isset($row['refere'])    and $row['refere']  !='' ) $saprod->refere     = $row['referencia'];
                if(isset($row['codinst'])   and $row['codinst'] > 0  ) $saprod->codinst    = $row['codinst'];
                $saprod->save();

                $prodsucursal = Saprodsucursal::with('producto')->where('codprod', $row['codprod'])->get();
                if($prodsucursal)
                    foreach ($prodsucursal as $item){
                        if($item->producto->comercial == $comercial)
                            $item->delete();
                    }

            }

        }
    }
}
