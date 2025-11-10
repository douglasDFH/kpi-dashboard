<?php

namespace App\Jobs;

use App\Models\WorkShift;
use App\Events\ProductionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SimulateProduction implements ShouldQueue
{
    use Queueable;

    public $workShift;

    public function __construct(WorkShift $workShift)
    {
        $this->workShift = $workShift;
    }

    public function handle(): void
    {
        // Refrescar datos del modelo
        $this->workShift->refresh();

        // Solo simular si está activo
        if ($this->workShift->status !== 'active') {
            return;
        }

        $targetQuantity = $this->workShift->target_snapshot['target_quantity'] ?? 100;
        
        // Si ya llegó al 100%, cambiar a pending_registration
        if ($this->workShift->actual_production >= $targetQuantity) {
            $this->workShift->update(['status' => 'pending_registration']);
            broadcast(new ProductionUpdated($this->workShift->fresh()));
            return;
        }

        // Simular producción: añadir entre 1-5 unidades por ciclo
        $increment = rand(1, 5);
        $newProduction = min($this->workShift->actual_production + $increment, $targetQuantity);
        
        // Calcular unidades buenas y defectuosas (95% buenas, 5% defectuosas)
        // Se calcula sobre el TOTAL acumulado, no sobre el incremento
        $goodUnits = round($newProduction * 0.95);
        $defectiveUnits = $newProduction - $goodUnits;

        // Actualizar el shift
        $this->workShift->update([
            'actual_production' => $newProduction,
            'good_units' => round($goodUnits),
            'defective_units' => round($defectiveUnits),
        ]);

        // Broadcast del evento
        broadcast(new ProductionUpdated($this->workShift->fresh()));

        // Si aún no llegó al 100%, programar siguiente ciclo en 5 segundos
        if ($newProduction < $targetQuantity) {
            dispatch(new SimulateProduction($this->workShift))->delay(now()->addSeconds(5));
        }
    }
}
