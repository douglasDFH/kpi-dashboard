<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ProductionDataController;
use App\Http\Controllers\DowntimeDataController;
use App\Http\Controllers\QualityDataController;
use App\Http\Controllers\ReportController;

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

// Reports Routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/oee', [ReportController::class, 'oee'])->name('oee');
    Route::get('/production', [ReportController::class, 'production'])->name('production');
    Route::get('/quality', [ReportController::class, 'quality'])->name('quality');
    Route::get('/downtime', [ReportController::class, 'downtime'])->name('downtime');
    Route::get('/comparative', [ReportController::class, 'comparative'])->name('comparative');
    Route::get('/custom', [ReportController::class, 'custom'])->name('custom');
    Route::post('/custom/generate', [ReportController::class, 'generateCustomReport'])->name('custom.generate');
    Route::post('/custom/export', [ReportController::class, 'exportCustomReport'])->name('custom.export');
});
