<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UpdateSuperAdminPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update SuperAdmin password to 123456
        User::where('email', 'admin@ecoplast.com')->update([
            'password' => Hash::make('123456'),
            'updated_at' => now()
        ]);

        $this->command->info('✅ Contraseña del SuperAdmin actualizada exitosamente a: 123456');
        $this->command->info('📧 Email: admin@ecoplast.com');
    }
}
