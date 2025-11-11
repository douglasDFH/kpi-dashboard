<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\ProductionData;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductionDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = Equipment::all();

        // Crear datos para los últimos 7 días
        for ($day = 0; $day < 7; $day++) {
            $date = Carbon::now()->subDays($day);

            foreach ($equipment as $eq) {
                // Crear 3 registros por día por equipo
                for ($shift = 0; $shift < 3; $shift++) {
                    $plannedProduction = rand(80, 120);
                    $actualProduction = rand(70, $plannedProduction);
                    $defectiveUnits = rand(0, (int) ($actualProduction * 0.1));
                    $goodUnits = $actualProduction - $defectiveUnits;

                    ProductionData::create([
                        'equipment_id' => $eq->id,
                        'planned_production' => $plannedProduction,
                        'actual_production' => $actualProduction,
                        'good_units' => $goodUnits,
                        'defective_units' => $defectiveUnits,
                        'cycle_time' => rand(50, 150) / 10, // 5.0 - 15.0 minutos
                        'production_date' => $date->copy()->addHours($shift * 8),
                    ]);
                }
            }
        }
    }
}
