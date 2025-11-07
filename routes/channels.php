<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal público para el dashboard de KPIs
Broadcast::channel('kpi-dashboard', function () {
    return true;
});
