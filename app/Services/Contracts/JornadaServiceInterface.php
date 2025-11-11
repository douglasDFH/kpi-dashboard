<?php

namespace App\Services\Contracts;

use App\Models\JornadaProduccion;

/**
 * Jornada Service Interface
 * 
 * Define el contrato para la gestión de jornadas de producción.
 */
interface JornadaServiceInterface
{
    /**
     * Inicia una nueva jornada de producción
     *
     * @param string $maquinaId UUID de la máquina
     * @param string $supervisorId ID del usuario supervisor
     * @return JornadaProduccion La jornada creada
     * @throws \Exception Si no hay plan activo
     */
    public function iniciarJornada(string $maquinaId, string $supervisorId): JornadaProduccion;

    /**
     * Finaliza una jornada de producción
     *
     * @param string $jornadaId UUID de la jornada
     * @return JornadaProduccion La jornada finalizada
     * @throws \Exception Si la jornada no está en estado correcto
     */
    public function finalizarJornada(string $jornadaId): JornadaProduccion;

    /**
     * Pausa una jornada de producción
     *
     * @param string $jornadaId UUID de la jornada
     * @param string|null $motivo Razón de la pausa (opcional)
     * @return JornadaProduccion La jornada pausada
     * @throws \Exception Si la jornada no está en 'running'
     */
    public function pausarJornada(string $jornadaId, ?string $motivo = null): JornadaProduccion;

    /**
     * Reanuda una jornada que fue pausada
     *
     * @param string $jornadaId UUID de la jornada
     * @return JornadaProduccion La jornada reanudada
     * @throws \Exception Si la jornada no está en 'paused'
     */
    public function reanudarJornada(string $jornadaId): JornadaProduccion;

    /**
     * Obtiene la jornada activa de una máquina
     *
     * @param string $maquinaId UUID de la máquina
     * @return JornadaProduccion|null La jornada activa o null
     */
    public function obtenerJornadaActiva(string $maquinaId): ?JornadaProduccion;

    /**
     * Verifica si hay eventos de parada abiertos en una jornada
     *
     * @param string $jornadaId UUID de la jornada
     * @return bool True si hay paradas abiertas
     */
    public function hayParadasAbiertas(string $jornadaId): bool;
}
