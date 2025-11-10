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
        Schema::create('eventos_parada_jornada', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jornada_id')->constrained('jornadas_produccion')->onDelete('cascade');
            $table->enum('motivo', ['pausa_programada', 'pausa_supervisor', 'mantenimiento', 'falla_critica_qa']);
            $table->timestamp('inicio_parada');
            $table->timestamp('fin_parada')->nullable();
            $table->text('comentarios')->nullable();
            $table->timestamps();

            $table->index(['jornada_id', 'fin_parada']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_parada_jornada');
    }
};
