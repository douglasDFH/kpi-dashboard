<?php

namespace App\Http\Controllers\Api\V1\Maquina;

use App\Events\MaquinaConectada;
use App\Http\Controllers\Controller;
use App\Models\Maquina;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatusController extends Controller
{
    /**
     * Actualiza el estado de la máquina
     *
     * PUT /api/v1/maquina/status
     */
    public function update(Request $request): JsonResponse
    {
        try {
            // Obtener máquina autenticada del middleware
            $maquina = $request->input('maquina');

            if (! $maquina || ! $maquina instanceof Maquina) {
                return response()->json([
                    'success' => false,
                    'message' => 'Máquina no autenticada',
                ], 401);
            }

            // Validar estado
            $request->validate([
                'status' => 'required|in:running,stopped,maintenance,idle',
            ]);

            // Actualizar estado
            $maquina->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'data' => [
                    'maquina_id' => $maquina->id,
                    'status' => $maquina->status,
                    'timestamp' => now()->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando estado de máquina', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Registra que la máquina se ha conectado
     *
     * POST /api/v1/maquina/conectar
     */
    public function conectar(Request $request): JsonResponse
    {
        try {
            // Obtener máquina autenticada del middleware
            $maquina = $request->input('maquina');

            if (! $maquina || ! $maquina instanceof Maquina) {
                return response()->json([
                    'success' => false,
                    'message' => 'Máquina no autenticada',
                ], 401);
            }

            // Broadcast evento de conexión
            broadcast(new MaquinaConectada($maquina));

            return response()->json([
                'success' => true,
                'message' => 'Máquina conectada exitosamente',
                'data' => [
                    'maquina_id' => $maquina->id,
                    'timestamp' => now()->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error conectando máquina', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al conectar máquina: '.$e->getMessage(),
            ], 500);
        }
    }
}
