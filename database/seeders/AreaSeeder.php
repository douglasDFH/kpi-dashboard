<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            [
                'nombre' => 'Área de Prensado',
                'descripcion' => 'Área dedicada a la producción de piezas mediante prensas hidráulicas',
            ],
            [
                'nombre' => 'Área de Ensamblaje',
                'descripcion' => 'Área para el ensamblaje final de productos terminados',
            ],
            [
                'nombre' => 'Área de Pintura',
                'descripcion' => 'Área especializada en procesos de pintura y acabados',
            ],
            [
                'nombre' => 'Área de Empaque',
                'descripcion' => 'Área dedicada al empaque y preparación para envío',
            ],
        ];

        foreach ($areas as $area) {
            \App\Models\Area::create($area);
        }

        $this->command->info('✅ Áreas creadas exitosamente: '.count($areas));
    }
}
