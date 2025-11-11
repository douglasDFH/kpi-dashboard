<?php

namespace App\Services;

use App\Services\Contracts\KpiServiceInterface;
use App\Models\Maquina;
use App\Models\RegistroProduccion;
use App\Models\EventoParadaJornada;
use App\Models\JornadaProduccion;
use App\Models\PlanMaquina;
use App\Models\ResultadoKpiJornada;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * KPI Service
 * 
 * Servicio de cálculo de KPIs según esquema v5 del proyecto.
 * Fórmula: OEE = Disponibilidad × Rendimiento × Calidad
 * 
 * Schema:
 * - jornadas_produccion: Jornada de trabajo completa de una máquina
 * - eventos_parada_jornada: Paradas (programadas/no programadas) dentro de una jornada
 * - registros_produccion: Registros individuales de producción (1:1 o lotes)
 * - planes_maquina: Plan con ideal_cycle_time_seconds para calcular rendimiento
 */
class KpiService implements KpiServiceInterface
{
    /**
     * Calcula OEE = Disponibilidad × Rendimiento × Calidad
     * 
     * Busca la jornada completada más reciente y sus KPIs pre-calculados,
     * o calcula en tiempo real si se especifica una jornada activa.
     *
     * @param string $maquinaId UUID de la máquina
     * @param Carbon|null $startDate (ignorado en esta versión - usa jornadas)
     * @param Carbon|null $endDate (ignorado en esta versión - usa jornadas)
     * @return array ['oee' => float, 'availability' => float, 'performance' => float, 'quality' => float, 'period' => [...]]
     */
    public function calculateOEE(string $maquinaId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // Buscar jornada más reciente completada (status: 'completed')
        $jornada = JornadaProduccion::where('maquina_id', $maquinaId)
            ->where('status', 'completed')
            ->latest('fin_real')
            ->first();

        if (!$jornada) {
            // Si no hay jornada completada, intentar usar la activa
            $jornada = JornadaProduccion::where('maquina_id', $maquinaId)
                ->where('status', 'running')
                ->latest('inicio_real')
                ->first();

            if (!$jornada) {
                // Sin jornada, devolver ceros
                return [
                    'oee' => 0.00,
                    'availability' => 0.00,
                    'performance' => 0.00,
                    'quality' => 0.00,
                    'period' => [
                        'start' => null,
                        'end' => null,
                    ],
                ];
            }
        }

        // Calcular componentes
        $availability = $this->calculateAvailability($maquinaId, $jornada->id);
        $performance = $this->calculatePerformance($maquinaId, $jornada->id);
        $quality = $this->calculateQuality($maquinaId, $jornada->id);

        // OEE = A × P × Q (ya en %)
        $oee = ($availability / 100) * ($performance / 100) * ($quality / 100) * 100;

        return [
            'oee' => round($oee, 2),
            'availability' => round($availability, 2),
            'performance' => round($performance, 2),
            'quality' => round($quality, 2),
            'period' => [
                'start' => $jornada->inicio_real?->toDateTimeString(),
                'end' => $jornada->fin_real?->toDateTimeString(),
            ],
        ];
    }

    /**
     * Calcula Disponibilidad (Availability)
     * 
     * Disponibilidad = (Tiempo Planificado - Tiempo de Paradas) / Tiempo Planificado × 100
     * 
     * Tiempo Planificado: inicio_real a fin_real (o hasta ahora si activa)
     * Tiempo de Paradas: suma de (fin_parada - inicio_parada) de eventos_parada_jornada
     *
     * @param string $maquinaId UUID de la máquina
     * @param string $jornadaId UUID de la jornada
     * @return float Porcentaje 0-100
     */
    public function calculateAvailability(string $maquinaId, string $jornadaId): float
    {
        $jornada = JornadaProduccion::where('id', $jornadaId)
            ->where('maquina_id', $maquinaId)
            ->first();

        if (!$jornada || !$jornada->inicio_real) {
            return 0.0;
        }

        // Tiempo planificado en minutos
        $inicio = $jornada->inicio_real;
        $fin = $jornada->fin_real ?? Carbon::now();
        $tiempoPlaneado = $inicio->diffInMinutes($fin);

        if ($tiempoPlaneado == 0) {
            return 0.0;
        }

        // Sumar tiempo de todas las paradas en esta jornada
        $tiempoParadas = EventoParadaJornada::where('jornada_id', $jornadaId)
            ->get()
            ->sum(function ($evento) {
                $inicio = $evento->inicio_parada;
                $fin = $evento->fin_parada ?? Carbon::now();
                return $inicio->diffInMinutes($fin);
            });

        $tiempoActivo = $tiempoPlaneado - $tiempoParadas;
        return ($tiempoActivo / $tiempoPlaneado) * 100;
    }

    /**
     * Calcula Rendimiento (Performance)
     * 
     * Rendimiento = (Tiempo Ideal × Unidades Producidas) / Tiempo de Operación Real × 100
     * 
     * Tiempo Ideal = ideal_cycle_time_seconds del plan
     * Unidades Producidas = total_unidades_producidas de la jornada
     * Tiempo Operación Real = Disponibilidad × Tiempo Planificado (sin paradas)
     *
     * @param string $maquinaId UUID de la máquina
     * @param string $jornadaId UUID de la jornada
     * @return float Porcentaje 0-100
     */
    public function calculatePerformance(string $maquinaId, string $jornadaId): float
    {
        $jornada = JornadaProduccion::where('id', $jornadaId)
            ->where('maquina_id', $maquinaId)
            ->first();

        if (!$jornada || !$jornada->inicio_real) {
            return 0.0;
        }

        // Obtener el plan con ideal_cycle_time_seconds
        $plan = PlanMaquina::find($jornada->plan_maquina_id);
        if (!$plan || $plan->ideal_cycle_time_seconds == 0) {
            return 0.0;
        }

        // Unidades producidas (total agregado en la jornada)
        $unidadesProducidas = $jornada->total_unidades_producidas ?? 0;

        if ($unidadesProducidas == 0) {
            return 0.0;
        }

        // Tiempo de operación real = tiempo disponible (sin paradas)
        $inicio = $jornada->inicio_real;
        $fin = $jornada->fin_real ?? Carbon::now();
        $tiempoPlaneado = $inicio->diffInSeconds($fin);

        // Restar tiempo de paradas
        $tiempoParadas = EventoParadaJornada::where('jornada_id', $jornadaId)
            ->get()
            ->sum(function ($evento) {
                $inicio = $evento->inicio_parada;
                $fin = $evento->fin_parada ?? Carbon::now();
                return $inicio->diffInSeconds($fin);
            });

        $tiempoOperacion = $tiempoPlaneado - $tiempoParadas;

        if ($tiempoOperacion == 0) {
            return 0.0;
        }

        // Performance = (Tiempo Ideal × Unidades) / Tiempo Real × 100
        $tiempoIdeal = $plan->ideal_cycle_time_seconds * $unidadesProducidas;
        return ($tiempoIdeal / $tiempoOperacion) * 100;
    }

    /**
     * Calcula Calidad (Quality)
     * 
     * Calidad = Unidades Buenas / Unidades Totales × 100
     * 
     * Unidades Buenas = suma de cantidad_buena de registros_produccion
     * Unidades Totales = suma de cantidad_producida de registros_produccion
     *
     * @param string $maquinaId UUID de la máquina
     * @param string $jornadaId UUID de la jornada
     * @return float Porcentaje 0-100
     */
    public function calculateQuality(string $maquinaId, string $jornadaId): float
    {
        // Sumar registros de producción de esta jornada
        $registros = RegistroProduccion::where('jornada_id', $jornadaId)
            ->where('maquina_id', $maquinaId)
            ->get();

        if ($registros->isEmpty()) {
            return 0.0;
        }

        $totalUnidades = $registros->sum('cantidad_producida');
        $unidadesBuenas = $registros->sum('cantidad_buena');

        if ($totalUnidades == 0) {
            return 0.0;
        }

        return ($unidadesBuenas / $totalUnidades) * 100;
    }

    /**
     * Calcula métricas adicionales de una jornada
     *
     * @param string $maquinaId UUID de la máquina
     * @param string|null $jornadaId UUID de la jornada (si null, usa última)
     * @return array Métricas: total_producido, total_bueno, total_malo, objetivo, cobertura
     */
    public function calculateAdditionalMetrics(string $maquinaId, ?string $jornadaId = null): array
    {
        if (!$jornadaId) {
            $jornada = JornadaProduccion::where('maquina_id', $maquinaId)
                ->latest('created_at')
                ->first();
        } else {
            $jornada = JornadaProduccion::find($jornadaId);
        }

        if (!$jornada) {
            return [
                'total_producido' => 0,
                'total_bueno' => 0,
                'total_malo' => 0,
                'objetivo' => 0,
                'cobertura' => 0.0,
            ];
        }

        $totalProducido = $jornada->total_unidades_producidas ?? 0;
        $totalBueno = $jornada->total_unidades_buenas ?? 0;
        $totalMalo = $jornada->total_unidades_malas ?? 0;
        $objetivo = $jornada->objetivo_unidades_copiado ?? 0;

        $cobertura = $objetivo > 0 ? ($totalProducido / $objetivo) * 100 : 0.0;

        return [
            'total_producido' => $totalProducido,
            'total_bueno' => $totalBueno,
            'total_malo' => $totalMalo,
            'objetivo' => $objetivo,
            'cobertura' => round($cobertura, 2),
        ];
    }
}
