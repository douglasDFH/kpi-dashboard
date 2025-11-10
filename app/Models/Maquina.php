<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;

class Maquina extends Model
{
    use SoftDeletes, HasUuids, HasApiTokens;

    protected $table = 'maquinas';

    protected $fillable = [
        'area_id',
        'nombre',
        'modelo',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relación con área
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Relación con planes de máquina
     */
    public function planesMaquina(): HasMany
    {
        return $this->hasMany(PlanMaquina::class, 'maquina_id');
    }

    /**
     * Relación con jornadas de producción
     */
    public function jornadasProduccion(): HasMany
    {
        return $this->hasMany(JornadaProduccion::class, 'maquina_id');
    }

    /**
     * Relación con registros de producción
     */
    public function registrosProduccion(): HasMany
    {
        return $this->hasMany(RegistroProduccion::class, 'maquina_id');
    }

    /**
     * Relación con registros de mantenimiento
     */
    public function registrosMantenimiento(): HasMany
    {
        return $this->hasMany(RegistroMantenimiento::class, 'maquina_id');
    }

    /**
     * Relación con resultados KPI
     */
    public function resultadosKpi(): HasMany
    {
        return $this->hasMany(ResultadoKpiJornada::class, 'maquina_id');
    }
}
