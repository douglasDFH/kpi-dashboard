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
}
