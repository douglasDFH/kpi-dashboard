<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect root to dashboard or login
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
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

    // User Management Routes
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

    // Audit Log Routes
    Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('/audit/{id}', [AuditLogController::class, 'show'])->name('audit.show');

    // Production Plans Routes
    Route::resource('production-plans', ProductionPlanController::class);
    Route::post('/production-plans/{productionPlan}/activate', [ProductionPlanController::class, 'activate'])->name('production-plans.activate');
    Route::post('/production-plans/{productionPlan}/complete', [ProductionPlanController::class, 'complete'])->name('production-plans.complete');
    Route::post('/production-plans/{productionPlan}/cancel', [ProductionPlanController::class, 'cancel'])->name('production-plans.cancel');

    // Work Shifts Routes
    Route::resource('work-shifts', WorkShiftController::class)->except(['edit', 'update']);
    Route::post('/work-shifts/{workShift}/end', [WorkShiftController::class, 'end'])->name('work-shifts.end');
    Route::post('/work-shifts/{workShift}/record-production', [WorkShiftController::class, 'recordProduction'])->name('work-shifts.record-production');
    */
});
