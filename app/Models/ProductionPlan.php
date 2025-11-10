<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionPlan extends Model
{
    protected $fillable = [
        'equipment_id',
        'product_name',
        'product_code',
        'target_quantity',
        'shift',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_quantity' => 'integer',
    ];

    /**
     * Relación con Equipment
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Relación con el usuario que creó el plan
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con jornadas de trabajo
     */
    public function workShifts(): HasMany
    {
        return $this->hasMany(WorkShift::class, 'plan_id');
    }

    /**
     * Verificar si el plan está activo
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               now()->between($this->start_date, $this->end_date);
    }

    /**
     * Marcar plan como completado
     */
    public function complete(): void
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Marcar plan como cancelado
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Obtener el progreso del plan (%)
     */
    public function getProgressAttribute(): float
    {
        $totalProduced = $this->workShifts()
            ->where('status', 'completed')
            ->sum('actual_production');
        
        if ($this->target_quantity == 0) {
            return 0;
        }
        
        return min(100, ($totalProduced / $this->target_quantity) * 100);
    }
}

