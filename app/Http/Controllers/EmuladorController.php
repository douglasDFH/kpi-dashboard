<?php

namespace App\Http\Controllers;

use App\Models\JornadaProduccion;
use App\Models\Maquina;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class EmuladorController extends Controller
{
    /**
     * Muestra la interfaz del emulador
     */
    public function index(): View
    {
        $maquinas = Maquina::with(['area', 'jornadasProduccion' => function ($query) {
            $query->where('status', 'running')->latest();
        }])->get();

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
            // Obtener máquina y token
            $maquina = Maquina::with('tokens')->findOrFail($request->maquina_id);
            $token = $maquina->tokens->first();

            if (! $token) {
                return response()->json([
                    'success' => false,
                    'message' => 'La máquina no tiene token Sanctum',
                ], 400);
            }

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

            // Enviar a la API
            $response = Http::withToken($token->plainTextToken ?? $token->token)
                ->post(url('/api/v1/maquina/produccion'), [
                    'cantidad_producida' => $request->cantidad_producida,
                    'cantidad_buena' => $request->cantidad_buena,
                    'cantidad_mala' => $request->cantidad_mala,
                ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producción enviada exitosamente',
                    'data' => $response->json(),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de API: ' . $response->body(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
