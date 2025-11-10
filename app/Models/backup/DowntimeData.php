<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DowntimeData extends Model
{
    protected $fillable = [
        'equipment_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'reason',
        'category',
        'description',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    // Calcula automáticamente la duración si no está seteada
    protected static function booted()
    {
        static::saving(function ($downtime) {
            if ($downtime->end_time && $downtime->start_time) {
                $downtime->duration_minutes = $downtime->start_time->diffInMinutes($downtime->end_time);
            }
        });
    }
}
