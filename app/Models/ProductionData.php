<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionData extends Model
{
    protected $fillable = [
        'equipment_id',
        'plan_id',
        'work_shift_id',
        'planned_production',
        'actual_production',
        'good_units',
        'defective_units',
        'cycle_time',
        'production_date',
    ];

    protected $casts = [
        'production_date' => 'datetime',
        'cycle_time' => 'decimal:2',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ProductionPlan::class, 'plan_id');
    }

    public function workShift(): BelongsTo
    {
        return $this->belongsTo(WorkShift::class, 'work_shift_id');
    }

    // Calcula la eficiencia de producción
    public function getEfficiencyAttribute(): float
    {
        if ($this->planned_production == 0) {
            return 0;
        }
        return ($this->actual_production / $this->planned_production) * 100;
    }

    // Calcula el porcentaje de calidad
    public function getQualityRateAttribute(): float
    {
        if ($this->actual_production == 0) {
            return 0;
        }
        return ($this->good_units / $this->actual_production) * 100;
    }
}
