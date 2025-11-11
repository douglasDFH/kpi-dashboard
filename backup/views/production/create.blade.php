<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Producción - Metalúrgica Precision S.A.</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Metalúrgica Precision S.A.</h1>
                        <p class="text-gray-600">Registrar Producción</p>
                    </div>
                    <a href="{{ route('production.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Registro de Producción Diaria</h2>
                        <p class="text-gray-600 mt-1">Complete los datos de producción del equipo</p>
                    </div>

                    <form action="{{ route('production.store') }}" method="POST" id="productionForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Equipment Selection -->
                            <div class="md:col-span-2">
                                <label for="equipment_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Equipo <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="equipment_id"
                                    name="equipment_id"
                                    required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('equipment_id') border-red-500 @enderror"
                                >
                                    <option value="">Seleccione un equipo</option>
                                    @foreach($equipment as $eq)
                                        <option value="{{ $eq->id }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                                            {{ $eq->name }} ({{ $eq->code }}) - {{ $eq->location }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('equipment_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Production Date -->
                            <div class="md:col-span-2">
                                <label for="production_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha y Hora de Producción <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    id="production_date"
                                    name="production_date"
                                    value="{{ old('production_date', now()->format('Y-m-d\TH:i')) }}"
                                    required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('production_date') border-red-500 @enderror"
                                >
                                @error('production_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Planned Production -->
                            <div>
                                <label for="planned_production" class="block text-sm font-medium text-gray-700 mb-2">
                                    Producción Planificada <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="planned_production"
                                    name="planned_production"
                                    value="{{ old('planned_production') }}"
                                    required
                                    min="1"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('planned_production') border-red-500 @enderror"
                                    placeholder="Ej: 1000"
                                >
                                @error('planned_production')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Unidades planificadas a producir</p>
                            </div>

                            <!-- Actual Production -->
                            <div>
                                <label for="actual_production" class="block text-sm font-medium text-gray-700 mb-2">
                                    Producción Real <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="actual_production"
                                    name="actual_production"
                                    value="{{ old('actual_production') }}"
                                    required
                                    min="0"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('actual_production') border-red-500 @enderror"
                                    placeholder="Ej: 950"
                                    onchange="calculateDefective()"
                                >
                                @error('actual_production')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Unidades realmente producidas</p>
                            </div>

                            <!-- Good Units -->
                            <div>
                                <label for="good_units" class="block text-sm font-medium text-gray-700 mb-2">
                                    Unidades Buenas <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="good_units"
                                    name="good_units"
                                    value="{{ old('good_units') }}"
                                    required
                                    min="0"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('good_units') border-red-500 @enderror"
                                    placeholder="Ej: 900"
                                    onchange="calculateDefective()"
                                >
                                @error('good_units')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Unidades sin defectos</p>
                            </div>

                            <!-- Defective Units -->
                            <div>
                                <label for="defective_units" class="block text-sm font-medium text-gray-700 mb-2">
                                    Unidades Defectuosas <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="defective_units"
                                    name="defective_units"
                                    value="{{ old('defective_units', 0) }}"
                                    required
                                    min="0"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('defective_units') border-red-500 @enderror"
                                    placeholder="Ej: 50"
                                    readonly
                                >
                                @error('defective_units')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Calculado automáticamente</p>
                            </div>

                            <!-- Cycle Time -->
                            <div class="md:col-span-2">
                                <label for="cycle_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tiempo de Ciclo (minutos) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    step="0.1"
                                    id="cycle_time"
                                    name="cycle_time"
                                    value="{{ old('cycle_time') }}"
                                    required
                                    min="0.1"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('cycle_time') border-red-500 @enderror"
                                    placeholder="Ej: 5.5"
                                >
                                @error('cycle_time')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Tiempo promedio para producir una unidad</p>
                            </div>
                        </div>

                        <!-- Calculation Summary -->
                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <h3 class="text-sm font-medium text-blue-900 mb-4">Resumen de Cálculo</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs text-blue-700">Eficiencia de Producción</p>
                                    <p class="text-2xl font-bold text-blue-900" id="efficiency">0%</p>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-700">Tasa de Calidad</p>
                                    <p class="text-2xl font-bold text-blue-900" id="quality_rate">0%</p>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-700">Tasa de Defectos</p>
                                    <p class="text-2xl font-bold text-blue-900" id="defect_rate">0%</p>
                                </div>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-yellow-700">
                                    <p class="font-medium">Importante:</p>
                                    <p class="mt-1">Asegúrese de que las unidades buenas + unidades defectuosas = producción real.</p>
                                    <p class="mt-1">Las unidades defectuosas se calculan automáticamente.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                            <a href="{{ route('production.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition shadow-md">
                                Guardar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function calculateDefective() {
            const actualProduction = parseInt(document.getElementById('actual_production').value) || 0;
            const goodUnits = parseInt(document.getElementById('good_units').value) || 0;
            const plannedProduction = parseInt(document.getElementById('planned_production').value) || 0;

            // Calculate defective units
            const defectiveUnits = actualProduction - goodUnits;
            document.getElementById('defective_units').value = defectiveUnits >= 0 ? defectiveUnits : 0;

            // Calculate efficiency
            const efficiency = plannedProduction > 0 ? (actualProduction / plannedProduction) * 100 : 0;
            document.getElementById('efficiency').textContent = efficiency.toFixed(1) + '%';

            // Calculate quality rate
            const qualityRate = actualProduction > 0 ? (goodUnits / actualProduction) * 100 : 0;
            document.getElementById('quality_rate').textContent = qualityRate.toFixed(1) + '%';

            // Calculate defect rate
            const defectRate = actualProduction > 0 ? (defectiveUnits / actualProduction) * 100 : 0;
            document.getElementById('defect_rate').textContent = defectRate.toFixed(1) + '%';
        }

        // Recalculate when planned production changes
        document.getElementById('planned_production').addEventListener('input', calculateDefective);

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', calculateDefective);
    </script>
</body>
</html>
