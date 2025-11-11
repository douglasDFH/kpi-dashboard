<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultadoKpiJornada extends Model
{
    use HasUuids;

    protected $table = 'resultados_kpi_jornada';

    protected $fillable = [
        'jornada_id',
        'maquina_id',
        'fecha_jornada',
        'disponibilidad',
        'rendimiento',
        'calidad',
        'oee_score',
        'tiempo_planificado_segundos',
        'tiempo_paradas_programadas_segundos',
        'tiempo_paradas_no_programadas_segundos',
        'tiempo_operacion_real_segundos',
    ];

    protected $casts = [
        'fecha_jornada' => 'date',
        'disponibilidad' => 'float',
        'rendimiento' => 'float',
        'calidad' => 'float',
        'oee_score' => 'float',
        'tiempo_planificado_segundos' => 'integer',
        'tiempo_paradas_programadas_segundos' => 'integer',
        'tiempo_paradas_no_programadas_segundos' => 'integer',
        'tiempo_operacion_real_segundos' => 'integer',
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
