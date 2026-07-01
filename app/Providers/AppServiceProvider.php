<?php

namespace App\Providers;

use App\View\Composers\ComercialComposer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.master', ComercialComposer::class);
        View::composer('layouts.partials.topbar', ComercialComposer::class);

        Schema::defaultStringLength(191);
    }
}
