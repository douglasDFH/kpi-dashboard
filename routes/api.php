<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KpiController;
use App\Http\Controllers\Api\ProductionDataController;
use App\Http\Controllers\Api\EquipmentController;

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
