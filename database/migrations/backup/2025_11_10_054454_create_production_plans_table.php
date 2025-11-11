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
        Schema::create('production_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->string('product_name'); // Nombre del producto a fabricar
            $table->string('product_code')->nullable(); // Código del producto
            $table->integer('target_quantity'); // Cantidad objetivo
            $table->enum('shift', ['morning', 'afternoon', 'night']); // Turno
            $table->date('start_date'); // Fecha de inicio del plan
            $table->date('end_date'); // Fecha de fin del plan
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Usuario que creó el plan
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();

            // Índices para consultas frecuentes
            $table->index(['equipment_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index('shift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_plans');
    }
};
