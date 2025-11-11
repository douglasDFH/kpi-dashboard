<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\MaquinaController;
use App\Http\Controllers\Admin\PlanMaquinaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Supervisor\JornadaController;
use App\Http\Controllers\Supervisor\MantenimientoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

    // ============================================
    // Admin Routes (Gestión de datos maestros)
    // ============================================
    Route::prefix('admin')->name('admin.')->group(function () {
        // Máquinas
        Route::resource('maquinas', MaquinaController::class);
        Route::post('maquinas/{id}/restore', [MaquinaController::class, 'restore'])
            ->name('maquinas.restore');
        Route::delete('maquinas/{id}/force-delete', [MaquinaController::class, 'forceDelete'])
            ->name('maquinas.forceDelete');

        // Planes de Producción
        Route::resource('planes', PlanMaquinaController::class);
        Route::post('planes/{id}/restore', [PlanMaquinaController::class, 'restore'])
            ->name('planes.restore');
        Route::delete('planes/{id}/force-delete', [PlanMaquinaController::class, 'forceDelete'])
            ->name('planes.forceDelete');

        // Áreas
        Route::resource('areas', AreaController::class);
        Route::post('areas/{id}/restore', [AreaController::class, 'restore'])
            ->name('areas.restore');
        Route::delete('areas/{id}/force-delete', [AreaController::class, 'forceDelete'])
            ->name('areas.forceDelete');
    });

    // ============================================
    // Supervisor Routes (Operación diaria)
    // ============================================
    Route::prefix('supervisor')->name('supervisor.')->group(function () {
        // Jornadas de Producción
        Route::resource('jornadas', JornadaController::class);
        Route::post('jornadas/{jornada}/pausar', [JornadaController::class, 'pausar'])
            ->name('jornadas.pausar');
        Route::post('jornadas/{jornada}/reanudar', [JornadaController::class, 'reanudar'])
            ->name('jornadas.reanudar');
        Route::post('jornadas/{jornada}/finalizar', [JornadaController::class, 'finalizar'])
            ->name('jornadas.finalizar');

        // Mantenimiento
        Route::resource('mantenimiento', MantenimientoController::class);
        Route::get('mantenimiento/maquina/{maquina}', [MantenimientoController::class, 'historialMaquina'])
            ->name('mantenimiento.historial');
    });

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
