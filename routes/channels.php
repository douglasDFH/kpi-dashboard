<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal público para el dashboard de KPIs
Broadcast::channel('kpi-dashboard.v1', function ($user) {
    // Si es máquina (Maquina model) o admin/superadmin
    if ($user instanceof \App\Models\Maquina) {
        return true;
    }

    if (method_exists($user, 'hasRole')) {
        return $user->hasRole('admin') || $user->hasRole('superadmin');
    }

    return false;
});
