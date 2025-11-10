<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkShift;

echo "=== Simulando Vista show.blade.php para WorkShift #32 ===\n\n";

$shift = WorkShift::with(['equipment', 'plan', 'operator'])->find(32);

if (!$shift) {
    echo "❌ WorkShift #32 NO EXISTE\n";
    exit;
}

echo "✅ Datos que recibe la vista:\n\n";

echo "--- Variables Blade disponibles ---\n";
echo "\$shift->id = {$shift->id}\n";
echo "\$shift->equipment->name = {$shift->equipment->name}\n";
echo "\$shift->status = {$shift->status}\n";
echo "\$shift->actual_production = {$shift->actual_production}\n";
echo "\$shift->good_units = {$shift->good_units}\n";
echo "\$shift->defective_units = {$shift->defective_units}\n";

echo "\n--- Sección: Plan de Producción ---\n";
echo "@if(\$shift->plan) = " . ($shift->plan ? 'TRUE' : 'FALSE') . "\n";

if ($shift->plan) {
    echo "  ✅ Plan existe:\n";
    echo "  \$shift->plan->id = {$shift->plan->id}\n";
    echo "  \$shift->plan->product_name = {$shift->plan->product_name}\n";
    echo "  Blade: #{{ \$shift->plan->id }} - {{ \$shift->plan->product_name }}\n";
    echo "  Resultado HTML: #{$shift->plan->id} - {$shift->plan->product_name}\n";
} else {
    echo "  ❌ Plan NO existe o es NULL\n";
}

echo "\n--- Sección: target_snapshot ---\n";
echo "\$shift->target_snapshot = " . json_encode($shift->target_snapshot) . "\n";

if ($shift->target_snapshot && isset($shift->target_snapshot['product_name'])) {
    echo "  ✅ product_name: {$shift->target_snapshot['product_name']}\n";
} else {
    echo "  ❌ product_name: NO DISPONIBLE\n";
}

if ($shift->target_snapshot && isset($shift->target_snapshot['target_quantity'])) {
    echo "  ✅ target_quantity: {$shift->target_snapshot['target_quantity']}\n";
} else {
    echo "  ❌ target_quantity: NO DISPONIBLE\n";
}

echo "\n--- JavaScript Alpine.js ---\n";
echo "actualProduction: {{ \$shift->actual_production }} = {$shift->actual_production}\n";
echo "goodUnits: {{ \$shift->good_units }} = {$shift->good_units}\n";
echo "defectiveUnits: {{ \$shift->defective_units }} = {$shift->defective_units}\n";

$targetQty = $shift->target_snapshot['target_quantity'] ?? 0;
echo "targetQuantity: {{ \$shift->target_snapshot['target_quantity'] ?? 0 }} = {$targetQty}\n";

echo "\n--- Estadísticas Calculadas ---\n";
$progress = $targetQty > 0 ? round(($shift->actual_production / $targetQty) * 100, 2) : 0;
echo "Progreso: {$shift->actual_production} / {$targetQty} = {$progress}%\n";

$qualityRate = $shift->actual_production > 0 
    ? round(($shift->good_units / $shift->actual_production) * 100, 2) 
    : 100;
echo "Tasa de Calidad: {$shift->good_units} / {$shift->actual_production} = {$qualityRate}%\n";

echo "\n--- Gráfico de Producción (Chart.js) ---\n";
echo "labels: ['Producido', 'Pendiente', 'Buenas', 'Defectuosas']\n";
echo "data: [\n";
echo "  Producido: {$shift->actual_production}\n";
$pending = max(0, $targetQty - $shift->actual_production);
echo "  Pendiente: {$pending}\n";
echo "  Buenas: {$shift->good_units}\n";
echo "  Defectuosas: {$shift->defective_units}\n";
echo "]\n";

echo "\n=== Conclusión ===\n";
if ($shift->plan && $shift->target_snapshot && isset($shift->target_snapshot['target_quantity'])) {
    echo "✅ TODOS LOS DATOS ESTÁN DISPONIBLES\n";
    echo "   La vista DEBERÍA mostrar:\n";
    echo "   - Plan de Producción: #{$shift->plan->id} - {$shift->plan->product_name}\n";
    echo "   - Producto: {$shift->target_snapshot['product_name']}\n";
    echo "   - Objetivo: {$targetQty} unidades\n";
    echo "   - Progreso: {$progress}%\n";
    echo "   - Gráfico con valores reales\n\n";
    
    echo "🔍 Si NO se ve en el navegador:\n";
    echo "   1. Presiona Ctrl+F5 para limpiar caché del navegador\n";
    echo "   2. Verifica la consola del navegador (F12) por errores JavaScript\n";
    echo "   3. Verifica que Chart.js esté cargado correctamente\n";
    echo "   4. Inspecciona el HTML generado con DevTools\n";
} else {
    echo "❌ FALTAN DATOS\n";
    if (!$shift->plan) echo "   - Plan no asociado\n";
    if (!$shift->target_snapshot) echo "   - target_snapshot vacío\n";
}
