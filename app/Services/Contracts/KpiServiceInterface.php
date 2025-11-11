<?php

namespace App\Services\Contracts;

use Carbon\Carbon;

/**
 * KPI Service Interface
 * 
 * Define el contrato para el cálculo de KPIs.
 * 
 * Utiliza el esquema v5 con jornadas_produccion como base.
 * Los métodos disponibles con sobrecarga de parámetros para flexibilidad.
 */
interface KpiServiceInterface
{
    /**
     * Calcula OEE = Disponibilidad × Rendimiento × Calidad
     * 
     * Busca la jornada completada más reciente y calcula sobre ella,
     * o usa la jornada activa si no hay completada.
     *
     * @param string $maquinaId UUID de la máquina
     * @param Carbon|null $startDate (ignorado - usa jornadas)
     * @param Carbon|null $endDate (ignorado - usa jornadas)
     * @return array ['oee' => float, 'availability' => float, 'performance' => float, 'quality' => float, 'period' => [...]]
     */
    public function calculateOEE(string $maquinaId, ?Carbon $startDate = null, ?Carbon $endDate = null): array;

    /**
     * Calcula Disponibilidad sobre una jornada específica
     * 
     * Disponibilidad = (Tiempo Planificado - Tiempo de Paradas) / Tiempo Planificado × 100
     *
     * @param string $maquinaId UUID de la máquina
     * @param string $jornadaId UUID de la jornada
     * @return float Porcentaje 0-100
     */
    public function calculateAvailability(string $maquinaId, string $jornadaId): float;

    /**
     * Calcula Rendimiento sobre una jornada específica
     * 
     * Rendimiento = (Tiempo Ideal × Unidades Producidas) / Tiempo de Operación Real × 100
     *
     * @param string $maquinaId UUID de la máquina
     * @param string $jornadaId UUID de la jornada
     * @return float Porcentaje 0-100
     */
    public function calculatePerformance(string $maquinaId, string $jornadaId): float;

    /**
     * Calcula Calidad sobre una jornada específica
     * 
     * Calidad = Unidades Buenas / Unidades Totales × 100
     *
     * @param string $maquinaId UUID de la máquina
     * @param string $jornadaId UUID de la jornada
     * @return float Porcentaje 0-100
     */
    public function calculateQuality(string $maquinaId, string $jornadaId): float;

    /**
     * Calcula métricas adicionales
     *
     * @param string $maquinaId UUID de la máquina
     * @param string|null $jornadaId UUID de la jornada (opcional, usa última si es null)
     * @return array Métricas: total_producido, total_bueno, total_malo, objetivo, cobertura
     */
    public function calculateAdditionalMetrics(string $maquinaId, ?string $jornadaId = null): array;
}
