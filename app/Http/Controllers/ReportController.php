<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\ProductionData;
use App\Models\ProductionPlan;
use App\Models\WorkShift;
use App\Models\QualityData;
use App\Models\DowntimeData;
use App\Services\KpiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Traits\AuthorizesPermissions;

class ReportController extends Controller
{
    use AuthorizesPermissions;
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
        $this->authorizePermission('reports.view', 'No tienes permiso para ver reportes.');
        
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

        // Obtener datos de producción con relaciones
        $query = ProductionData::with(['equipment', 'plan', 'workShift'])
            ->whereBetween('production_date', [$startDate, $endDate])
            ->orderBy('production_date', 'desc');

        if ($equipmentId) {
            $query->where('equipment_id', $equipmentId);
        }

        $productionData = $query->get();

        // Obtener estadísticas de planes en el período
        $plansQuery = ProductionPlan::with('equipment')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            });

        if ($equipmentId) {
            $plansQuery->where('equipment_id', $equipmentId);
        }

        $plans = $plansQuery->get();
        
        $planStats = [
            'total' => $plans->count(),
            'completed' => $plans->where('status', 'completed')->count(),
            'active' => $plans->where('status', 'active')->count(),
            'cancelled' => $plans->where('status', 'cancelled')->count(),
            'target_total' => $plans->sum('target_quantity'),
        ];

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

        return view('reports.production', compact('productionData', 'equipment', 'startDate', 'endDate', 'totals', 'equipmentId', 'chartData', 'planStats', 'plans'));
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
        $this->authorizePermission('reports.view', 'No tienes permiso para generar reportes.');
        
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
        $this->authorizePermission('reports.export', 'No tienes permiso para exportar reportes.');
        
        $validated = $request->validate([
            'format' => 'required|in:pdf,excel,csv',
        ]);

        // Get report data from session or regenerate
        $reportData = $request->input('report_data');
        
        if (!$reportData) {
            return response()->json([
                'success' => false,
                'message' => 'No hay datos de reporte para exportar',
            ], 400);
        }

        $data = json_decode($reportData, true);
        
        if ($validated['format'] === 'pdf') {
            return $this->exportToPdf($data);
        } elseif ($validated['format'] === 'csv') {
            return $this->exportToCsv($data);
        }

        return response()->json([
            'success' => false,
            'message' => 'Formato no soportado',
        ], 400);
    }

    /**
     * Export report to PDF
     */
    private function exportToPdf($reportData)
    {
        $data = $reportData['data'];
        $period = $reportData['period'];
        $selectedMetrics = [];
        
        // Determinar qué métricas están incluidas
        if (isset($data[0])) {
            if (isset($data[0]['oee'])) $selectedMetrics[] = 'oee';
            if (isset($data[0]['production'])) $selectedMetrics[] = 'producción';
            if (isset($data[0]['quality'])) $selectedMetrics[] = 'calidad';
            if (isset($data[0]['downtime'])) $selectedMetrics[] = 'downtime';
        }

        $pdf = Pdf::loadView('reports.custom-pdf', [
            'data' => $data,
            'period' => $period,
            'selectedMetrics' => $selectedMetrics,
        ]);

        $filename = 'reporte-personalizado-' . date('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export report to CSV
     */
    private function exportToCsv($reportData)
    {
        $data = $reportData['data'];
        $period = $reportData['period'];
        
        $filename = 'reporte-personalizado-' . date('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($data, $period) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Información del reporte
            fputcsv($file, ['REPORTE PERSONALIZADO - METALURGICA PRECISION S.A.']);
            fputcsv($file, ['Período', $period['start'] . ' - ' . $period['end']]);
            fputcsv($file, ['Generado', date('d/m/Y H:i:s')]);
            fputcsv($file, []);
            
            foreach ($data as $equipmentData) {
                // Nombre del equipo
                fputcsv($file, []);
                fputcsv($file, ['EQUIPO', $equipmentData['equipment']['name'], 'Código', $equipmentData['equipment']['code']]);
                fputcsv($file, []);
                
                // OEE
                if (isset($equipmentData['oee'])) {
                    fputcsv($file, ['INDICADORES OEE']);
                    fputcsv($file, ['Métrica', 'Valor']);
                    fputcsv($file, ['OEE', $equipmentData['oee']['oee'] . '%']);
                    fputcsv($file, ['Disponibilidad', $equipmentData['oee']['availability'] . '%']);
                    fputcsv($file, ['Rendimiento', $equipmentData['oee']['performance'] . '%']);
                    fputcsv($file, ['Calidad', $equipmentData['oee']['quality'] . '%']);
                    fputcsv($file, []);
                }
                
                // Producción
                if (isset($equipmentData['production'])) {
                    fputcsv($file, ['MÉTRICAS DE PRODUCCIÓN']);
                    fputcsv($file, ['Métrica', 'Valor']);
                    fputcsv($file, ['Planificado', number_format($equipmentData['production']['total_planned'], 0, ',', '.')]);
                    fputcsv($file, ['Producido', number_format($equipmentData['production']['total_actual'], 0, ',', '.')]);
                    fputcsv($file, ['Unidades Buenas', number_format($equipmentData['production']['total_good'], 0, ',', '.')]);
                    fputcsv($file, ['Eficiencia', number_format($equipmentData['production']['efficiency'], 2, ',', '.') . '%']);
                    fputcsv($file, []);
                }
                
                // Calidad
                if (isset($equipmentData['quality'])) {
                    fputcsv($file, ['MÉTRICAS DE CALIDAD']);
                    fputcsv($file, ['Métrica', 'Valor']);
                    fputcsv($file, ['Inspeccionado', number_format($equipmentData['quality']['total_inspected'], 0, ',', '.')]);
                    fputcsv($file, ['Aprobadas', number_format($equipmentData['quality']['total_approved'], 0, ',', '.')]);
                    fputcsv($file, ['Rechazadas', number_format($equipmentData['quality']['total_rejected'], 0, ',', '.')]);
                    fputcsv($file, ['Tasa de Calidad', number_format($equipmentData['quality']['quality_rate'], 2, ',', '.') . '%']);
                    fputcsv($file, []);
                }
                
                // Downtime
                if (isset($equipmentData['downtime'])) {
                    fputcsv($file, ['TIEMPOS MUERTOS']);
                    fputcsv($file, ['Métrica', 'Valor']);
                    fputcsv($file, ['Total Minutos', number_format($equipmentData['downtime']['total_minutes'], 0, ',', '.')]);
                    fputcsv($file, ['Total Horas', $equipmentData['downtime']['total_hours']]);
                    fputcsv($file, ['Planificado', number_format($equipmentData['downtime']['planned'], 0, ',', '.')]);
                    fputcsv($file, ['No Planificado', number_format($equipmentData['downtime']['unplanned'], 0, ',', '.')]);
                    fputcsv($file, []);
                }
                
                fputcsv($file, ['----------------------------------------']);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
