<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\JornadaProduccion;
use App\Models\Maquina;
use App\Models\Area;
use App\Models\PlanMaquina;
use App\Models\User;
use App\Services\KpiService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KpiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KpiService $kpiService;
    protected JornadaProduccion $jornada;

    public function setUp(): void
    {
        parent::setUp();

        $this->kpiService = app(KpiService::class);

        // Crear datos de prueba
        $area = Area::factory()->create();
        $maquina = Maquina::factory()->for($area)->create();
        $supervisor = User::factory()->create();

        $plan = PlanMaquina::factory()
            ->for($maquina)
            ->create([
                'objetivo_unidades' => 1000,
                'ideal_cycle_time_seconds' => 30,
                'limite_fallos_critico' => 10,
            ]);

        $this->jornada = JornadaProduccion::factory()
            ->for($plan, 'planMaquina')
            ->for($maquina)
            ->for($supervisor, 'supervisor')
            ->create([
                'status' => 'running',
                'inicio_real' => now()->subMinutes(30), // 30 minutos atrás = 1800 segundos
                'fin_real' => now(), // Ahora
                'objetivo_unidades_copiado' => 1000,
                'limite_fallos_critico_copiado' => 10,
                'total_unidades_producidas' => 60, // 60 unidades en 1800 segundos con ideal 30s = (60*30)/1800*100 = 100%
                'total_unidades_buenas' => 57,
                'total_unidades_malas' => 3,
        ]);
    }

    #[Test]
    public function calcula_oee_correctamente()
    {
        $resultado = $this->kpiService->calculateOEE($this->jornada->maquina_id);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('oee', $resultado);
        $this->assertGreaterThanOrEqual(0, $resultado['oee']);
        $this->assertLessThanOrEqual(100, $resultado['oee']);
    }

    #[Test]
    public function calcula_disponibilidad_correctamente()
    {
        $disponibilidad = $this->kpiService->calculateAvailability(
            $this->jornada->maquina_id,
            $this->jornada->id
        );

        $this->assertIsFloat($disponibilidad);
        $this->assertGreaterThanOrEqual(0, $disponibilidad);
        $this->assertLessThanOrEqual(100, $disponibilidad);
    }

    #[Test]
    public function calcula_calidad_correctamente()
    {
        // Crear registros de producción para calcular calidad
        // 855 buenas de 900 producidas = 95% de calidad
        $this->jornada->registrosProduccion()->create([
            'maquina_id' => $this->jornada->maquina_id,
            'cantidad_producida' => 900,
            'cantidad_buena' => 855,
            'cantidad_mala' => 45,
        ]);

        $calidad = $this->kpiService->calculateQuality(
            $this->jornada->maquina_id,
            $this->jornada->id
        );

        $this->assertIsFloat($calidad);
        $this->assertEquals(95.0, $calidad);
    }

    #[Test]
    public function calcula_rendimiento_correctamente()
    {
        $rendimiento = $this->kpiService->calculatePerformance(
            $this->jornada->maquina_id,
            $this->jornada->id
        );

        $this->assertIsFloat($rendimiento);
        $this->assertGreaterThanOrEqual(0, $rendimiento);
        $this->assertLessThanOrEqual(100, $rendimiento);
    }

    #[Test]
    public function retorna_cero_cuando_no_hay_produccion()
    {
        $jornadaSinProduccion = JornadaProduccion::factory()
            ->for($this->jornada->planMaquina, 'planMaquina')
            ->for($this->jornada->maquina)
            ->for($this->jornada->supervisor, 'supervisor')
            ->create([
                'status' => 'running',
                'objetivo_unidades_copiado' => 1000,
                'limite_fallos_critico_copiado' => 10,
                'total_unidades_producidas' => 0,
                'total_unidades_buenas' => 0,
                'total_unidades_malas' => 0,
            ]);

        $calidad = $this->kpiService->calculateQuality(
            $jornadaSinProduccion->maquina_id,
            $jornadaSinProduccion->id
        );
        $this->assertEquals(0, $calidad);
    }
}
