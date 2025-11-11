<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Procesar el login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Actualizar último login
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->updateLastLogin();

            // Registrar en auditoría
            AuditLog::logAction(
                'login',
                null,
                null,
                "Usuario {$user->name} inició sesión",
                [],
                []
            );

            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('Las credenciales proporcionadas no coinciden con nuestros registros.'),
        ]);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Registrar en auditoría
        if ($user) {
            AuditLog::logAction(
                'logout',
                null,
                null,
                "Usuario {$user->name} cerró sesión",
                [],
                []
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
