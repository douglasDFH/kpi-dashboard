<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class WorkShift extends Model
{
    protected $fillable = [
        'equipment_id',
        'plan_id',
        'shift_type',
        'start_time',
        'end_time',
        'target_snapshot',
        'actual_production',
        'good_units',
        'defective_units',
        'status',
        'operator_id',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'target_snapshot' => 'array',
        'actual_production' => 'integer',
        'good_units' => 'integer',
        'defective_units' => 'integer',
    ];

    /**
     * Relación con Equipment
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Relación con ProductionPlan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(ProductionPlan::class, 'plan_id');
    }

    /**
     * Relación con el operador
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * Iniciar jornada tomando snapshot del plan
     */
    public static function startShift(int $equipmentId, ?int $planId, string $shiftType, int $operatorId): self
    {
        $snapshot = null;
        
        if ($planId) {
            $plan = ProductionPlan::find($planId);
            if ($plan) {
                $snapshot = [
                    'product_name' => $plan->product_name,
                    'target_quantity' => $plan->target_quantity,
                    'shift' => $plan->shift,
                ];
                
                // Activar plan si está pending
                if ($plan->status === 'pending') {
                    $plan->update(['status' => 'active']);
                }
            }
        }

        return self::create([
            'equipment_id' => $equipmentId,
            'plan_id' => $planId,
            'shift_type' => $shiftType,
            'start_time' => now(),
            'target_snapshot' => $snapshot,
            'status' => 'active',
            'operator_id' => $operatorId,
        ]);
    }

    /**
     * Finalizar jornada
     */
    public function endShift(): void
    {
        $this->update([
            'end_time' => now(),
            'status' => 'completed',
        ]);

        // Crear registro automático en production_data
        if ($this->actual_production > 0) {
            $this->createProductionDataRecord();
        }

        // Si hay plan y se completó el objetivo, marcar plan como completado
        if ($this->plan && $this->actual_production >= $this->plan->target_quantity) {
            $this->plan->complete();
        }
    }

    /**
     * Crear registro en production_data automáticamente
     */
    protected function createProductionDataRecord(): void
    {
        // Calcular cycle_time en minutos
        $duration = $this->start_time->diffInMinutes($this->end_time);
        $cycleTime = $this->actual_production > 0 
            ? round($duration / $this->actual_production, 2)
            : 0;

        // Obtener planned_production desde el snapshot del plan (si existe)
        $plannedProduction = 0;
        if ($this->target_snapshot && isset($this->target_snapshot['target_quantity'])) {
            $plannedProduction = $this->target_snapshot['target_quantity'];
        }

        ProductionData::create([
            'equipment_id' => $this->equipment_id,
            'plan_id' => $this->plan_id,
            'work_shift_id' => $this->id,
            'planned_production' => $plannedProduction,
            'actual_production' => $this->actual_production,
            'good_units' => $this->good_units,
            'defective_units' => $this->defective_units,
            'cycle_time' => $cycleTime,
            'production_date' => $this->start_time,
        ]);
    }

    /**
     * Registrar producción en la jornada
     */
    public function recordProduction(int $quantity, int $goodUnits, int $defectiveUnits): void
    {
        $this->increment('actual_production', $quantity);
        $this->increment('good_units', $goodUnits);
        $this->increment('defective_units', $defectiveUnits);
    }

    /**
     * Verificar si la jornada está activa
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && is_null($this->end_time);
    }

    /**
     * Obtener duración de la jornada en minutos
     */
    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->end_time) {
            return now()->diffInMinutes($this->start_time);
        }
        
        return $this->end_time->diffInMinutes($this->start_time);
    }

    /**
     * Obtener progreso de la jornada (%)
     */
    public function getProgressAttribute(): float
    {
        if (!$this->target_snapshot || !isset($this->target_snapshot['target_quantity'])) {
            return 0;
        }
        
        $target = $this->target_snapshot['target_quantity'];
        
        if ($target == 0) {
            return 0;
        }
        
        return min(100, ($this->actual_production / $target) * 100);
    }

    /**
     * Obtener tasa de calidad (%)
     */
    public function getQualityRateAttribute(): float
    {
        if ($this->actual_production == 0) {
            return 100;
        }
        
        return ($this->good_units / $this->actual_production) * 100;
    }
}

