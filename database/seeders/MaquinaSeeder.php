<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MaquinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = \App\Models\Area::all();

        if ($areas->isEmpty()) {
            $this->command->error('❌ No hay áreas creadas. Ejecuta AreaSeeder primero.');

            return;
        }

        $maquinas = [
            // Área de Prensado
            [
                'area_id' => $areas->where('nombre', 'Área de Prensado')->first()->id,
                'nombre' => 'Prensa Hidráulica 1',
                'modelo' => 'PH-500',
                'status' => 'idle',
            ],
            [
                'area_id' => $areas->where('nombre', 'Área de Prensado')->first()->id,
                'nombre' => 'Prensa Hidráulica 2',
                'modelo' => 'PH-500',
                'status' => 'idle',
            ],

            // Área de Ensamblaje
            [
                'area_id' => $areas->where('nombre', 'Área de Ensamblaje')->first()->id,
                'nombre' => 'Estación de Ensamblaje 1',
                'modelo' => 'EA-200',
                'status' => 'idle',
            ],

            // Área de Pintura
            [
                'area_id' => $areas->where('nombre', 'Área de Pintura')->first()->id,
                'nombre' => 'Cabina de Pintura 1',
                'modelo' => 'CP-300',
                'status' => 'idle',
            ],
        ];

        foreach ($maquinas as $maquina) {
            \App\Models\Maquina::create($maquina);
        }

        $this->command->info('✅ Máquinas creadas exitosamente: '.count($maquinas));
    }
}
