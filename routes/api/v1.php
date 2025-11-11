<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Maquina\ProduccionController;
use App\Http\Controllers\Api\V1\Maquina\StatusController;
use App\Http\Controllers\Api\V1\Maquina\HeartbeatController;

/*
|--------------------------------------------------------------------------
| API v1 Routes - Máquinas
|--------------------------------------------------------------------------
|
| Rutas para máquinas autenticadas con Laravel Sanctum
| 
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas de máquina autenticada
    Route::prefix('maquina')->name('maquina.')->group(function () {
        // Registrar producción (Caso de Uso 3)
        Route::post('/produccion', [ProduccionController::class, 'store'])
            ->name('produccion.store');
        
        // Actualizar estado de máquina
        Route::put('/status', [StatusController::class, 'update'])
            ->name('status.update');
        
        // Heartbeat (keep-alive)
        Route::post('/heartbeat', [HeartbeatController::class, 'ping'])
            ->name('heartbeat.ping');
    });
});
