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
        Schema::create('registros_produccion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jornada_id')->constrained('jornadas_produccion')->onDelete('cascade');
            $table->foreignUuid('maquina_id')->constrained('maquinas');

            // Datos reportados por la máquina
            $table->integer('cantidad_producida');
            $table->integer('cantidad_buena');
            $table->integer('cantidad_mala');

            $table->timestamps();

            $table->index(['jornada_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_produccion');
    }
};
