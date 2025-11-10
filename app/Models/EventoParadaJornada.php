<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoParadaJornada extends Model
{
    protected $table = 'eventos_parada_jornada';

    protected $fillable = [
        'jornada_id',
        'motivo',
        'inicio_parada',
        'fin_parada',
        'comentarios',
    ];

    protected $casts = [
        'motivo' => 'string',
        'inicio_parada' => 'datetime',
        'fin_parada' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con jornada de producción
     */
    public function jornada(): BelongsTo
    {
        return $this->belongsTo(JornadaProduccion::class, 'jornada_id');
    }
}
