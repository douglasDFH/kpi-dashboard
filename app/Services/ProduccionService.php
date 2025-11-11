<?php

namespace App\Services;

use App\Services\Contracts\ProduccionServiceInterface;
use App\Models\RegistroProduccion;
use App\Models\JornadaProduccion;
use App\Models\EventoParadaJornada;
use Illuminate\Support\Facades\Log;

/**
 * Produccion Service
 * 
 * Servicio de gestión de registros de producción.
 * 
 * Responsabilidades:
 * - Registrar producción: Crear RegistroProduccion y actualizar jornadas_produccion
 * - Verificar límites: Validar límites de fallos críticos
 * - Detener automáticamente: Crear evento de parada si se excede el límite
 * 
 * Schema Referencias:
 * - registros_produccion: Registra cantidad_producida, cantidad_buena, cantidad_mala
 * - jornadas_produccion: Agrega totales: total_unidades_producidas, total_unidades_buenas, total_unidades_malas
 * - eventos_parada_jornada: Crea evento con motivo 'falla_critica_qa' si se supera el límite
 */
class ProduccionService implements ProduccionServiceInterface
{
    /**
     * Registra un evento de producción de la máquina
     * 
     * Proceso:
     * 1. Valida que exista una jornada activa
     * 2. Crea RegistroProduccion con los datos
     * 3. Actualiza contadores en JornadaProduccion (usando increment)
     * 4. Verifica límite de fallos críticos
     * 5. Si se supera, detiene automáticamente la máquina
     * 6. Dispara evento WebSocket
     * 7. Registra en auditoría
     *
     * @param string $maquinaId UUID de la máquina
     * @param int $cantidadProducida Cantidad producida
     * @param int $cantidadBuena Cantidad sin defectos
     * @param int $cantidadMala Cantidad con defectos
     * @return RegistroProduccion El registro creado
     * @throws \Exception Si no hay jornada activa
     */
    public function registrarProduccion(
        string $maquinaId,
        int $cantidadProducida,
        int $cantidadBuena,
        int $cantidadMala
    ): RegistroProduccion {
        // Buscar jornada activa
        $jornada = JornadaProduccion::where('maquina_id', $maquinaId)
            ->where('status', 'running')
            ->first();

        if (!$jornada) {
            throw new \Exception("No hay jornada activa para la máquina: {$maquinaId}");
        }

        // Validar que la suma coincida
        if ($cantidadBuena + $cantidadMala !== $cantidadProducida) {
            throw new \Exception("Validación fallida: cantidad_buena + cantidad_mala debe ser igual a cantidad_producida");
        }

        // Crear registro de producción
        $registro = RegistroProduccion::create([
            'jornada_id' => $jornada->id,
            'maquina_id' => $maquinaId,
            'cantidad_producida' => $cantidadProducida,
            'cantidad_buena' => $cantidadBuena,
            'cantidad_mala' => $cantidadMala,
        ]);

        // Actualizar contadores de la jornada (usando query builder para persistencia inmediata)
        JornadaProduccion::where('id', $jornada->id)->increment('total_unidades_producidas', $cantidadProducida);
        JornadaProduccion::where('id', $jornada->id)->increment('total_unidades_buenas', $cantidadBuena);
        JornadaProduccion::where('id', $jornada->id)->increment('total_unidades_malas', $cantidadMala);

        // Obtener jornada fresca con los valores actualizados
        $jornada = JornadaProduccion::findOrFail($jornada->id);

        // Verificar límite de fallos
        if ($jornada->total_unidades_malas >= $jornada->limite_fallos_critico_copiado) {
            $this->detenerPorFallosCriticos($jornada->id);
        }

        // TODO: Disparar evento WebSocket para actualizar dashboard

        // Log en auditoría
        Log::info("Producción registrada", [
            'jornada_id' => $jornada->id,
            'maquina_id' => $maquinaId,
            'cantidad_producida' => $cantidadProducida,
            'cantidad_buena' => $cantidadBuena,
            'cantidad_mala' => $cantidadMala,
        ]);

        return $registro;
    }

    /**
     * Verifica si se ha alcanzado el límite de fallos críticos
     *
     * Comparar: total_unidades_malas >= limite_fallos_critico_copiado
     *
     * @param string $jornadaId UUID de la jornada
     * @return bool True si se alcanzó el límite
     */
    public function verificarLimiteFallos(string $jornadaId): bool
    {
        $jornada = JornadaProduccion::findOrFail($jornadaId);

        return $jornada->total_unidades_malas >= $jornada->limite_fallos_critico_copiado;
    }

    /**
     * Detiene automáticamente la máquina por límite de fallos críticos
     * 
     * Proceso:
     * 1. Actualiza estado de jornada a 'stopped_critical'
     * 2. Crea evento de parada con motivo 'falla_critica_qa'
     * 3. Cierra eventos de parada abiertos
     * 4. Dispara evento WebSocket
     * 5. Registra en auditoría
     *
     * @param string $jornadaId UUID de la jornada
     * @return void
     * @throws \Exception Si falla la detención
     */
    public function detenerPorFallosCriticos(string $jornadaId): void
    {
        $jornada = JornadaProduccion::findOrFail($jornadaId);

        // Crear evento de parada por falla crítica
        EventoParadaJornada::create([
            'jornada_id' => $jornadaId,
            'motivo' => 'falla_critica_qa',
            'inicio_parada' => now(),
        ]);

        // Actualizar estado de jornada
        $jornada->update(['status' => 'stopped_critical']);

        // TODO: Disparar evento WebSocket para alertar en dashboard

        // Log en auditoría
        Log::warning("Máquina detenida por fallos críticos", [
            'jornada_id' => $jornadaId,
            'maquina_id' => $jornada->maquina_id,
            'total_fallos' => $jornada->total_unidades_malas,
            'limite' => $jornada->limite_fallos_critico_copiado,
        ]);
    }
}
