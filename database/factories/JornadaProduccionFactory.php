<?php

namespace Database\Factories;

use App\Models\JornadaProduccion;
use Illuminate\Database\Eloquent\Factories\Factory;

class JornadaProduccionFactory extends Factory
{
    protected $model = JornadaProduccion::class;

    public function definition(): array
    {
        return [
            'status' => 'running',
            'inicio_real' => now(),
            'fin_real' => null,
            'objetivo_unidades_copiado' => 1000,
            'unidad_medida_copiado' => 'piezas',
            'limite_fallos_critico_copiado' => 10,
            'total_unidades_producidas' => 0,
            'total_unidades_buenas' => 0,
            'total_unidades_malas' => 0,
        ];
    }
}
