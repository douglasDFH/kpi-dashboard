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
        Schema::table('production_plans', function (Blueprint $table) {
            // Cambiar start_date y end_date de date a datetime
            $table->datetime('start_date')->change();
            $table->datetime('end_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_plans', function (Blueprint $table) {
            // Revertir a date
            $table->date('start_date')->change();
            $table->date('end_date')->change();
        });
    }
};
