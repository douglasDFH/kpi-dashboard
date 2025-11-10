<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resultados_kpi_jornada', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jornada_id')->constrained('jornadas_produccion')->onDelete('cascade');
            $table->foreignUuid('maquina_id')->constrained('maquinas');
            $table->date('fecha_jornada');

            // KPIs
            $table->float('disponibilidad');
            $table->float('rendimiento');
            $table->float('calidad');
            $table->float('oee_score'); // OEE = D * R * C

            // Tiempos (calculados para el reporte)
            $table->integer('tiempo_planificado_segundos');
            $table->integer('tiempo_paradas_programadas_segundos');
            $table->integer('tiempo_paradas_no_programadas_segundos');
            $table->integer('tiempo_operacion_real_segundos');

            $table->timestamps();

            $table->index(['maquina_id', 'fecha_jornada']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados_kpi_jornada');
    }
};
