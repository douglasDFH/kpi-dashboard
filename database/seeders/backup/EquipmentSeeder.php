<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Equipment;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = [
            [
                'name' => 'Prensa Hidráulica 1',
                'code' => 'PH-001',
                'type' => 'Prensa',
                'location' => 'Área de Producción A',
                'is_active' => true,
            ],
            [
                'name' => 'Torno CNC 1',
                'code' => 'TC-001',
                'type' => 'Torno',
                'location' => 'Área de Producción B',
                'is_active' => true,
            ],
            [
                'name' => 'Fresadora Industrial 1',
                'code' => 'FI-001',
                'type' => 'Fresadora',
                'location' => 'Área de Producción C',
                'is_active' => true,
            ],
            [
                'name' => 'Línea de Ensamblaje 1',
                'code' => 'LE-001',
                'type' => 'Línea de Ensamblaje',
                'location' => 'Área de Ensamblaje',
                'is_active' => true,
            ],
        ];

        foreach ($equipment as $eq) {
            Equipment::create($eq);
        }
    }
}
