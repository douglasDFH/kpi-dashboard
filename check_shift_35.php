<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkShift;

$shift = WorkShift::find(35);

if (!$shift) {
    echo "❌ WorkShift #35 NO EXISTE\n";
    exit;
}

echo "=== WorkShift #35 ===\n";
echo "Status: {$shift->status}\n";
echo "Producción actual: {$shift->actual_production}\n";
echo "Buenas: {$shift->good_units}\n";
echo "Defectuosas: {$shift->defective_units}\n";
echo "Target: " . ($shift->target_snapshot['target_quantity'] ?? 0) . "\n";
echo "Progress: " . round(($shift->actual_production / ($shift->target_snapshot['target_quantity'] ?? 1)) * 100, 2) . "%\n";
