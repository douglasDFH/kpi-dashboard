<?php

namespace Database\Seeders;

use App\Models\User;
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

        // Seed KPI Dashboard data
        $this->call([
            EquipmentSeeder::class,
            ProductionPlanSeeder::class,
            ProductionDataSeeder::class,
            QualityDataSeeder::class,
            DowntimeDataSeeder::class,
        ]);
    }
}
