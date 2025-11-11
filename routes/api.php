<?php

use App\Http\Controllers\Api\V1\Maquina\HeartbeatController;
use App\Http\Controllers\Api\V1\Maquina\ProduccionController;
use App\Http\Controllers\Api\V1\Maquina\StatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API v1 Routes - Máquinas autenticadas con Sanctum
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::prefix('maquina')->name('maquina.')->group(function () {
        // Registrar producción (Caso de Uso 3)
        Route::post('/produccion', [ProduccionController::class, 'store'])
            ->name('produccion.store');

        // Actualizar estado de máquina
        Route::put('/status', [StatusController::class, 'update'])
            ->name('status.update');

        // Conectar máquina (para contador de conectadas)
        Route::post('/conectar', [StatusController::class, 'conectar'])
            ->name('conectar');

        // Heartbeat (keep-alive)
        Route::post('/heartbeat', [HeartbeatController::class, 'ping'])
            ->name('heartbeat.ping');
    });
});

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Equipment routes
Route::apiResource('equipment', EquipmentController::class)->names([
    'index' => 'api.equipment.index',
    'store' => 'api.equipment.store',
    'show' => 'api.equipment.show',
    'update' => 'api.equipment.update',
    'destroy' => 'api.equipment.destroy',
]);

// Production Data routes
Route::apiResource('production-data', ProductionDataController::class)->names([
    'index' => 'api.production-data.index',
    'store' => 'api.production-data.store',
    'show' => 'api.production-data.show',
    'update' => 'api.production-data.update',
    'destroy' => 'api.production-data.destroy',
]);

// KPI routes
Route::prefix('kpi')->group(function () {
    Route::get('/', [KpiController::class, 'index']);
    Route::get('/{equipmentId}', [KpiController::class, 'show']);
    Route::get('/{equipmentId}/availability', [KpiController::class, 'availability']);
    Route::get('/{equipmentId}/performance', [KpiController::class, 'performance']);
    Route::get('/{equipmentId}/quality', [KpiController::class, 'quality']);
});
*/
