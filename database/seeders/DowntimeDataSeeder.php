<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\DowntimeData;
use Carbon\Carbon;

class DowntimeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = Equipment::all();

        $reasons = [
            'Mantenimiento Preventivo',
            'Falla Mecánica',
            'Falta de Material',
            'Cambio de Herramienta',
            'Ajuste de Máquina',
            'Falla Eléctrica',
        ];

        $categories = ['planificado', 'no planificado'];

        // Crear datos para los últimos 7 días
        for ($day = 0; $day < 7; $day++) {
            $date = Carbon::now()->subDays($day);

            foreach ($equipment as $eq) {
                // 2-3 eventos de downtime por equipo por día
                $downtimeCount = rand(2, 3);

                for ($i = 0; $i < $downtimeCount; $i++) {
                    $startTime = $date->copy()->addHours(rand(1, 20));
                    $durationMinutes = rand(15, 180);
                    $endTime = $startTime->copy()->addMinutes($durationMinutes);

                    $reason = $reasons[array_rand($reasons)];
                    $category = ($reason === 'Mantenimiento Preventivo') ? 'planificado' : $categories[array_rand($categories)];

                    DowntimeData::create([
                        'equipment_id' => $eq->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'duration_minutes' => $durationMinutes,
                        'reason' => $reason,
                        'category' => $category,
                        'description' => 'Downtime registrado automáticamente',
                    ]);
                }
            }
        }
    }
}
