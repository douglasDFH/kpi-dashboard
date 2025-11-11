<?php

namespace App\Http\Middleware;

use App\Models\Maquina;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMaquina
{
    /**
     * Autentica una máquina usando el header personalizado X-Machine-Token
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener token de configuración
        $expectedToken = config('services.machine.auth_token');
        $headerName = config('services.machine.auth_header', 'X-Machine-Token');

        // Obtener token del header
        $providedToken = $request->header($headerName);

        // Validar token
        if (! $providedToken || $providedToken !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado. Token de máquina inválido.',
            ], 401);
        }

        // Validar que maquina_id esté presente en el request
        $maquinaId = $request->input('maquina_id');
        
        if (! $maquinaId) {
            return response()->json([
                'success' => false,
                'message' => 'maquina_id es requerido',
            ], 400);
        }

        // Buscar máquina
        $maquina = Maquina::find($maquinaId);

        if (! $maquina) {
            return response()->json([
                'success' => false,
                'message' => 'Máquina no encontrada',
            ], 404);
        }

        // Agregar máquina al request para uso en el controlador
        $request->merge(['maquina' => $maquina]);

        return $next($request);
    }
}
