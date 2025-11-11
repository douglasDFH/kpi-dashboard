<?php

namespace App\Http\Controllers;

use App\Events\MaquinaConectada;
use App\Models\JornadaProduccion;
use App\Models\Maquina;
use App\Services\Contracts\ProduccionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmuladorController extends Controller
{
    private ProduccionServiceInterface $produccionService;

    public function __construct(ProduccionServiceInterface $produccionService)
    {
        $this->produccionService = $produccionService;
    }

    /**
     * Muestra la interfaz del emulador para UNA máquina específica
     */
    public function show(string $maquinaId): View
    {
        $maquina = Maquina::with(['area', 'jornadasProduccion' => function ($query) {
            $query->whereIn('status', ['pending', 'running'])->latest();
        }])->findOrFail($maquinaId);

        return view('emulador.show', [
            'maquina' => $maquina,
        ]);
    }

    /**
     * Lista todas las máquinas para seleccionar
     */
    public function index(): View
    {
        $maquinas = Maquina::with(['area'])->get();

        return view('emulador.index', [
            'maquinas' => $maquinas,
        ]);
    }

    /**
     * Emula una producción manual desde la interfaz web
     */
    public function emular(Request $request): JsonResponse
    {
        $request->validate([
            'maquina_id' => 'required|uuid|exists:maquinas,id',
            'cantidad_producida' => 'required|integer|min:1',
            'cantidad_buena' => 'required|integer|min:0',
            'cantidad_mala' => 'required|integer|min:0',
        ]);

        try {
            // Obtener máquina
            $maquina = Maquina::findOrFail($request->maquina_id);

            // Verificar jornada activa
            $jornada = JornadaProduccion::where('maquina_id', $maquina->id)
                ->where('status', 'running')
                ->first();

            if (! $jornada) {
                return response()->json([
                    'success' => false,
                    'message' => 'La máquina no tiene jornada activa',
                ], 400);
            }

            // Registrar producción usando el servicio directamente
            $registro = $this->produccionService->registrarProduccion(
                maquinaId: $maquina->id,
                cantidadProducida: $request->cantidad_producida,
                cantidadBuena: $request->cantidad_buena,
                cantidadMala: $request->cantidad_mala
            );

            // Obtener jornada actualizada
            $jornada = $registro->jornada->fresh();

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
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Conecta una máquina emulada
     */
    public function conectar(Request $request): JsonResponse
    {
        $request->validate([
            'maquina_id' => 'required|uuid|exists:maquinas,id',
        ]);

        try {
            // Obtener máquina
            $maquina = Maquina::findOrFail($request->maquina_id);

            // Broadcast evento de conexión directamente
            broadcast(new \App\Events\MaquinaConectada($maquina));

            return response()->json([
                'success' => true,
                'message' => 'Máquina conectada exitosamente',
                'data' => [
                    'maquina_id' => $maquina->id,
                    'nombre' => $maquina->nombre,
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
