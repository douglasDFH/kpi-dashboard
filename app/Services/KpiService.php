<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\ProductionData;
use App\Models\QualityData;
use App\Models\DowntimeData;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KpiService
{
    /**
     * Calcula el OEE (Overall Equipment Effectiveness) para un equipo
     * OEE = Availability × Performance × Quality
     *
     * @param int $equipmentId
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function calculateOEE(int $equipmentId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // Por defecto, usar últimos 30 días para tener más datos
        $startDate = $startDate ?? Carbon::now()->subDays(30)->startOfDay();
        $endDate = $endDate ?? Carbon::now()->endOfDay();

        $availability = $this->calculateAvailability($equipmentId, $startDate, $endDate);
        $performance = $this->calculatePerformance($equipmentId, $startDate, $endDate);
        $quality = $this->calculateQuality($equipmentId, $startDate, $endDate);

        $oee = ($availability / 100) * ($performance / 100) * ($quality / 100) * 100;

        return [
            'oee' => round($oee, 2),
            'availability' => round($availability, 2),
            'performance' => round($performance, 2),
            'quality' => round($quality, 2),
            'period' => [
                'start' => $startDate->toDateTimeString(),
                'end' => $endDate->toDateTimeString(),
            ],
        ];
    }

    /**
     * Calcula la disponibilidad (Availability)
     * Availability = (Planned Production Time - Downtime) / Planned Production Time × 100
     *
     * @param int $equipmentId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return float
     */
    public function calculateAvailability(int $equipmentId, Carbon $startDate, Carbon $endDate): float
    {
        // Tiempo planificado en minutos (8 horas por día como ejemplo)
        $plannedProductionTime = $startDate->diffInMinutes($endDate);

        // Total de downtime en minutos
        $totalDowntime = DowntimeData::where('equipment_id', $equipmentId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->sum('duration_minutes');

        if ($plannedProductionTime == 0) {
            return 0;
        }

        $runTime = $plannedProductionTime - $totalDowntime;
        return ($runTime / $plannedProductionTime) * 100;
    }

    /**
     * Calcula el rendimiento (Performance)
     * Performance = (Ideal Cycle Time × Total Count) / Run Time × 100
     *
     * @param int $equipmentId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return float
     */
    public function calculatePerformance(int $equipmentId, Carbon $startDate, Carbon $endDate): float
    {
        $productionData = ProductionData::where('equipment_id', $equipmentId)
            ->whereBetween('production_date', [$startDate, $endDate])
            ->get();

        if ($productionData->isEmpty()) {
            return 0;
        }

        $totalActualProduction = $productionData->sum('actual_production');
        $totalPlannedProduction = $productionData->sum('planned_production');

        if ($totalPlannedProduction == 0) {
            return 0;
        }

        return ($totalActualProduction / $totalPlannedProduction) * 100;
    }

    /**
     * Calcula la calidad (Quality)
     * Quality = Good Units / Total Units × 100
     *
     * @param int $equipmentId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return float
     */
    public function calculateQuality(int $equipmentId, Carbon $startDate, Carbon $endDate): float
    {
        $productionData = ProductionData::where('equipment_id', $equipmentId)
            ->whereBetween('production_date', [$startDate, $endDate])
            ->get();

        if ($productionData->isEmpty()) {
            return 0;
        }

        $totalGoodUnits = $productionData->sum('good_units');
        $totalActualProduction = $productionData->sum('actual_production');

        if ($totalActualProduction == 0) {
            return 0;
        }

        return ($totalGoodUnits / $totalActualProduction) * 100;
    }

    /**
     * Obtiene un resumen de KPIs para todos los equipos
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function getAllEquipmentKPIs(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $equipment = Equipment::where('is_active', true)->get();
        $kpis = [];

        foreach ($equipment as $eq) {
            $kpis[] = [
                'equipment_id' => $eq->id,
                'equipment_name' => $eq->name,
                'equipment_code' => $eq->code,
                'kpis' => $this->calculateOEE($eq->id, $startDate, $endDate),
            ];
        }

        return $kpis;
    }

    /**
     * Calcula métricas adicionales
     *
     * @param int $equipmentId
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function calculateAdditionalMetrics(int $equipmentId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // Por defecto, usar últimos 30 días para coincidir con calculateOEE
        $startDate = $startDate ?? Carbon::now()->subDays(30)->startOfDay();
        $endDate = $endDate ?? Carbon::now()->endOfDay();

        // Producción total
        $totalProduction = ProductionData::where('equipment_id', $equipmentId)
            ->whereBetween('production_date', [$startDate, $endDate])
            ->sum('actual_production');

        // Unidades defectuosas
        $defectiveUnits = ProductionData::where('equipment_id', $equipmentId)
            ->whereBetween('production_date', [$startDate, $endDate])
            ->sum('defective_units');

        // Total downtime
        $totalDowntime = DowntimeData::where('equipment_id', $equipmentId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->sum('duration_minutes');

        // Downtime por categoría
        $downtimeByCategory = DowntimeData::where('equipment_id', $equipmentId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(duration_minutes) as total_minutes'))
            ->groupBy('category')
            ->get();

        return [
            'total_production' => $totalProduction,
            'defective_units' => $defectiveUnits,
            'total_downtime_minutes' => $totalDowntime,
            'downtime_by_category' => $downtimeByCategory,
        ];
    }
}
