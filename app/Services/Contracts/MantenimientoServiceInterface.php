<?php

namespace App\Services\Contracts;

use App\Models\RegistroMantenimiento;

/**
 * Mantenimiento Service Interface
 *
 * Define el contrato para la gestión de mantenimientos.
 */
interface MantenimientoServiceInterface
{
    /**
     * Registra un evento de mantenimiento
     *
     * @param  string  $maquinaId  UUID de la máquina
     * @param  string  $supervisorId  ID del usuario supervisor
     * @param  string  $tipo  Tipo de mantenimiento (preventivo, correctivo, calibracion)
     * @param  string  $descripcion  Descripción del mantenimiento
     * @param  string|null  $jornadaId  UUID de la jornada (opcional)
     * @return RegistroMantenimiento El registro creado
     */
    public function registrarMantenimiento(
        string $maquinaId,
        string $supervisorId,
        string $tipo,
        string $descripcion,
        ?string $jornadaId = null
    ): RegistroMantenimiento;

    /**
     * Obtiene historial de mantenimientos de una máquina
     *
     * @param  string  $maquinaId  UUID de la máquina
     * @param  int  $limit  Límite de registros
     */
    public function obtenerHistorial(string $maquinaId, int $limit = 50): \Illuminate\Database\Eloquent\Collection;
}
