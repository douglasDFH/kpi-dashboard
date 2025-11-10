<?php

namespace App\Http\Controllers;

use App\Services\KpiService;
use App\Models\Equipment;
use App\Models\ProductionPlan;
use App\Models\ProductionData;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    public function index()
    {
        $equipment = Equipment::where('is_active', true)->get();
        $kpis = $this->kpiService->getAllEquipmentKPIs();

        // Estadísticas de planes vs producción real
        $activePlans = ProductionPlan::where('status', 'active')->count();
        $completedPlansToday = ProductionPlan::where('status', 'completed')
            ->whereDate('updated_at', today())
            ->count();
        
        $activeShifts = WorkShift::where('status', 'active')->count();
        
        // Comparativa Plan vs Real (últimos 7 días)
        $planVsReal = $this->getPlanVsRealComparison();
        
        // Top equipos por cumplimiento
        $topEquipment = $this->getTopEquipmentByCompliance();

        return view('dashboard', compact(
            'equipment', 
            'kpis', 
            'activePlans', 
            'completedPlansToday', 
            'activeShifts',
            'planVsReal',
            'topEquipment'
        ));
    }

    /**
     * Obtener comparativa de plan vs real (últimos 7 días)
     */
    private function getPlanVsRealComparison()
    {
        return ProductionData::select(
                DB::raw('DATE(production_date) as date'),
                DB::raw('SUM(planned_production) as total_planned'),
                DB::raw('SUM(actual_production) as total_actual'),
                DB::raw('ROUND(SUM(actual_production) * 100.0 / NULLIF(SUM(planned_production), 0), 2) as efficiency')
            )
            ->whereDate('production_date', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(production_date)'))
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Obtener top equipos por cumplimiento
     */
    private function getTopEquipmentByCompliance()
    {
        return Equipment::select('equipment.id', 'equipment.name', 'equipment.code')
            ->join('production_data', 'equipment.id', '=', 'production_data.equipment_id')
            ->selectRaw('
                SUM(production_data.planned_production) as total_planned,
                SUM(production_data.actual_production) as total_actual,
                ROUND(SUM(production_data.actual_production) * 100.0 / NULLIF(SUM(production_data.planned_production), 0), 2) as compliance
            ')
            ->whereDate('production_data.production_date', '>=', now()->subDays(30))
            ->groupBy('equipment.id', 'equipment.name', 'equipment.code')
            ->orderByDesc('compliance')
            ->limit(5)
            ->get();
    }
}
