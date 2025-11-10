<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function productionData(): HasMany
    {
        return $this->hasMany(ProductionData::class);
    }

    public function qualityData(): HasMany
    {
        return $this->hasMany(QualityData::class);
    }

    public function downtimeData(): HasMany
    {
        return $this->hasMany(DowntimeData::class);
    }

    public function productionPlans(): HasMany
    {
        return $this->hasMany(ProductionPlan::class);
    }

    public function workShifts(): HasMany
    {
        return $this->hasMany(WorkShift::class);
    }

    /**
     * Obtener el plan activo actual
     */
    public function getActivePlan(): ?ProductionPlan
    {
        return $this->productionPlans()
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();
    }

    /**
     * Obtener la jornada activa actual
     */
    public function getActiveShift(): ?WorkShift
    {
        return $this->workShifts()
            ->where('status', 'active')
            ->whereNull('end_time')
            ->latest()
            ->first();
    }
}
