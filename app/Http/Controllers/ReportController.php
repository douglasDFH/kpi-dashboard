<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\ProductionData;
use App\Models\QualityData;
use App\Models\DowntimeData;
use App\Services\KpiService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    /**
     * Display the reports index page
     */
    public function index()
    {
        $equipment = Equipment::where('is_active', true)->get();
        return view('reports.index', compact('equipment'));
    }

    /**
     * Display OEE report
     */
    public function oee(Request $request)
    {
        $equipment = Equipment::where('is_active', true)->get();

        // Filtros de fecha
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->subDays(7);

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        // Calcular OEE para cada equipo
        $oeeData = [];
        foreach ($equipment as $eq) {
            $kpis = $this->kpiService->calculateOEE($eq->id, $startDate, $endDate);
            $metrics = $this->kpiService->calculateAdditionalMetrics($eq->id, $startDate, $endDate);

            $oeeData[] = [
                'equipment' => $eq,
                'kpis' => $kpis,
                'metrics' => $metrics,
            ];
        }

        return view('reports.oee', compact('oeeData', 'equipment', 'startDate', 'endDate'));
    }

    /**
     * Display production report
     */
    public function production(Request $request)
    {
        $equipment = Equipment::where('is_active', true)->get();

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->subDays(7);

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        $equipmentId = $request->equipment_id;

        // Obtener datos de producción
        $query = ProductionData::with('equipment')
            ->whereBetween('production_date', [$startDate, $endDate])
            ->orderBy('production_date', 'desc');

        if ($equipmentId) {
            $query->where('equipment_id', $equipmentId);
        }

        $productionData = $query->get();

        // Calcular totales
        $totals = [
            'planned' => $productionData->sum('planned_production'),
            'actual' => $productionData->sum('actual_production'),
            'good' => $productionData->sum('good_units'),
            'defective' => $productionData->sum('defective_units'),
            'efficiency' => $productionData->sum('planned_production') > 0
                ? ($productionData->sum('actual_production') / $productionData->sum('planned_production')) * 100
                : 0,
        ];

        return view('reports.production', compact('productionData', 'equipment', 'startDate', 'endDate', 'totals', 'equipmentId'));
    }

    /**
     * Display quality report
     */
    public function quality(Request $request)
    {
        $equipment = Equipment::where('is_active', true)->get();

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->subDays(7);

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        $equipmentId = $request->equipment_id;

        // Obtener datos de calidad
        $query = QualityData::with('equipment')
            ->whereBetween('inspection_date', [$startDate, $endDate])
            ->orderBy('inspection_date', 'desc');

        if ($equipmentId) {
            $query->where('equipment_id', $equipmentId);
        }

        $qualityData = $query->get();

        // Calcular totales
        $totals = [
            'total_inspected' => $qualityData->sum('total_inspected'),
            'approved' => $qualityData->sum('approved_units'),
            'rejected' => $qualityData->sum('rejected_units'),
            'quality_rate' => $qualityData->sum('total_inspected') > 0
                ? ($qualityData->sum('approved_units') / $qualityData->sum('total_inspected')) * 100
                : 0,
        ];

        // Agrupar defectos por tipo
        $defectsByType = $qualityData->whereNotNull('defect_type')
            ->groupBy('defect_type')
            ->map(function ($items) {
                return $items->sum('rejected_units');
            })
            ->sortDesc();

        return view('reports.quality', compact('qualityData', 'equipment', 'startDate', 'endDate', 'totals', 'defectsByType', 'equipmentId'));
    }

    /**
     * Display downtime report
     */
    public function downtime(Request $request)
    {
        $equipment = Equipment::where('is_active', true)->get();

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->subDays(7);

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        $equipmentId = $request->equipment_id;

        // Obtener datos de downtime
        $query = DowntimeData::with('equipment')
            ->whereBetween('start_time', [$startDate, $endDate])
            ->orderBy('start_time', 'desc');

        if ($equipmentId) {
            $query->where('equipment_id', $equipmentId);
        }

        $downtimeData = $query->get();

        // Calcular totales
        $totals = [
            'total_downtime' => $downtimeData->sum('duration_minutes'),
            'total_downtime_hours' => round($downtimeData->sum('duration_minutes') / 60, 2),
            'planned' => $downtimeData->where('category', 'planificado')->sum('duration_minutes'),
            'unplanned' => $downtimeData->where('category', 'no planificado')->sum('duration_minutes'),
        ];

        // Agrupar downtime por razón
        $downtimeByReason = $downtimeData->groupBy('reason')
            ->map(function ($items) {
                return $items->sum('duration_minutes');
            })
            ->sortDesc();

        return view('reports.downtime', compact('downtimeData', 'equipment', 'startDate', 'endDate', 'totals', 'downtimeByReason', 'equipmentId'));
    }

    /**
     * Display comparative report
     */
    public function comparative(Request $request)
    {
        $equipment = Equipment::where('is_active', true)->get();

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->subDays(7);

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        // Calcular KPIs para cada equipo
        $comparativeData = [];
        foreach ($equipment as $eq) {
            $kpis = $this->kpiService->calculateOEE($eq->id, $startDate, $endDate);
            $metrics = $this->kpiService->calculateAdditionalMetrics($eq->id, $startDate, $endDate);

            $comparativeData[] = [
                'equipment' => $eq,
                'kpis' => $kpis,
                'metrics' => $metrics,
            ];
        }

        return view('reports.comparative', compact('comparativeData', 'equipment', 'startDate', 'endDate'));
    }
}
