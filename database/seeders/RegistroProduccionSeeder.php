<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegistroProduccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📊 Los registros de producción se crean dinámicamente durante las jornadas activas.');
        $this->command->info('   Este seeder está preparado para datos de ejemplo, pero requiere jornadas activas.');
        $this->command->info('   En producción, las máquinas API crean estos registros automáticamente.');

        // Nota: Los registros de producción se crean automáticamente por las máquinas
        // durante las jornadas activas. Este seeder se deja como referencia
        // para crear datos históricos si fuera necesario.

        $this->command->info('✅ RegistroProduccionSeeder preparado (sin datos de ejemplo)');
    }
}
