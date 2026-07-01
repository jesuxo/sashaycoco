<?php
// app/View/Composers/ComercialComposer.php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ComercialComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();

        if ($user) {
            $comerciales_acceso = $user->getComercialesAcceso();
            $comercialdata = session('comercialdata');

            $view->with([
                'comerciales_acceso' => $comerciales_acceso,
                'comercialdata' => $comercialdata,
                'comercialid' => session('comercialid')
            ]);
        } else {
            $view->with([
                'comerciales_acceso' => collect(),
                'comercialdata' => null,
                'comercialid' => null
            ]);
        }
    }
}
