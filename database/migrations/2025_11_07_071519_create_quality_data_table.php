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
        Schema::create('quality_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->integer('total_inspected'); // Total de unidades inspeccionadas
            $table->integer('approved_units'); // Unidades aprobadas
            $table->integer('rejected_units'); // Unidades rechazadas
            $table->string('defect_type')->nullable(); // Tipo de defecto
            $table->text('notes')->nullable();
            $table->timestamp('inspection_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_data');
    }
};
