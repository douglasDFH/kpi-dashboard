<?php

namespace App\Events\KpiDashboard\V1;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProduccionRegistrada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $maquinaId;
    public array $data;

    /**
     * Create a new event instance.
     * 
     * @param string $maquinaId UUID de la máquina
     * @param array $data Datos de producción actualizados
     */
    public function __construct(string $maquinaId, array $data)
    {
        $this->maquinaId = $maquinaId;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('kpi-dashboard.v1'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'produccion.registrada';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'maquina_id' => $this->maquinaId,
            'data' => $this->data,
        ];
    }
}
