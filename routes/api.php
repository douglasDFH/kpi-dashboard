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
Route::apiResource('equipment', EquipmentController::class);

// Production Data routes
Route::apiResource('production-data', ProductionDataController::class);

// KPI routes
Route::prefix('kpi')->group(function () {
    Route::get('/', [KpiController::class, 'index']);
    Route::get('/{equipmentId}', [KpiController::class, 'show']);
    Route::get('/{equipmentId}/availability', [KpiController::class, 'availability']);
    Route::get('/{equipmentId}/performance', [KpiController::class, 'performance']);
    Route::get('/{equipmentId}/quality', [KpiController::class, 'quality']);
});
