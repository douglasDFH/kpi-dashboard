<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityData extends Model
{
    protected $fillable = [
        'equipment_id',
        'total_inspected',
        'approved_units',
        'rejected_units',
        'defect_type',
        'notes',
        'inspection_date',
    ];

    protected $casts = [
        'inspection_date' => 'datetime',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    // Calcula el porcentaje de calidad
    public function getQualityPercentageAttribute(): float
    {
        if ($this->total_inspected == 0) {
            return 0;
        }
        return ($this->approved_units / $this->total_inspected) * 100;
    }

    // Calcula el porcentaje de rechazo
    public function getRejectRateAttribute(): float
    {
        if ($this->total_inspected == 0) {
            return 0;
        }
        return ($this->rejected_units / $this->total_inspected) * 100;
    }
}
