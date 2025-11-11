<?php

namespace App\Services\Contracts;

use App\Models\RegistroProduccion;

/**
 * Produccion Service Interface
 * 
 * Define el contrato para la gestión de registros de producción.
 */
interface ProduccionServiceInterface
{
    /**
     * Registra un evento de producción de la máquina
     * 
     * Actualiza automáticamente los contadores en jornadas_produccion
     * y verifica límites de fallos críticos.
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
    ): RegistroProduccion;

    /**
     * Verifica si se ha alcanzado el límite de fallos críticos
     *
     * @param string $jornadaId UUID de la jornada
     * @return bool True si se alcanzó el límite
     */
    public function verificarLimiteFallos(string $jornadaId): bool;

    /**
     * Detiene automáticamente la máquina por límite de fallos críticos
     *
     * @param string $jornadaId UUID de la jornada
     * @return void
     * @throws \Exception Si falla la detención
     */
    public function detenerPorFallosCriticos(string $jornadaId): void;
}
