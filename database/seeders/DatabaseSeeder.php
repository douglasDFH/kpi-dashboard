<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles, permissions and users
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Seed factory structure
        $this->call([
            AreaSeeder::class,
            MaquinaSeeder::class,
        ]);

        // Seed production plans
        $this->call([
            PlanMaquinaSeeder::class,
        ]);

        // Seed work shifts with schedules
        $this->call([
            JornadaProduccionSeeder::class,
        ]);

        // Seed production data (registros se crean dinámicamente)
        $this->call([
            RegistroProduccionSeeder::class,
        ]);
    }
}
