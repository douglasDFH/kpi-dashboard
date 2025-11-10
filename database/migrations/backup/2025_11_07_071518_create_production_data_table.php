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
        Schema::create('production_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->integer('planned_production'); // Producción planificada
            $table->integer('actual_production'); // Producción real
            $table->integer('good_units'); // Unidades buenas
            $table->integer('defective_units'); // Unidades defectuosas
            $table->decimal('cycle_time', 8, 2); // Tiempo de ciclo en minutos
            $table->timestamp('production_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_data');
    }
};
