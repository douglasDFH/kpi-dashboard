<?php

namespace App\Http\Controllers\Api\V1\Maquina;

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
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        try {
            // Obtener máquina autenticada
            $maquina = auth('sanctum')->user();
            
            if (!$maquina || !$maquina instanceof Maquina) {
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
                'message' => 'Error al actualizar estado: ' . $e->getMessage(),
            ], 500);
        }
    }
}
