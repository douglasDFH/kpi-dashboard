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
        Schema::create('work_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('production_plans')->onDelete('set null'); // Plan asociado
            $table->enum('shift_type', ['morning', 'afternoon', 'night']); // Tipo de turno
            $table->timestamp('start_time'); // Hora de inicio real
            $table->timestamp('end_time')->nullable(); // Hora de fin (null si está activo)
            $table->json('target_snapshot')->nullable(); // Snapshot del plan al inicio (JSON)
            $table->integer('actual_production')->default(0); // Producción real acumulada
            $table->integer('good_units')->default(0); // Unidades buenas
            $table->integer('defective_units')->default(0); // Unidades defectuosas
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->foreignId('operator_id')->nullable()->constrained('users')->onDelete('set null'); // Operador del turno
            $table->text('notes')->nullable(); // Notas de la jornada
            $table->timestamps();

            // Índices
            $table->index(['equipment_id', 'status']);
            $table->index(['start_time', 'end_time']);
            $table->index('shift_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_shifts');
    }
};
