<?php

namespace Tests\Feature\Api\V1;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Maquina;
use App\Models\JornadaProduccion;
use App\Models\Area;
use App\Models\PlanMaquina;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProduccionApiTest extends TestCase
{
    use RefreshDatabase;

    protected Maquina $maquina;
    protected JornadaProduccion $jornada;
    protected User $supervisor;

    public function setUp(): void
    {
        parent::setUp();

        // Crear área
        $area = Area::factory()->create();

        // Crear máquina con token
        $this->maquina = Maquina::factory()
            ->for($area)
            ->create();

        // Crear token para la máquina
        $token = $this->maquina->createToken('api-token');
        $this->maquina->token = $token->plainTextToken;

        // Crear supervisor
        $this->supervisor = User::factory()->create();
    }

    /**
     * Helper para crear una jornada activa
     */
    private function createActiveJornada($limite = 10)
    {
        $plan = PlanMaquina::factory()
            ->for($this->maquina)
            ->create([
                'objetivo_unidades' => 1000,
                'limite_fallos_critico' => $limite,
            ]);

        return JornadaProduccion::factory()
            ->for($plan, 'planMaquina')
            ->for($this->maquina)
            ->for($this->supervisor, 'supervisor')
            ->create([
                'status' => 'running',
                'objetivo_unidades_copiado' => 1000,
                'limite_fallos_critico_copiado' => $limite,
            ]);
    }

    #[Test]
    public function puede_registrar_produccion_con_token_valido()
    {
        $jornada = $this->createActiveJornada();

        $response = $this->postJson(
            '/api/v1/maquina/produccion',
            [
                'cantidad_producida' => 10,
                'cantidad_buena' => 9,
                'cantidad_mala' => 1,
            ],
            [
                'Authorization' => 'Bearer ' . $this->maquina->token,
            ]
        );

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'registro_id',
                'jornada' => [
                    'id',
                    'total_producidas',
                    'total_buenas',
                    'total_malas',
                    'progreso',
                    'status',
                ]
            ]
        ]);

        // Verificar que se actualizó la jornada
        $jornada->refresh();
        $this->assertEquals(10, $jornada->total_unidades_producidas);
        $this->assertEquals(9, $jornada->total_unidades_buenas);
        $this->assertEquals(1, $jornada->total_unidades_malas);
    }

    #[Test]
    public function rechaza_produccion_sin_token()
    {
        $response = $this->postJson(
            '/api/v1/maquina/produccion',
            [
                'cantidad_producida' => 10,
                'cantidad_buena' => 9,
                'cantidad_mala' => 1,
            ]
        );

        $response->assertStatus(401);
    }

    #[Test]
    public function rechaza_produccion_sin_jornada_activa()
    {
        // Crear jornada y luego pausarla
        $jornada = $this->createActiveJornada();
        $jornada->update(['status' => 'paused']);

        $response = $this->postJson(
            '/api/v1/maquina/produccion',
            [
                'cantidad_producida' => 10,
                'cantidad_buena' => 9,
                'cantidad_mala' => 1,
            ],
            [
                'Authorization' => 'Bearer ' . $this->maquina->token,
            ]
        );

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString(
            'No hay jornada activa',
            $response->json('message')
        );
    }

    #[Test]
    public function valida_datos_requeridos()
    {
        $this->createActiveJornada();

        $response = $this->postJson(
            '/api/v1/maquina/produccion',
            [
                'cantidad_producida' => 10,
                // Faltan cantidad_buena y cantidad_mala
            ],
            [
                'Authorization' => 'Bearer ' . $this->maquina->token,
            ]
        );

        $response->assertStatus(422);
    }

    #[Test]
    public function detiene_maquina_por_limite_de_fallos()
    {
        // Crear jornada con límite bajo
        $plan = PlanMaquina::factory()
            ->for($this->maquina)
            ->create([
                'objetivo_unidades' => 1000,
                'limite_fallos_critico' => 5,
            ]);

        $jornada = JornadaProduccion::factory()
            ->for($plan, 'planMaquina')
            ->for($this->maquina)
            ->for($this->supervisor, 'supervisor')
            ->create([
                'status' => 'running',
                'objetivo_unidades_copiado' => 1000,
                'limite_fallos_critico_copiado' => 5,
                'total_unidades_producidas' => 0,
                'total_unidades_buenas' => 0,
                'total_unidades_malas' => 0,
            ]);

        // Enviar 5 unidades malas (alcanza límite)
        $response = $this->postJson(
            '/api/v1/maquina/produccion',
            [
                'cantidad_producida' => 5,
                'cantidad_buena' => 0,
                'cantidad_mala' => 5,
            ],
            [
                'Authorization' => 'Bearer ' . $this->maquina->token,
            ]
        );

        $response->assertStatus(201);

        // Verificar que la jornada está detenida
        $jornadaActualizada = JornadaProduccion::findOrFail($jornada->id);
        $this->assertEquals('stopped_critical', $jornadaActualizada->status);
    }
}
