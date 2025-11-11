<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\ProductionPlan;
use App\Models\User;
use App\Models\WorkShift;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = Equipment::all();

        // Buscar usuarios por rol de forma más flexible
        $superAdmin = User::first(); // Usar el primer usuario disponible
        $supervisor = User::skip(1)->first() ?? $superAdmin;
        $operator = User::skip(2)->first() ?? $superAdmin;

        if ($equipment->isEmpty()) {
            $this->command->warn('⚠️  Advertencia: No se encontraron equipos.');

            return;
        }

        $this->command->info('🏭 Creando planes de producción...');

        // Planes completados (últimas 2 semanas)
        foreach ($equipment->take(3) as $eq) {
            for ($i = 14; $i > 7; $i--) {
                $plan = ProductionPlan::create([
                    'equipment_id' => $eq->id,
                    'product_name' => 'Pieza '.fake()->randomElement(['A100', 'B200', 'C300']),
                    'product_code' => 'PRD-'.fake()->unique()->numberBetween(1000, 9999),
                    'target_quantity' => $target = fake()->numberBetween(800, 1200),
                    'shift' => $shift = fake()->randomElement(['morning', 'afternoon', 'night']),
                    'start_date' => $startDate = Carbon::now()->subDays($i),
                    'end_date' => $startDate->copy()->addDay(),
                    'status' => 'completed',
                    'created_by' => $superAdmin->id,
                ]);

                WorkShift::create([
                    'equipment_id' => $eq->id,
                    'plan_id' => $plan->id,
                    'shift_type' => $shift,
                    'start_time' => $this->getShiftStartTime($startDate, $shift),
                    'end_time' => $this->getShiftEndTime($startDate, $shift),
                    'target_snapshot' => ['product_name' => $plan->product_name, 'target_quantity' => $target],
                    'actual_production' => $actual = fake()->numberBetween($target * 0.85, $target * 1.05),
                    'good_units' => $good = (int) ($actual * 0.95),
                    'defective_units' => $actual - $good,
                    'status' => 'completed',
                    'operator_id' => $operator->id,
                ]);
            }
        }

        // Planes activos (hoy)
        foreach ($equipment as $eq) {
            $plan = ProductionPlan::create([
                'equipment_id' => $eq->id,
                'product_name' => 'Pieza '.fake()->randomElement(['A100', 'B200', 'C300']),
                'product_code' => 'PRD-'.fake()->unique()->numberBetween(1000, 9999),
                'target_quantity' => $target = fake()->numberBetween(900, 1100),
                'shift' => $shift = 'morning',
                'start_date' => Carbon::today(),
                'end_date' => Carbon::today()->addDay(),
                'status' => 'active',
                'created_by' => $supervisor->id,
            ]);

            WorkShift::create([
                'equipment_id' => $eq->id,
                'plan_id' => $plan->id,
                'shift_type' => $shift,
                'start_time' => Carbon::today()->setTime(6, 0),
                'end_time' => null,
                'target_snapshot' => ['product_name' => $plan->product_name, 'target_quantity' => $target],
                'actual_production' => fake()->numberBetween(0, $target * 0.6),
                'good_units' => fake()->numberBetween(0, (int) ($target * 0.57)),
                'defective_units' => fake()->numberBetween(0, (int) ($target * 0.03)),
                'status' => 'active',
                'operator_id' => $operator->id,
            ]);

            // Planes pendientes
            for ($i = 1; $i <= 3; $i++) {
                ProductionPlan::create([
                    'equipment_id' => $eq->id,
                    'product_name' => 'Pieza '.fake()->randomElement(['A100', 'B200', 'C300']),
                    'product_code' => 'PRD-'.fake()->unique()->numberBetween(1000, 9999),
                    'target_quantity' => fake()->numberBetween(800, 1200),
                    'shift' => fake()->randomElement(['morning', 'afternoon', 'night']),
                    'start_date' => Carbon::today()->addDays($i),
                    'end_date' => Carbon::today()->addDays($i + 1),
                    'status' => 'pending',
                    'created_by' => $supervisor->id,
                ]);
            }
        }

        $this->command->info('✅ Planes de producción creados');
    }

    private function getShiftStartTime(Carbon $date, string $shift): Carbon
    {
        return match ($shift) {
            'morning' => $date->copy()->setTime(6, 0),
            'afternoon' => $date->copy()->setTime(14, 0),
            'night' => $date->copy()->setTime(22, 0),
        };
    }

    private function getShiftEndTime(Carbon $date, string $shift): Carbon
    {
        return match ($shift) {
            'morning' => $date->copy()->setTime(14, 0),
            'afternoon' => $date->copy()->setTime(22, 0),
            'night' => $date->copy()->addDay()->setTime(6, 0),
        };
    }
}
