<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductionPlan;

echo "=== Verificando Plan #46 ===\n\n";

$plan = ProductionPlan::find(46);

if ($plan) {
    echo "✅ Plan #46 EXISTE\n";
    echo "ID: {$plan->id}\n";
    echo "Status: {$plan->status}\n";
    echo "Producto: {$plan->product_name}\n";
    echo "Equipment ID: {$plan->equipment_id}\n";
    echo "Equipment Name: {$plan->equipment->name}\n";
    echo "Cantidad: {$plan->target_quantity}\n";
    echo "Fecha Inicio: {$plan->start_date}\n";
    echo "Fecha Fin: {$plan->end_date}\n";
    echo "Turno: {$plan->shift}\n";
    echo "\n";
} else {
    echo "❌ Plan #46 NO EXISTE en la base de datos\n\n";
}

echo "=== Planes disponibles para work-shifts ===\n";
echo "(Status: active o pending)\n\n";

$availablePlans = ProductionPlan::whereIn('status', ['active', 'pending'])
    ->with('equipment')
    ->orderBy('id', 'desc')
    ->get();

echo "Total de planes disponibles: " . $availablePlans->count() . "\n\n";

foreach ($availablePlans as $p) {
    echo "Plan #{$p->id} - {$p->product_name} ({$p->shift}) - Status: {$p->status} - Equipo: {$p->equipment->name}\n";
}

echo "\n=== Todos los planes en BD ===\n\n";

$allPlans = ProductionPlan::orderBy('id', 'desc')->limit(10)->get();

foreach ($allPlans as $p) {
    echo "Plan #{$p->id} - Status: {$p->status} - Producto: {$p->product_name}\n";
}
