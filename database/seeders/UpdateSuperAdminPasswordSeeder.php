<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UpdateSuperAdminPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update SuperAdmin password to 123456
        $user = User::where('email', 'admin@ecoplast.com')->first();

        if ($user) {
            $user->update([
                'password' => Hash::make('123456'),
                'updated_at' => now(),
            ]);
            $this->command->info('✅ Contraseña del SuperAdmin actualizada exitosamente a: 123456');
        } else {
            $this->command->warn('⚠️  Usuario admin@ecoplast.com no encontrado');
        }
    }
}
