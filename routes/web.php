<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ProductionDataController;
use App\Http\Controllers\DowntimeDataController;
use App\Http\Controllers\QualityDataController;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Dashboard Route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Equipment Management Routes
Route::resource('equipment', EquipmentController::class);

// Production Data Routes
Route::resource('production', ProductionDataController::class);

// Downtime Data Routes
Route::resource('downtime', DowntimeDataController::class);

// Quality Data Routes
Route::resource('quality', QualityDataController::class);
