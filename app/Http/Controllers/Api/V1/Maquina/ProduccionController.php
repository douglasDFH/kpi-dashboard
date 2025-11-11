<?php

namespace App\Http\Controllers\Api\V1\Maquina;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegistrarProduccionRequest;
use App\Models\Maquina;
use App\Models\JornadaProduccion;
use App\Models\RegistroProduccion;
use App\Models\EventoParadaJornada;
use App\Events\KpiDashboard\V1\ProduccionRegistrada;
use App\Events\KpiDashboard\V1\MaquinaDetenidaCritica;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProduccionController extends Controller
{
    /**
     * Registra producción desde la máquina
     * 
     * POST /api/v1/maquina/produccion
     * 
     * Flujo:
     * 1. Obtener máquina autenticada vía token Sanctum
     * 2. Buscar jornada activa (running)
     * 3. Crear registro en registros_produccion
     * 4. Actualizar contadores en jornadas_produccion
     * 5. Verificar límite de fallos (parada automática)
     * 6. Disparar evento WebSocket (Broadcast)
     * 
     * @param RegistrarProduccionRequest $request
     * @return JsonResponse
     */
    public function store(RegistrarProduccionRequest $request): JsonResponse
    {
        try {
            // 1. Obtener máquina autenticada
            $maquina = auth('sanctum')->user();
            
            if (!$maquina || !$maquina instanceof Maquina) {
                return response()->json([
                    'success' => false,
                    'message' => 'Máquina no autenticada',
                ], 401);
            }

            // 2. Buscar jornada activa
            $jornada = JornadaProduccion::where('maquina_id', $maquina->id)
                ->where('status', 'running')
                ->latest()
                ->first();

            if (!$jornada) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay jornada activa para esta máquina',
                ], 400);
            }

            // 3. Crear registro de producción
            $registro = RegistroProduccion::create([
                'jornada_id' => $jornada->id,
                'maquina_id' => $maquina->id,
                'cantidad_producida' => $request->cantidad_producida,
                'cantidad_buena' => $request->cantidad_buena,
                'cantidad_mala' => $request->cantidad_mala,
            ]);

            // 4. Actualizar contadores de la jornada
            $jornada->update([
                'total_unidades_producidas' => $jornada->total_unidades_producidas + $request->cantidad_producida,
                'total_unidades_buenas' => $jornada->total_unidades_buenas + $request->cantidad_buena,
                'total_unidades_malas' => $jornada->total_unidades_malas + $request->cantidad_mala,
            ]);

            // 5. Verificar límite de fallos críticos
            if ($jornada->total_unidades_malas >= $jornada->limite_fallos_critico_copiado) {
                // Actualizar jornada a estado crítico
                $jornada->update(['status' => 'stopped_critical']);

                // Crear evento de parada automática
                EventoParadaJornada::create([
                    'jornada_id' => $jornada->id,
                    'motivo' => 'falla_critica_qa',
                    'inicio_parada' => now(),
                    'comentarios' => 'Parada automática por límite de fallos críticos',
                ]);

                // Broadcast evento de parada crítica
                broadcast(new MaquinaDetenidaCritica(
                    $maquina->id,
                    [
                        'status' => 'stopped_critical',
                        'motivo' => 'falla_critica_qa',
                        'total_fallos' => $jornada->total_unidades_malas,
                        'limite' => $jornada->limite_fallos_critico_copiado,
                        'timestamp' => now()->toISOString(),
                    ]
                ));
            }

            // 6. Disparar evento WebSocket (Broadcast)
            broadcast(new ProduccionRegistrada(
                $maquina->id,
                [
                    'total_unidades_producidas' => $jornada->total_unidades_producidas,
                    'total_unidades_buenas' => $jornada->total_unidades_buenas,
                    'total_unidades_malas' => $jornada->total_unidades_malas,
                    'progreso' => ($jornada->total_unidades_producidas / $jornada->objetivo_unidades_copiado) * 100,
                    'timestamp' => now()->toISOString(),
                ]
            ));

            return response()->json([
                'success' => true,
                'message' => 'Producción registrada exitosamente',
                'data' => [
                    'registro_id' => $registro->id,
                    'jornada' => [
                        'id' => $jornada->id,
                        'total_producidas' => $jornada->total_unidades_producidas,
                        'total_buenas' => $jornada->total_unidades_buenas,
                        'total_malas' => $jornada->total_unidades_malas,
                        'progreso' => ($jornada->total_unidades_producidas / $jornada->objetivo_unidades_copiado) * 100,
                        'status' => $jornada->status,
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error registrando producción', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar producción: ' . $e->getMessage(),
            ], 500);
        }
    }
}
