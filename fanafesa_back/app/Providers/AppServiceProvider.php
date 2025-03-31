<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        Schema::defaultStringLength(191);

        /*DB::listen(function ($query) {
            // Registra la consulta y su tiempo de ejecución
            Log::info('Consulta ejecutada: ' . $query->sql);
            Log::info('Tiempo de ejecución: ' . $query->time . 'ms');
        });*/
    }
}
