<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanMaquinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maquinas = \App\Models\Maquina::all();

        if ($maquinas->isEmpty()) {
            $this->command->error('❌ No hay máquinas creadas. Ejecuta MaquinaSeeder primero.');
            return;
        }

        $this->command->info('🏭 Creando planes de producción...');

        $planes = [];

        // Crear planes para máquinas de prensado
        $prensas = $maquinas->filter(function($maquina) {
            return str_contains($maquina->nombre, 'Prensa');
        });

        foreach ($prensas as $prensa) {
            $planes[] = [
                'maquina_id' => $prensa->id,
                'nombre_plan' => 'Turno Mañana - Producto Estándar',
                'objetivo_unidades' => 1500,
                'unidad_medida' => 'piezas',
                'ideal_cycle_time_seconds' => 30,
                'limite_fallos_critico' => 10,
                'activo' => true,
            ];

            $planes[] = [
                'maquina_id' => $prensa->id,
                'nombre_plan' => 'Turno Tarde - Producto Premium',
                'objetivo_unidades' => 1200,
                'unidad_medida' => 'piezas',
                'ideal_cycle_time_seconds' => 35,
                'limite_fallos_critico' => 8,
                'activo' => false,
            ];
        }

        // Crear planes para estaciones de ensamblaje
        $ensamblaje = $maquinas->filter(function($maquina) {
            return str_contains($maquina->nombre, 'Ensamblaje');
        });

        foreach ($ensamblaje as $estacion) {
            $planes[] = [
                'maquina_id' => $estacion->id,
                'nombre_plan' => 'Turno Mañana - Modelo Básico',
                'objetivo_unidades' => 800,
                'unidad_medida' => 'unidades',
                'ideal_cycle_time_seconds' => 45,
                'limite_fallos_critico' => 5,
                'activo' => true,
            ];
        }

        // Crear planes para otras máquinas
        $otras = $maquinas->filter(function($maquina) {
            return !str_contains($maquina->nombre, 'Prensa') && !str_contains($maquina->nombre, 'Ensamblaje');
        });

        foreach ($otras as $maquina) {
            $planes[] = [
                'maquina_id' => $maquina->id,
                'nombre_plan' => 'Turno Estándar',
                'objetivo_unidades' => 1000,
                'unidad_medida' => 'unidades',
                'ideal_cycle_time_seconds' => 60,
                'limite_fallos_critico' => 5,
                'activo' => true,
            ];
        }

        foreach ($planes as $plan) {
            \App\Models\PlanMaquina::create($plan);
        }

        $this->command->info('✅ Planes de producción creados: ' . count($planes));
    }
}
