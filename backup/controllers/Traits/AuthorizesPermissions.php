<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Auth;

trait AuthorizesPermissions
{
    /**
     * Verificar si el usuario actual tiene un permiso específico
     */
    protected function authorizePermission(string $permission, ?string $errorMessage = null): void
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->hasPermission($permission)) {
            $message = $errorMessage ?? 'No tienes permiso para realizar esta acción.';
            abort(403, $message);
        }
    }
}
