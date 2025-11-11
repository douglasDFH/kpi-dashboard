<?php

namespace Database\Seeders;

use App\Models\JornadaProduccion;
use App\Models\Maquina;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JornadaProduccionSeeder extends Seeder
{
    /**
     * Horarios de trabajo: 08-12, 14-18, 19-23
     * Semana laboral: Lunes a Viernes
     */
    private const HORARIOS = [
        ['inicio' => '08:00', 'fin' => '12:00', 'nombre' => 'Turno Mañana'],
        ['inicio' => '14:00', 'fin' => '18:00', 'nombre' => 'Turno Tarde'],
        ['inicio' => '19:00', 'fin' => '23:00', 'nombre' => 'Turno Noche'],
    ];

    public function run(): void
    {
        $supervisor = User::where('email', 'carlos@ecoplast.com')->first();

        if (! $supervisor) {
            $this->command->error('Supervisor no encontrado');

            return;
        }        // Obtener máquinas
        $maquinas = Maquina::all();

        foreach ($maquinas as $maquina) {
            // Crear jornadas para hoy en cada turno
            foreach (self::HORARIOS as $turno) {
                $inicio = Carbon::now()
                    ->setTimeFromTimeString($turno['inicio']);

                $fin = Carbon::now()
                    ->setTimeFromTimeString($turno['fin']);

                // Si el turno ya pasó, crear para mañana
                if ($fin < now()) {
                    $inicio->addDay();
                    $fin->addDay();
                }

                JornadaProduccion::create([
                    'maquina_id' => $maquina->id,
                    'plan_maquina_id' => $maquina->planesMaquina()->first()->id,
                    'supervisor_id' => $supervisor->id,
                    'status' => $this->determinarEstado($inicio, $fin),
                    'inicio_previsto' => $inicio,
                    'fin_previsto' => $fin,
                    'inicio_real' => $this->debeEstarActivo($inicio, $fin) ? $inicio : null,
                    'fin_real' => null,
                    'objetivo_unidades_copiado' => $maquina->planesMaquina()->first()->objetivo_unidades,
                    'unidad_medida_copiado' => 'piezas',
                    'limite_fallos_critico_copiado' => $maquina->planesMaquina()->first()->limite_fallos_critico,
                    'total_unidades_producidas' => 0,
                    'total_unidades_buenas' => 0,
                    'total_unidades_malas' => 0,
                ]);

                $this->command->line("✅ Jornada creada: {$maquina->nombre} - {$turno['nombre']}");
            }
        }

        $this->command->info('✅ Jornadas de producción creadas exitosamente');
    }

    private function determinarEstado(Carbon $inicio, Carbon $fin): string
    {
        $ahora = now();

        if ($ahora < $inicio) {
            return 'pending';
        }

        if ($ahora > $fin) {
            return 'completed';
        }

        return 'running';
    }

    private function debeEstarActivo(Carbon $inicio, Carbon $fin): bool
    {
        $ahora = now();

        return $ahora >= $inicio && $ahora <= $fin;
    }
}
