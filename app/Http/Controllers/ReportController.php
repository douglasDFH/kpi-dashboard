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

        // Preparar datos para el gráfico
        $chartData = $productionData->map(function($prod) {
            return [
                'date' => $prod->production_date->format('d/m'),
                'equipment' => $prod->equipment->name,
                'planned' => $prod->planned_production,
                'actual' => $prod->actual_production,
                'good' => $prod->good_units
            ];
        })->values();

        return view('reports.production', compact('productionData', 'equipment', 'startDate', 'endDate', 'totals', 'equipmentId', 'chartData'));
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

    /**
     * Display custom report builder
     */
    public function custom()
    {
        $equipment = Equipment::where('is_active', true)->get();
        return view('reports.custom', compact('equipment'));
    }

    /**
     * Generate custom report based on user selections
     */
    public function generateCustomReport(Request $request)
    {
        $validated = $request->validate([
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'exists:equipment,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'metrics' => 'required|array|min:1',
            'metrics.*' => 'in:oee,production,quality,downtime',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $equipmentIds = $validated['equipment_ids'];
        $metrics = $validated['metrics'];

        $reportData = [];

        foreach ($equipmentIds as $equipmentId) {
            $equipment = Equipment::find($equipmentId);
            $data = ['equipment' => $equipment];

            // OEE Metrics
            if (in_array('oee', $metrics)) {
                $data['oee'] = $this->kpiService->calculateOEE($equipmentId, $startDate, $endDate);
            }

            // Production Metrics
            if (in_array('production', $metrics)) {
                $productionData = ProductionData::where('equipment_id', $equipmentId)
                    ->whereBetween('production_date', [$startDate, $endDate])
                    ->get();

                $data['production'] = [
                    'total_planned' => $productionData->sum('planned_production'),
                    'total_actual' => $productionData->sum('actual_production'),
                    'total_good' => $productionData->sum('good_units'),
                    'total_defective' => $productionData->sum('defective_units'),
                    'efficiency' => $productionData->sum('planned_production') > 0
                        ? ($productionData->sum('actual_production') / $productionData->sum('planned_production')) * 100
                        : 0,
                    'records' => $productionData,
                ];
            }

            // Quality Metrics
            if (in_array('quality', $metrics)) {
                $qualityData = QualityData::where('equipment_id', $equipmentId)
                    ->whereBetween('inspection_date', [$startDate, $endDate])
                    ->get();

                $data['quality'] = [
                    'total_inspected' => $qualityData->sum('total_inspected'),
                    'total_approved' => $qualityData->sum('approved_units'),
                    'total_rejected' => $qualityData->sum('rejected_units'),
                    'quality_rate' => $qualityData->sum('total_inspected') > 0
                        ? ($qualityData->sum('approved_units') / $qualityData->sum('total_inspected')) * 100
                        : 0,
                    'records' => $qualityData,
                ];
            }

            // Downtime Metrics
            if (in_array('downtime', $metrics)) {
                $downtimeData = DowntimeData::where('equipment_id', $equipmentId)
                    ->whereBetween('start_time', [$startDate, $endDate])
                    ->get();

                $data['downtime'] = [
                    'total_minutes' => $downtimeData->sum('duration_minutes'),
                    'total_hours' => round($downtimeData->sum('duration_minutes') / 60, 2),
                    'planned' => $downtimeData->where('category', 'planificado')->sum('duration_minutes'),
                    'unplanned' => $downtimeData->where('category', 'no planificado')->sum('duration_minutes'),
                    'records' => $downtimeData,
                ];
            }

            $reportData[] = $data;
        }

        return response()->json([
            'success' => true,
            'data' => $reportData,
            'period' => [
                'start' => $startDate->format('d/m/Y'),
                'end' => $endDate->format('d/m/Y'),
            ],
        ]);
    }

    /**
     * Export custom report to PDF or Excel
     */
    public function exportCustomReport(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:pdf,excel',
            'report_data' => 'required|json',
        ]);

        // Para futuro: Implementar exportación con libraries como:
        // - Laravel Excel (maatwebsite/excel) para Excel
        // - DomPDF o Snappy para PDF
        
        return response()->json([
            'success' => true,
            'message' => 'Funcionalidad de exportación disponible próximamente',
            'format' => $validated['format'],
        ]);
    }
}
