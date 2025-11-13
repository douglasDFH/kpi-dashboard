<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar nuevo estado a la columna enum
        DB::statement("ALTER TABLE work_shifts MODIFY COLUMN status ENUM('active', 'pending_registration', 'completed', 'cancelled') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        // Revertir al enum original
        DB::statement("ALTER TABLE work_shifts MODIFY COLUMN status ENUM('active', 'completed', 'cancelled') NOT NULL DEFAULT 'active'");
    }
};
