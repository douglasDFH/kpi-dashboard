<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Equipment;
use App\Models\ProductionData;

echo "=== DEBUG DASHBOARD ===\n";

$equipmentCount = Equipment::count();
echo "Total Equipos: $equipmentCount\n";

if ($equipmentCount > 0) {
    $equipment = Equipment::where('is_active', true)->get();
    echo "Equipos activos: " . $equipment->count() . "\n";
    
    foreach ($equipment as $eq) {
        echo "\n📦 Equipo: {$eq->name} (ID: {$eq->id})\n";
        
        $productionCount = ProductionData::where('equipment_id', $eq->id)->count();
        echo "  - Registros Producción: $productionCount\n";
        
        if ($productionCount > 0) {
            $latestProduction = ProductionData::where('equipment_id', $eq->id)->latest()->first();
            echo "  - Última producción: {$latestProduction->production_date}\n";
        }
    }
} else {
    echo "❌ NO HAY EQUIPOS EN LA BD\n";
    echo "💡 Crear equipos en: http://127.0.0.1:8000/equipment\n";
}

echo "\n=== FIN DEBUG ===\n";
