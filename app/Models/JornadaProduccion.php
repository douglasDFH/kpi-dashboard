<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JornadaProduccion extends Model
{
    use HasUuids, HasFactory;
    protected $table = 'jornadas_produccion';

    protected $fillable = [
        'plan_maquina_id',
        'maquina_id',
        'supervisor_id',
        'status',
        'inicio_real',
        'fin_real',
        'objetivo_unidades_copiado',
        'unidad_medida_copiado',
        'limite_fallos_critico_copiado',
        'total_unidades_producidas',
        'total_unidades_buenas',
        'total_unidades_malas',
    ];

    protected $casts = [
        'status' => 'string',
        'inicio_real' => 'datetime',
        'fin_real' => 'datetime',
        'objetivo_unidades_copiado' => 'integer',
        'limite_fallos_critico_copiado' => 'integer',
        'total_unidades_producidas' => 'integer',
        'total_unidades_buenas' => 'integer',
        'total_unidades_malas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con plan de máquina
     */
    public function planMaquina(): BelongsTo
    {
        return $this->belongsTo(PlanMaquina::class, 'plan_maquina_id');
    }

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
     * Relación con eventos de parada
     */
    public function eventosParada(): HasMany
    {
        return $this->hasMany(EventoParadaJornada::class, 'jornada_id');
    }

    /**
     * Relación con registros de producción
     */
    public function registrosProduccion(): HasMany
    {
        return $this->hasMany(RegistroProduccion::class, 'jornada_id');
    }

    /**
     * Relación con registros de mantenimiento
     */
    public function registrosMantenimiento(): HasMany
    {
        return $this->hasMany(RegistroMantenimiento::class, 'jornada_id');
    }

    /**
     * Relación con resultados KPI
     */
    public function resultadoKpi(): HasOne
    {
        return $this->hasOne(ResultadoKpiJornada::class, 'jornada_id');
    }
}
