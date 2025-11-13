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
        Schema::create('jornadas_produccion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('plan_maquina_id')->constrained('planes_maquina');
            $table->foreignUuid('maquina_id')->constrained('maquinas');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'running', 'paused', 'completed', 'stopped_critical'])->default('pending');

            // Timestamps Previsto
            $table->timestamp('inicio_previsto')->nullable();
            $table->timestamp('fin_previsto')->nullable();

            // Timestamps Reales
            $table->timestamp('inicio_real')->nullable();
            $table->timestamp('fin_real')->nullable();

            // Snapshot (Copia) del Plan
            $table->integer('objetivo_unidades_copiado');
            $table->string('unidad_medida_copiado', 50);
            $table->integer('limite_fallos_critico_copiado');

            // Datos Agregados (para dashboards rápidos)
            $table->integer('total_unidades_producidas')->default(0);
            $table->integer('total_unidades_buenas')->default(0);
            $table->integer('total_unidades_malas')->default(0);

            $table->timestamps();

            $table->index(['maquina_id', 'status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jornadas_produccion');
    }
};
