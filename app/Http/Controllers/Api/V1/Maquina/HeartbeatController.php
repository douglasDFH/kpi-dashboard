<?php

namespace App\Http\Controllers\Api\V1\Maquina;

use App\Http\Controllers\Controller;
use App\Models\Maquina;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class HeartbeatController extends Controller
{
    /**
     * Keep-alive (heartbeat) desde la máquina
     *
     * POST /api/v1/maquina/heartbeat
     *
     * Actualiza el timestamp de última comunicación
     */
    public function ping(): JsonResponse
    {
        try {
            // Obtener máquina autenticada
            $maquina = auth('sanctum')->user();

            if (! $maquina || ! $maquina instanceof Maquina) {
                return response()->json([
                    'success' => false,
                    'message' => 'Máquina no autenticada',
                ], 401);
            }

            // Actualizar updated_at (touch)
            $maquina->touch();

            return response()->json([
                'success' => true,
                'message' => 'Heartbeat recibido',
                'data' => [
                    'maquina_id' => $maquina->id,
                    'status' => $maquina->status,
                    'timestamp' => now()->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error en heartbeat de máquina', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error en heartbeat: '.$e->getMessage(),
            ], 500);
        }
    }
}
