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
        Schema::create('registros_mantenimiento', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('maquina_id')->constrained('maquinas');
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('set null');
            $table->foreignUuid('jornada_id')->nullable()->constrained('jornadas_produccion')->onDelete('set null');
            $table->enum('tipo', ['preventivo', 'correctivo', 'calibracion']);
            $table->text('descripcion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_mantenimiento');
    }
};
