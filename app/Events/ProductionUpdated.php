<?php

namespace App\Events;

use App\Models\WorkShift;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $workShift;

    public function __construct(WorkShift $workShift)
    {
        $this->workShift = $workShift;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('work-shift.' . $this->workShift->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'production.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->workShift->id,
            'actual_production' => $this->workShift->actual_production,
            'good_units' => $this->workShift->good_units,
            'defective_units' => $this->workShift->defective_units,
            'progress' => $this->workShift->target_snapshot['target_quantity'] > 0 
                ? round(($this->workShift->actual_production / $this->workShift->target_snapshot['target_quantity']) * 100, 2)
                : 0,
            'status' => $this->workShift->status,
        ];
    }
}
