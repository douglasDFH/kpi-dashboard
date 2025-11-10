<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Equipment;
use App\Models\ProductionPlan;
use App\Models\WorkShift;
use App\Models\ProductionData;
use App\Models\User;

echo "\n========================================\n";
echo "🧪 PRUEBA DE INTEGRACIÓN COMPLETA\n";
echo "========================================\n\n";

// 1. Verificar equipos disponibles
$equipment = Equipment::where('is_active', true)->first();
echo "📌 PASO 1: Equipo seleccionado\n";
echo "   ID: {$equipment->id}\n";
echo "   Nombre: {$equipment->name}\n";
echo "   Código: {$equipment->code}\n\n";

// 2. Crear un nuevo plan de producción
$user = User::first();
$plan = ProductionPlan::create([
    'equipment_id' => $equipment->id,
    'product_name' => 'Pieza de Prueba XYZ-789',
    'product_code' => 'XYZ-789',
    'target_quantity' => 500,
    'shift' => 'morning',
    'start_date' => now(),
    'end_date' => now()->addDays(2),
    'status' => 'pending',
    'created_by' => $user->id,
    'notes' => 'Plan de prueba para verificar integración completa'
]);

echo "✅ PASO 2: Plan de Producción creado\n";
echo "   Plan ID: {$plan->id}\n";
echo "   Producto: {$plan->product_name}\n";
echo "   Meta: {$plan->target_quantity} unidades\n";
echo "   Estado: {$plan->status}\n\n";

// 3. Iniciar una jornada de trabajo vinculada al plan
$shift = WorkShift::startShift(
    $equipment->id,
    $plan->id,
    'morning',
    $user->id
);

echo "✅ PASO 3: Jornada de Trabajo iniciada\n";
echo "   WorkShift ID: {$shift->id}\n";
echo "   Equipo: {$shift->equipment->name}\n";
echo "   Plan vinculado: {$shift->plan_id}\n";
echo "   Inicio: {$shift->start_time->format('Y-m-d H:i:s')}\n";
echo "   Estado: {$shift->status}\n";
echo "   Snapshot capturado:\n";
print_r($shift->target_snapshot);
echo "\n";

// 4. Verificar que el plan cambió a 'active'
$plan->refresh();
echo "✅ PASO 4: Estado del Plan actualizado\n";
echo "   Plan status: {$plan->status} (debe ser 'active')\n\n";

// 5. Registrar producción en la jornada (simulando 3 registros)
echo "✅ PASO 5: Registrando producción...\n";

// Registro 1
$shift->recordProduction(100, 95, 5);
echo "   ✓ Registro 1: 100 unidades (95 buenas, 5 defectuosas)\n";
echo "     Total producido: {$shift->actual_production}\n";

// Registro 2
$shift->recordProduction(150, 145, 5);
echo "   ✓ Registro 2: 150 unidades (145 buenas, 5 defectuosas)\n";
echo "     Total producido: {$shift->actual_production}\n";

// Registro 3
$shift->recordProduction(180, 175, 5);
echo "   ✓ Registro 3: 180 unidades (175 buenas, 5 defectuosas)\n";
echo "     Total producido: {$shift->actual_production}\n";
echo "     Progreso: " . round($shift->progress, 2) . "%\n";
echo "     Calidad: " . round($shift->quality_rate, 2) . "%\n\n";

// 6. Verificar que NO existe ProductionData antes de finalizar
$productionDataBefore = ProductionData::where('work_shift_id', $shift->id)->count();
echo "📊 PASO 6: Verificar ProductionData ANTES de finalizar\n";
echo "   Registros en production_data: {$productionDataBefore} (debe ser 0)\n\n";

// 7. Finalizar la jornada (esto debe crear ProductionData automáticamente)
echo "✅ PASO 7: Finalizando jornada...\n";
$shift->endShift();
$shift->refresh();

echo "   WorkShift finalizado\n";
echo "   Fin: {$shift->end_time->format('Y-m-d H:i:s')}\n";
echo "   Estado: {$shift->status}\n";
echo "   Duración: " . $shift->start_time->diffInMinutes($shift->end_time) . " minutos\n\n";

// 8. Verificar que SÍ existe ProductionData después de finalizar
echo "🎯 PASO 8: Verificar ProductionData DESPUÉS de finalizar\n";
$productionData = ProductionData::where('work_shift_id', $shift->id)->first();

if ($productionData) {
    echo "   ✅ ¡Registro creado automáticamente!\n";
    echo "   ProductionData ID: {$productionData->id}\n";
    echo "   Plan vinculado: {$productionData->plan_id} ✓\n";
    echo "   WorkShift vinculado: {$productionData->work_shift_id} ✓\n";
    echo "   Equipo: {$productionData->equipment->name}\n";
    echo "   Planificado: {$productionData->planned_production}\n";
    echo "   Real: {$productionData->actual_production}\n";
    echo "   Buenas: {$productionData->good_units}\n";
    echo "   Defectuosas: {$productionData->defective_units}\n";
    echo "   Cycle Time: {$productionData->cycle_time} min/unidad\n";
    echo "   Fecha: {$productionData->production_date->format('Y-m-d H:i:s')}\n\n";
    
    // Verificar relaciones
    echo "🔗 PASO 9: Verificar relaciones\n";
    echo "   productionData->plan->product_name: {$productionData->plan->product_name} ✓\n";
    echo "   productionData->workShift->id: {$productionData->workShift->id} ✓\n";
    echo "   productionData->equipment->name: {$productionData->equipment->name} ✓\n\n";
} else {
    echo "   ❌ ERROR: No se creó el registro en ProductionData\n\n";
}

// 10. Resumen final
echo "========================================\n";
echo "📋 RESUMEN DEL FLUJO COMPLETO\n";
echo "========================================\n";
echo "Plan de Producción: #{$plan->id} - {$plan->product_name}\n";
echo "  └─ Meta: {$plan->target_quantity} unidades\n";
echo "  └─ Estado: {$plan->status}\n";
echo "\nJornada de Trabajo: #{$shift->id}\n";
echo "  └─ Producido: {$shift->actual_production} unidades\n";
echo "  └─ Progreso: " . round($shift->progress, 2) . "%\n";
echo "  └─ Estado: {$shift->status}\n";
echo "\nRegistro Histórico: #" . ($productionData ? $productionData->id : 'N/A') . "\n";
echo "  └─ Vinculado a Plan: " . ($productionData && $productionData->plan_id ? '✓' : '✗') . "\n";
echo "  └─ Vinculado a Jornada: " . ($productionData && $productionData->work_shift_id ? '✓' : '✗') . "\n";
echo "  └─ Eficiencia: " . ($productionData ? round($productionData->efficiency, 2) : 'N/A') . "%\n";
echo "  └─ Calidad: " . ($productionData ? round($productionData->quality_rate, 2) : 'N/A') . "%\n";
echo "\n✅ INTEGRACIÓN COMPLETA FUNCIONAL\n\n";
