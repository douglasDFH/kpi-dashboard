<?php

namespace App\Http\Controllers\Api\V1\Maquina;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegistrarProduccionRequest;
use App\Services\Contracts\ProduccionServiceInterface;
use App\Models\Maquina;
use App\Models\JornadaProduccion;
use Illuminate\Http\JsonResponse;

class ProduccionController extends Controller
{
    private ProduccionServiceInterface $produccionService;

    public function __construct(ProduccionServiceInterface $produccionService)
    {
        $this->produccionService = $produccionService;
    }

    /**
     * Registra producción desde la máquina (Caso de Uso 3)
     * 
     * POST /api/v1/maquina/produccion
     * 
     * Recibe: cantidad_producida, cantidad_buena, cantidad_mala
     * Delega lógica: ProduccionService::registrarProduccion()
     * Retorna: registro creado + datos actualizados de jornada
     * 
     * @param RegistrarProduccionRequest $request
     * @return JsonResponse
     */
    public function store(RegistrarProduccionRequest $request): JsonResponse
    {
        try {
            // Obtener máquina autenticada
            $maquina = auth('sanctum')->user();

            // Delegar al servicio (caso de uso 3: máquina registra producción)
            $registro = $this->produccionService->registrarProduccion(
                maquinaId: $maquina->id,
                cantidadProducida: $request->cantidad_producida,
                cantidadBuena: $request->cantidad_buena,
                cantidadMala: $request->cantidad_mala
            );

            // Obtener jornada actualizada para respuesta
            $jornada = $registro->jornada;

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
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
