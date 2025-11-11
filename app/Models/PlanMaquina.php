<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanMaquina extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'planes_maquina';

    protected $fillable = [
        'maquina_id',
        'nombre_plan',
        'objetivo_unidades',
        'unidad_medida',
        'ideal_cycle_time_seconds',
        'limite_fallos_critico',
        'activo',
    ];

    protected $casts = [
        'objetivo_unidades' => 'integer',
        'ideal_cycle_time_seconds' => 'float',
        'limite_fallos_critico' => 'integer',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relación con máquina
     */
    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquina::class, 'maquina_id');
    }

    /**
     * Relación con jornadas de producción
     */
    public function jornadasProduccion(): HasMany
    {
        return $this->hasMany(JornadaProduccion::class, 'plan_maquina_id');
    }

    /**
     * Scope para obtener solo planes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
