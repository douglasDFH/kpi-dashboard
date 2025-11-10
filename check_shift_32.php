<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkShift;
use App\Models\ProductionPlan;

echo "=== Verificando WorkShift #32 ===\n\n";

$shift = WorkShift::with('plan', 'equipment')->find(32);

if (!$shift) {
    echo "❌ WorkShift #32 NO EXISTE\n";
    exit;
}

echo "✅ WorkShift #32 EXISTE\n\n";
echo "ID: {$shift->id}\n";
echo "Equipment: {$shift->equipment->name} (ID: {$shift->equipment_id})\n";
echo "Status: {$shift->status}\n";
echo "Shift Type: {$shift->shift_type}\n";
echo "Start Time: {$shift->start_time}\n";
echo "End Time: " . ($shift->end_time ?? 'NULL (activo)') . "\n";
echo "\n";

echo "=== Plan Asociado ===\n";
echo "plan_id: " . ($shift->plan_id ?? 'NULL') . "\n";

if ($shift->plan_id) {
    $plan = $shift->plan;
    if ($plan) {
        echo "✅ Plan #{$plan->id} encontrado\n";
        echo "Producto: {$plan->product_name}\n";
        echo "Cantidad objetivo: {$plan->target_quantity}\n";
        echo "Status: {$plan->status}\n";
    } else {
        echo "❌ Plan no encontrado (eliminado?)\n";
    }
} else {
    echo "⚠️ Sin plan asociado\n";
}

echo "\n=== target_snapshot ===\n";
echo "Valor: ";
if ($shift->target_snapshot) {
    echo json_encode($shift->target_snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n\n";
    
    echo "Campos disponibles:\n";
    foreach ($shift->target_snapshot as $key => $value) {
        echo "  - {$key}: {$value}\n";
    }
} else {
    echo "NULL o VACÍO\n";
    echo "❌ PROBLEMA: target_snapshot está vacío\n";
}

echo "\n=== Producción Actual ===\n";
echo "actual_production: {$shift->actual_production}\n";
echo "good_units: {$shift->good_units}\n";
echo "defective_units: {$shift->defective_units}\n";

echo "\n=== Diagnóstico ===\n";
if (!$shift->target_snapshot || empty($shift->target_snapshot)) {
    echo "🔴 PROBLEMA ENCONTRADO:\n";
    echo "   El target_snapshot está vacío. Esto significa que:\n";
    echo "   1. La jornada se creó sin plan asociado, O\n";
    echo "   2. El plan se asoció después de iniciar la jornada\n";
    echo "   3. El snapshot no se guardó correctamente al crear la jornada\n\n";
    
    if ($shift->plan_id) {
        echo "   SOLUCIÓN: Actualizar el target_snapshot con datos del plan actual\n";
        echo "   Plan asociado: #{$shift->plan_id}\n";
    } else {
        echo "   SOLUCIÓN: Asociar un plan o agregar datos manualmente\n";
    }
} else {
    echo "✅ target_snapshot tiene datos correctos\n";
}
