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
        // Tabla de roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // superadmin, admin, gerente, supervisor, operador, calidad, mantenimiento
            $table->string('display_name'); // Nombre para mostrar
            $table->text('description')->nullable();
            $table->integer('level')->default(0); // Nivel jerárquico (1=más alto, 7=más bajo)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabla de permisos
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // equipment.view, production.create, etc.
            $table->string('display_name');
            $table->string('module'); // equipment, production, quality, downtime, reports, users
            $table->string('action'); // view, create, edit, delete, export
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tabla pivot roles-permisos
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });

        // Agregar role_id a users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('email')->constrained()->onDelete('set null');
            $table->boolean('is_active')->default(true)->after('role_id');
            $table->string('phone')->nullable()->after('is_active');
            $table->string('position')->nullable()->after('phone'); // Cargo/Puesto
            $table->timestamp('last_login_at')->nullable()->after('position');
        });

        // Tabla de auditoría
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // created, updated, deleted, viewed, exported, login, logout
            $table->string('model_type')->nullable(); // App\Models\Equipment, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description');
            $table->json('old_values')->nullable(); // Valores anteriores
            $table->json('new_values')->nullable(); // Valores nuevos
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'is_active', 'phone', 'position', 'last_login_at']);
        });

        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
