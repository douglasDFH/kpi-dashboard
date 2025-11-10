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
        Schema::create('planes_maquina', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('maquina_id')->constrained('maquinas');
            $table->string('nombre_plan', 255);
            $table->integer('objetivo_unidades')->default(1000);
            $table->string('unidad_medida', 50)->default('piezas');
            $table->float('ideal_cycle_time_seconds')->default(0);
            $table->integer('limite_fallos_critico')->default(10);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['maquina_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planes_maquina');
    }
};
