<?php

namespace Database\Factories;

use App\Models\PlanMaquina;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanMaquinaFactory extends Factory
{
    protected $model = PlanMaquina::class;

    public function definition(): array
    {
        return [
            'nombre_plan' => 'Plan ' . $this->faker->unique()->numerify('###'),
            'objetivo_unidades' => 1000,
            'unidad_medida' => 'piezas',
            'ideal_cycle_time_seconds' => 30,
            'limite_fallos_critico' => 10,
            'activo' => true,
        ];
    }
}
