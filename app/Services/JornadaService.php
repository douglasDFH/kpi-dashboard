<?php

namespace App\Services;

use App\Services\Contracts\JornadaServiceInterface;
use App\Models\JornadaProduccion;
use App\Models\PlanMaquina;
use App\Models\EventoParadaJornada;
use App\Models\Maquina;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Jornada Service
 * 
 * Servicio de gestión de jornadas de producción.
 * 
 * Responsabilidades:
 * - Iniciar jornadas: Crear registro en jornadas_produccion con snapshot del plan
 * - Finalizar jornadas: Cerrar jornada y dispara cálculo de KPIs
 * - Pausar jornadas: Crear evento de parada
 * - Reanudar jornadas: Cerrar evento de parada abierto
 * 
 * Schema Referencias:
 * - jornadas_produccion: Contiene el estado y datos agregados de la jornada
 * - eventos_parada_jornada: Registra todas las paradas (inicio_parada, fin_parada)
 * - planes_maquina: Plan base con objetivos e ideal_cycle_time
 */
class JornadaService implements JornadaServiceInterface
{
    /**
     * Inicia una nueva jornada de producción
     * 
     * Proceso:
     * 1. Busca el plan activo para la máquina
     * 2. Crea registro en jornadas_produccion con snapshot del plan
     * 3. Sets status a 'running'
     * 4. Registra en auditoría
     *
     * @param string $maquinaId UUID de la máquina
     * @param string $supervisorId ID del usuario supervisor
     * @return JornadaProduccion La jornada creada
     * @throws \Exception Si no hay plan activo
     */
    public function iniciarJornada(string $maquinaId, string $supervisorId): JornadaProduccion
    {
        // Buscar máquina
        $maquina = Maquina::findOrFail($maquinaId);

        // Buscar plan activo
        $planActivo = PlanMaquina::where('maquina_id', $maquinaId)
            ->where('activo', true)
            ->first();

        if (!$planActivo) {
            throw new \Exception("No hay plan activo para la máquina: {$maquina->nombre}");
        }

        // Crear jornada con snapshot del plan
        $jornada = JornadaProduccion::create([
            'plan_maquina_id' => $planActivo->id,
            'maquina_id' => $maquinaId,
            'supervisor_id' => $supervisorId,
            'status' => 'running',
            'inicio_real' => now(),
            
            // Snapshot del plan
            'objetivo_unidades_copiado' => $planActivo->objetivo_unidades,
            'unidad_medida_copiado' => $planActivo->unidad_medida,
            'limite_fallos_critico_copiado' => $planActivo->limite_fallos_critico,
            
            // Iniciar contadores en 0
            'total_unidades_producidas' => 0,
            'total_unidades_buenas' => 0,
            'total_unidades_malas' => 0,
        ]);

        // Log en auditoría
        Log::info("Jornada iniciada", [
            'jornada_id' => $jornada->id,
            'maquina_id' => $maquinaId,
            'supervisor_id' => $supervisorId,
            'plan_id' => $planActivo->id,
        ]);

        return $jornada;
    }

    /**
     * Finaliza una jornada de producción
     * 
     * Proceso:
     * 1. Valida que la jornada esté en estado 'running'
     * 2. Cierra todos los eventos de parada abiertos
     * 3. Sets status a 'completed' y fin_real
     * 4. Dispara Job CalcularKpisFinalesJornada
     * 5. Registra en auditoría
     *
     * @param string $jornadaId UUID de la jornada
     * @return JornadaProduccion La jornada finalizada
     * @throws \Exception Si la jornada no está en estado correcto
     */
    public function finalizarJornada(string $jornadaId): JornadaProduccion
    {
        $jornada = JornadaProduccion::findOrFail($jornadaId);

        if ($jornada->status !== 'running' && $jornada->status !== 'paused' && $jornada->status !== 'stopped_critical') {
            throw new \Exception("No se puede finalizar una jornada en estado: {$jornada->status}");
        }

        // Cerrar eventos de parada abiertos
        $eventosAbiertos = EventoParadaJornada::where('jornada_id', $jornadaId)
            ->whereNull('fin_parada')
            ->get();

        foreach ($eventosAbiertos as $evento) {
            $evento->update(['fin_parada' => now()]);
        }

        // Finalizar jornada
        $jornada->update([
            'status' => 'completed',
            'fin_real' => now(),
        ]);

        // Disparar Job para calcular KPIs finales
        // TODO: Implementar CalcularKpisFinalesJornada Job
        // dispatch(new CalcularKpisFinalesJornada($jornada->id));

        // Log en auditoría
        Log::info("Jornada finalizada", [
            'jornada_id' => $jornadaId,
            'maquina_id' => $jornada->maquina_id,
            'total_producido' => $jornada->total_unidades_producidas,
            'objetivo' => $jornada->objetivo_unidades_copiado,
        ]);

        return $jornada;
    }

    /**
     * Pausa una jornada de producción
     * 
     * Proceso:
     * 1. Valida que la jornada esté en 'running'
     * 2. Crea evento de parada con motivo 'pausa_supervisor'
     * 3. Sets status a 'paused'
     * 4. Dispara evento WebSocket
     * 5. Registra en auditoría
     *
     * @param string $jornadaId UUID de la jornada
     * @param string $motivo Razón de la pausa (opcional)
     * @return JornadaProduccion La jornada pausada
     * @throws \Exception Si la jornada no está en 'running'
     */
    public function pausarJornada(string $jornadaId, string $motivo = null): JornadaProduccion
    {
        $jornada = JornadaProduccion::findOrFail($jornadaId);

        if ($jornada->status !== 'running') {
            throw new \Exception("No se puede pausar una jornada en estado: {$jornada->status}");
        }

        // Crear evento de parada
        EventoParadaJornada::create([
            'jornada_id' => $jornadaId,
            'motivo' => 'pausa_supervisor',
            'inicio_parada' => now(),
            'comentarios' => $motivo,
        ]);

        // Actualizar estado de jornada
        $jornada->update(['status' => 'paused']);

        // TODO: Disparar evento WebSocket para actualizar dashboard

        // Log en auditoría
        Log::info("Jornada pausada", [
            'jornada_id' => $jornadaId,
            'maquina_id' => $jornada->maquina_id,
            'motivo' => $motivo,
        ]);

        return $jornada;
    }

    /**
     * Reanuda una jornada que fue pausada
     * 
     * Proceso:
     * 1. Valida que la jornada esté en 'paused'
     * 2. Busca el último evento de parada abierto
     * 3. Cierra el evento (sets fin_parada)
     * 4. Sets status a 'running'
     * 5. Dispara evento WebSocket
     * 6. Registra en auditoría
     *
     * @param string $jornadaId UUID de la jornada
     * @return JornadaProduccion La jornada reanudada
     * @throws \Exception Si la jornada no está en 'paused'
     */
    public function reanudarJornada(string $jornadaId): JornadaProduccion
    {
        $jornada = JornadaProduccion::findOrFail($jornadaId);

        if ($jornada->status !== 'paused') {
            throw new \Exception("No se puede reanudar una jornada en estado: {$jornada->status}");
        }

        // Buscar evento de parada abierto más reciente
        $eventoAbierto = EventoParadaJornada::where('jornada_id', $jornadaId)
            ->whereNull('fin_parada')
            ->latest('created_at')
            ->first();

        if ($eventoAbierto) {
            $eventoAbierto->update(['fin_parada' => now()]);
        }

        // Reanudar jornada
        $jornada->update(['status' => 'running']);

        // TODO: Disparar evento WebSocket para actualizar dashboard

        // Log en auditoría
        Log::info("Jornada reanudada", [
            'jornada_id' => $jornadaId,
            'maquina_id' => $jornada->maquina_id,
        ]);

        return $jornada;
    }

    /**
     * Obtiene la jornada activa de una máquina
     *
     * @param string $maquinaId UUID de la máquina
     * @return JornadaProduccion|null La jornada activa o null
     */
    public function obtenerJornadaActiva(string $maquinaId): ?JornadaProduccion
    {
        return JornadaProduccion::where('maquina_id', $maquinaId)
            ->where('status', 'running')
            ->first();
    }

    /**
     * Verifica si hay eventos de parada abiertos en una jornada
     *
     * @param string $jornadaId UUID de la jornada
     * @return bool True si hay paradas abiertas
     */
    public function hayParadasAbiertas(string $jornadaId): bool
    {
        return EventoParadaJornada::where('jornada_id', $jornadaId)
            ->whereNull('fin_parada')
            ->exists();
    }
}
