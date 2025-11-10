<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroProduccion extends Model
{
    protected $table = 'registros_produccion';

    protected $fillable = [
        'jornada_id',
        'maquina_id',
        'cantidad_producida',
        'cantidad_buena',
        'cantidad_mala',
    ];

    protected $casts = [
        'cantidad_producida' => 'integer',
        'cantidad_buena' => 'integer',
        'cantidad_mala' => 'integer',
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

    /**
     * Relación con máquina
     */
    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquina::class, 'maquina_id');
    }
}
