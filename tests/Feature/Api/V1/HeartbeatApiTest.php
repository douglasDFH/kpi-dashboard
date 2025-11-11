<?php

namespace Tests\Feature\Api\V1;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Maquina;
use App\Models\Area;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HeartbeatApiTest extends TestCase
{
    use RefreshDatabase;

    protected Maquina $maquina;

    public function setUp(): void
    {
        parent::setUp();

        $area = Area::factory()->create();
        $this->maquina = Maquina::factory()
            ->for($area)
            ->create();

        $token = $this->maquina->createToken('api-token');
        $this->maquina->token = $token->plainTextToken;
    }

    #[Test]
    public function puede_enviar_heartbeat_con_token_valido()
    {
        $response = $this->postJson(
            '/api/v1/maquina/heartbeat',
            [],
            [
                'Authorization' => 'Bearer ' . $this->maquina->token,
            ]
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Heartbeat recibido',
        ]);
    }

    #[Test]
    public function rechaza_heartbeat_sin_token()
    {
        $response = $this->postJson('/api/v1/maquina/heartbeat', []);

        $response->assertStatus(401);
    }

    #[Test]
    public function actualiza_timestamp_de_maquina()
    {
        $updatedAtAntes = $this->maquina->updated_at;

        sleep(1);

        $this->postJson(
            '/api/v1/maquina/heartbeat',
            [],
            [
                'Authorization' => 'Bearer ' . $this->maquina->token,
            ]
        );

        $this->maquina->refresh();
        $this->assertNotEquals($updatedAtAntes, $this->maquina->updated_at);
    }
}
