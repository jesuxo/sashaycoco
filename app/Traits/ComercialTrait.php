<?php
// app/Traits/ComercialTrait.php

namespace App\Traits;

use Illuminate\Support\Facades\Session;

trait ComercialTrait
{
    protected function getComercialId()
    {
        $comercialId = Session::get('comercialid');
        if (!$comercialId) {
            Session::put('comercialid', 1);
            $comercialId = 1;
        }
        return $comercialId;
    }

    protected function getSucursalesComercial()
    {
        $comercialId = $this->getComercialId();
        return \App\Models\Sasucursal::where('fk_comercial', $comercialId)
            ->orderBy('descrip')
            ->get();
    }
}
