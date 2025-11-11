<?php

namespace App\Services;

use App\Models\Maquina;
use App\Models\RegistroMantenimiento;
use App\Services\Contracts\MantenimientoServiceInterface;
use Illuminate\Support\Facades\Log;

/**
 * Mantenimiento Service
 *
 * Servicio de gestión de registros de mantenimiento.
 *
 * Responsabilidades:
 * - Registrar mantenimientos: Crear RegistroMantenimiento
 * - Obtener historial: Consultar mantenimientos por máquina
 *
 * Schema Referencias:
 * - registros_mantenimiento: Registra tipo (preventivo, correctivo, calibracion)
 */
class MantenimientoService implements MantenimientoServiceInterface
{
    /**
     * Registra un evento de mantenimiento
     *
     * Proceso:
     * 1. Valida máquina existe
     * 2. Crea RegistroMantenimiento
     * 3. Vincula con jornada si se proporciona
     * 4. Registra en auditoría
     *
     * @param  string  $maquinaId  UUID de la máquina
     * @param  string  $supervisorId  ID del usuario supervisor
     * @param  string  $tipo  Tipo de mantenimiento (preventivo, correctivo, calibracion)
     * @param  string  $descripcion  Descripción del mantenimiento
     * @param  string|null  $jornadaId  UUID de la jornada (opcional)
     * @return RegistroMantenimiento El registro creado
     *
     * @throws \Exception Si la máquina no existe
     */
    public function registrarMantenimiento(
        string $maquinaId,
        string $supervisorId,
        string $tipo,
        string $descripcion,
        ?string $jornadaId = null
    ): RegistroMantenimiento {
        // Validar que la máquina existe
        $maquina = Maquina::findOrFail($maquinaId);

        // Validar tipo
        $tiposValidos = ['preventivo', 'correctivo', 'calibracion'];
        if (! in_array($tipo, $tiposValidos)) {
            throw new \Exception("Tipo de mantenimiento inválido: {$tipo}");
        }

        // Crear registro de mantenimiento
        $registro = RegistroMantenimiento::create([
            'maquina_id' => $maquinaId,
            'supervisor_id' => $supervisorId,
            'jornada_id' => $jornadaId,
            'tipo' => $tipo,
            'descripcion' => $descripcion,
        ]);

        // Log en auditoría
        Log::info('Mantenimiento registrado', [
            'registro_id' => $registro->id,
            'maquina_id' => $maquinaId,
            'supervisor_id' => $supervisorId,
            'tipo' => $tipo,
            'jornada_id' => $jornadaId,
        ]);

        return $registro;
    }

    /**
     * Obtiene historial de mantenimientos de una máquina
     *
     * Consulta registros ordenados por fecha descendente (más recientes primero).
     *
     * @param  string  $maquinaId  UUID de la máquina
     * @param  int  $limit  Límite de registros (default: 50)
     */
    public function obtenerHistorial(string $maquinaId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return RegistroMantenimiento::where('maquina_id', $maquinaId)
            ->with('jornada', 'supervisor')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
