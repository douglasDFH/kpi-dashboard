<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroMantenimiento extends Model
{
    use HasUuids;

    protected $table = 'registros_mantenimiento';

    protected $fillable = [
        'maquina_id',
        'supervisor_id',
        'jornada_id',
        'tipo',
        'descripcion',
    ];

    protected $casts = [
        'tipo' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con máquina
     */
    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquina::class, 'maquina_id');
    }

    /**
     * Relación con supervisor
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Relación con jornada de producción
     */
    public function jornada(): BelongsTo
    {
        return $this->belongsTo(JornadaProduccion::class, 'jornada_id');
    }
}
