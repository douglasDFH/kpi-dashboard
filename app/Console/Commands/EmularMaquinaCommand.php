<?php

namespace App\Console\Commands;

use App\Models\JornadaProduccion;
use App\Models\Maquina;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EmularMaquinaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emular:maquina 
                            {maquina_id? : UUID de la máquina a emular}
                            {--all : Emular todas las máquinas}
                            {--interval=5 : Intervalo en segundos entre producciones}
                            {--cantidad=10 : Cantidad de unidades por lote}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Emula producción de una o todas las máquinas enviando datos a la API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $maquinaId = $this->argument('maquina_id');
        $emularTodas = $this->option('all');
        $interval = (int) $this->option('interval');
        $cantidadBase = (int) $this->option('cantidad');

        if (! $maquinaId && ! $emularTodas) {
            $this->error('Debe especificar un maquina_id o usar --all');

            return 1;
        }

        // Obtener máquinas a emular
        if ($emularTodas) {
            $maquinas = Maquina::with('tokens')->get();
            $this->info('Emulando todas las máquinas (' . $maquinas->count() . ')');
        } else {
            $maquinas = Maquina::with('tokens')->where('id', $maquinaId)->get();
            if ($maquinas->isEmpty()) {
                $this->error('Máquina no encontrada');

                return 1;
            }
            $this->info('Emulando máquina: ' . $maquinas->first()->nombre);
        }

        $this->info('Intervalo: ' . $interval . ' segundos');
        $this->info('Presiona Ctrl+C para detener');
        $this->newLine();

        // Ciclo infinito de emulación
        $iteracion = 1;
        while (true) {
            foreach ($maquinas as $maquina) {
                $this->emularProduccion($maquina, $cantidadBase, $iteracion);
            }

            $this->info("[Iteración $iteracion] Esperando {$interval}s...");
            sleep($interval);
            $iteracion++;
        }

        return 0;
    }

    /**
     * Emula producción para una máquina
     */
    private function emularProduccion(Maquina $maquina, int $cantidadBase, int $iteracion): void
    {
        // Verificar que tenga token
        $token = $maquina->tokens->first();
        if (! $token) {
            $this->warn("[{$maquina->nombre}] No tiene token Sanctum");

            return;
        }

        // Verificar que tenga jornada activa
        $jornada = JornadaProduccion::where('maquina_id', $maquina->id)
            ->where('status', 'running')
            ->first();

        if (! $jornada) {
            $this->warn("[{$maquina->nombre}] No tiene jornada activa");

            return;
        }

        // Generar datos aleatorios (simulación)
        $cantidadProducida = rand($cantidadBase - 2, $cantidadBase + 2);
        $tasaDefectos = rand(0, 15); // 0-15% de defectos
        $cantidadMala = (int) ($cantidadProducida * ($tasaDefectos / 100));
        $cantidadBuena = $cantidadProducida - $cantidadMala;

        // Enviar a la API
        try {
            $response = Http::withToken($token->plainTextToken ?? $token->token)
                ->post(url('/api/v1/maquina/produccion'), [
                    'cantidad_producida' => $cantidadProducida,
                    'cantidad_buena' => $cantidadBuena,
                    'cantidad_mala' => $cantidadMala,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $progreso = $data['data']['jornada']['progreso'] ?? 0;
                $this->info(
                    "[{$maquina->nombre}] ✓ Producido: {$cantidadProducida} " .
                        "(B:{$cantidadBuena} M:{$cantidadMala}) - Progreso: " . round($progreso, 1) . '%'
                );
            } else {
                $this->error("[{$maquina->nombre}] Error: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("[{$maquina->nombre}] Excepción: " . $e->getMessage());
        }
    }
}
