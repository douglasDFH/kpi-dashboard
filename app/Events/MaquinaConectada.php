<?php

namespace App\Events;

use App\Models\Maquina;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaquinaConectada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Maquina $maquina;

    /**
     * Create a new event instance.
     */
    public function __construct(Maquina $maquina)
    {
        $this->maquina = $maquina;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('kpi-dashboard.v1'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'maquina.conectada';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'maquina_id' => $this->maquina->id,
            'maquina_nombre' => $this->maquina->nombre,
            'area' => $this->maquina->area->nombre,
        ];
    }
}
