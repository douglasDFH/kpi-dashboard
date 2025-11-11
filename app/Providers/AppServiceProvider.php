<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\KpiServiceInterface;
use App\Services\KpiService;
use App\Services\Contracts\JornadaServiceInterface;
use App\Services\JornadaService;
use App\Services\Contracts\ProduccionServiceInterface;
use App\Services\ProduccionService;
use App\Services\Contracts\MantenimientoServiceInterface;
use App\Services\MantenimientoService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(KpiServiceInterface::class, KpiService::class);
        $this->app->bind(JornadaServiceInterface::class, JornadaService::class);
        $this->app->bind(ProduccionServiceInterface::class, ProduccionService::class);
        $this->app->bind(MantenimientoServiceInterface::class, MantenimientoService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
