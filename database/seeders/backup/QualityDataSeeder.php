<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\QualityData;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class QualityDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = Equipment::all();

        $defectTypes = [
            'Fuera de Tolerancia',
            'Medida Incorrecta',
            'Deformación',
            'Rayado',
            'Corrosión',
            'Rebaba',
            'Porosidad',
            'Grieta',
            'Inclusión',
            'Dureza Inadecuada',
            'Ensamblaje Defectuoso',
            'Falta de Componente',
            null, // Sin defecto
        ];

        foreach ($equipment as $eq) {
            // Generate 15 quality inspections per equipment (3 per day for 5 days)
            for ($day = 0; $day < 5; $day++) {
                for ($shift = 0; $shift < 3; $shift++) {
                    $inspectionDate = Carbon::now()->subDays($day)->setHour(8 + ($shift * 8))->setMinute(rand(0, 59));

                    // Generate realistic quality data
                    $totalInspected = rand(80, 120);

                    // Most inspections should have high quality (90-100% approval)
                    // Some should be medium (80-90%)
                    // Few should be low (<80%)
                    $rand = rand(1, 100);
                    if ($rand <= 70) {
                        // High quality (90-100%)
                        $approvalRate = rand(90, 100) / 100;
                    } elseif ($rand <= 90) {
                        // Medium quality (80-90%)
                        $approvalRate = rand(80, 90) / 100;
                    } else {
                        // Low quality (<80%)
                        $approvalRate = rand(60, 80) / 100;
                    }

                    $approvedUnits = (int) ($totalInspected * $approvalRate);
                    $rejectedUnits = $totalInspected - $approvedUnits;

                    // If there are rejected units, assign a defect type
                    $defectType = $rejectedUnits > 0 ? $defectTypes[array_rand($defectTypes)] : null;

                    QualityData::create([
                        'equipment_id' => $eq->id,
                        'total_inspected' => $totalInspected,
                        'approved_units' => $approvedUnits,
                        'rejected_units' => $rejectedUnits,
                        'defect_type' => $defectType,
                        'notes' => $rejectedUnits > 0
                            ? 'Inspección con '.$rejectedUnits.' unidades rechazadas. Se requiere revisión del proceso.'
                            : 'Inspección sin defectos. Proceso dentro de especificaciones.',
                        'inspection_date' => $inspectionDate,
                    ]);
                }
            }
        }
    }
}
