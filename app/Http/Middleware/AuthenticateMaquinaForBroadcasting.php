<?php

namespace App\Http\Middleware;

use App\Models\Maquina;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticateMaquinaForBroadcasting
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Intentar autenticar como usuario normal primero
        if (Auth::check()) {
            return $next($request);
        }

        // 2. Verificar si viene con token de máquina en header personalizado
        $machineToken = $request->header(config('services.machine.auth_header', 'X-Machine-Token'));
        if ($machineToken) {
            $expectedToken = config('services.machine.auth_token');

            if ($machineToken === $expectedToken) {
                // Token válido, obtener ID de máquina del request
                $maquinaId = $request->input('maquina_id') ?? $request->route('maquina_id');

                if ($maquinaId) {
                    $maquina = Maquina::find($maquinaId);
                    if ($maquina) {
                        Auth::setUser($maquina);

                        return $next($request);
                    }
                }
            }
        }

        // 3. Si viene con token Sanctum (Bearer), autenticar la máquina
        $token = $request->bearerToken();
        if ($token) {
            $personalAccessToken = PersonalAccessToken::findToken($token);

            if ($personalAccessToken) {
                // Establecer la máquina como usuario autenticado
                Auth::setUser($personalAccessToken->tokenable);

                return $next($request);
            }
        }

        return $next($request);
    }
}
