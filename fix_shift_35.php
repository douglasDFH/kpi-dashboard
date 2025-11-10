<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WorkShift;

$shift = WorkShift::find(35);

// Calcular correctamente buenas y defectuosas
$goodUnits = round($shift->actual_production * 0.95);
$defectiveUnits = $shift->actual_production - $goodUnits;

$shift->update([
    'status' => 'pending_registration',
    'good_units' => $goodUnits,
    'defective_units' => $defectiveUnits
]);

echo "✅ Jornada #35 actualizada\n";
echo "Status: {$shift->status}\n";
echo "Producción: {$shift->actual_production}\n";
echo "Buenas: {$shift->good_units}\n";
echo "Defectuosas: {$shift->defective_units}\n";
