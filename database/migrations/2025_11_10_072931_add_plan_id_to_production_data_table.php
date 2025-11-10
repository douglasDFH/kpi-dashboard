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
        Schema::table('production_data', function (Blueprint $table) {
            // Agregar columna plan_id para vincular con production_plans
            $table->foreignId('plan_id')->nullable()->after('equipment_id')
                  ->constrained('production_plans')->onDelete('set null');
            
            // Agregar columna work_shift_id para rastrear jornada
            $table->foreignId('work_shift_id')->nullable()->after('plan_id')
                  ->constrained('work_shifts')->onDelete('set null');
            
            // Índice para consultas frecuentes
            $table->index(['plan_id', 'production_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_data', function (Blueprint $table) {
            $table->dropForeign(['work_shift_id']);
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['work_shift_id', 'plan_id']);
        });
    }
};
