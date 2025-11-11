<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\KpiService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KpiController extends Controller
{
    protected $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    /**
     * Obtiene todos los KPIs de todos los equipos
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $kpis = $this->kpiService->getAllEquipmentKPIs($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $kpis,
        ]);
    }

    /**
     * Obtiene el OEE de un equipo específico
     */
    public function show(Request $request, int $equipmentId)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $oee = $this->kpiService->calculateOEE($equipmentId, $startDate, $endDate);
        $additionalMetrics = $this->kpiService->calculateAdditionalMetrics($equipmentId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => [
                'oee' => $oee,
                'metrics' => $additionalMetrics,
            ],
        ]);
    }

    /**
     * Obtiene la disponibilidad de un equipo
     */
    public function availability(Request $request, int $equipmentId)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $availability = $this->kpiService->calculateAvailability($equipmentId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => [
                'availability' => $availability,
            ],
        ]);
    }

    /**
     * Obtiene el rendimiento de un equipo
     */
    public function performance(Request $request, int $equipmentId)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $performance = $this->kpiService->calculatePerformance($equipmentId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => [
                'performance' => $performance,
            ],
        ]);
    }

    /**
     * Obtiene la calidad de un equipo
     */
    public function quality(Request $request, int $equipmentId)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $quality = $this->kpiService->calculateQuality($equipmentId, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => [
                'quality' => $quality,
            ],
        ]);
    }
}
