<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Inspección de Calidad - Metalúrgica Precision S.A.</title>
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
                        <p class="text-gray-600">Editar Inspección de Calidad</p>
                    </div>
                    <a href="{{ route('quality.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
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
                        <h2 class="text-2xl font-bold text-gray-800">Editar Inspección de Calidad</h2>
                        <p class="text-gray-600 mt-1">Actualice los datos de la inspección</p>
                    </div>

                    <form action="{{ route('quality.update', $quality) }}" method="POST" id="qualityForm">
                        @csrf
                        @method('PUT')

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
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('equipment_id') border-red-500 @enderror"
                                >
                                    <option value="">Seleccione un equipo</option>
                                    @foreach($equipment as $eq)
                                        <option value="{{ $eq->id }}" {{ old('equipment_id', $quality->equipment_id) == $eq->id ? 'selected' : '' }}>
                                            {{ $eq->name }} ({{ $eq->code }}) - {{ $eq->location }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('equipment_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Inspection Date -->
                            <div class="md:col-span-2">
                                <label for="inspection_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha y Hora de Inspección <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    id="inspection_date"
                                    name="inspection_date"
                                    value="{{ old('inspection_date', $quality->inspection_date->format('Y-m-d\TH:i')) }}"
                                    required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('inspection_date') border-red-500 @enderror"
                                >
                                @error('inspection_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Inspected -->
                            <div>
                                <label for="total_inspected" class="block text-sm font-medium text-gray-700 mb-2">
                                    Total Inspeccionado <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="total_inspected"
                                    name="total_inspected"
                                    value="{{ old('total_inspected', $quality->total_inspected) }}"
                                    min="1"
                                    required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('total_inspected') border-red-500 @enderror"
                                    placeholder="0"
                                    readonly
                                >
                                @error('total_inspected')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Se calcula automáticamente (Aprobadas + Rechazadas)</p>
                            </div>

                            <!-- Approved Units -->
                            <div>
                                <label for="approved_units" class="block text-sm font-medium text-gray-700 mb-2">
                                    Unidades Aprobadas <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="approved_units"
                                    name="approved_units"
                                    value="{{ old('approved_units', $quality->approved_units) }}"
                                    min="0"
                                    required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('approved_units') border-red-500 @enderror"
                                    placeholder="0"
                                    onchange="calculateTotal()"
                                >
                                @error('approved_units')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Rejected Units -->
                            <div>
                                <label for="rejected_units" class="block text-sm font-medium text-gray-700 mb-2">
                                    Unidades Rechazadas <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="rejected_units"
                                    name="rejected_units"
                                    value="{{ old('rejected_units', $quality->rejected_units) }}"
                                    min="0"
                                    required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('rejected_units') border-red-500 @enderror"
                                    placeholder="0"
                                    onchange="calculateTotal()"
                                >
                                @error('rejected_units')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Defect Type -->
                            <div>
                                <label for="defect_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Defecto
                                </label>
                                <select
                                    id="defect_type"
                                    name="defect_type"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('defect_type') border-red-500 @enderror"
                                >
                                    <option value="">Seleccione tipo (opcional)</option>
                                    <optgroup label="Defectos Dimensionales">
                                        <option value="Fuera de Tolerancia" {{ old('defect_type', $quality->defect_type) == 'Fuera de Tolerancia' ? 'selected' : '' }}>Fuera de Tolerancia</option>
                                        <option value="Medida Incorrecta" {{ old('defect_type', $quality->defect_type) == 'Medida Incorrecta' ? 'selected' : '' }}>Medida Incorrecta</option>
                                        <option value="Deformación" {{ old('defect_type', $quality->defect_type) == 'Deformación' ? 'selected' : '' }}>Deformación</option>
                                    </optgroup>
                                    <optgroup label="Defectos Superficiales">
                                        <option value="Rayado" {{ old('defect_type', $quality->defect_type) == 'Rayado' ? 'selected' : '' }}>Rayado</option>
                                        <option value="Corrosión" {{ old('defect_type', $quality->defect_type) == 'Corrosión' ? 'selected' : '' }}>Corrosión</option>
                                        <option value="Rebaba" {{ old('defect_type', $quality->defect_type) == 'Rebaba' ? 'selected' : '' }}>Rebaba</option>
                                        <option value="Porosidad" {{ old('defect_type', $quality->defect_type) == 'Porosidad' ? 'selected' : '' }}>Porosidad</option>
                                    </optgroup>
                                    <optgroup label="Defectos de Material">
                                        <option value="Grieta" {{ old('defect_type', $quality->defect_type) == 'Grieta' ? 'selected' : '' }}>Grieta</option>
                                        <option value="Inclusión" {{ old('defect_type', $quality->defect_type) == 'Inclusión' ? 'selected' : '' }}>Inclusión</option>
                                        <option value="Dureza Inadecuada" {{ old('defect_type', $quality->defect_type) == 'Dureza Inadecuada' ? 'selected' : '' }}>Dureza Inadecuada</option>
                                    </optgroup>
                                    <optgroup label="Otros">
                                        <option value="Ensamblaje Defectuoso" {{ old('defect_type', $quality->defect_type) == 'Ensamblaje Defectuoso' ? 'selected' : '' }}>Ensamblaje Defectuoso</option>
                                        <option value="Falta de Componente" {{ old('defect_type', $quality->defect_type) == 'Falta de Componente' ? 'selected' : '' }}>Falta de Componente</option>
                                        <option value="Otros" {{ old('defect_type', $quality->defect_type) == 'Otros' ? 'selected' : '' }}>Otros</option>
                                    </optgroup>
                                </select>
                                @error('defect_type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notas y Observaciones
                                </label>
                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows="4"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                                    placeholder="Describa detalles adicionales de la inspección..."
                                >{{ old('notes', $quality->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Quality Summary -->
                        <div class="mt-6 bg-purple-50 border border-purple-200 rounded-lg p-6">
                            <h3 class="text-sm font-medium text-purple-900 mb-3">Resumen de Calidad</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs text-purple-700">Total a Inspeccionar</p>
                                    <p class="text-2xl font-bold text-purple-900" id="summary_total">{{ $quality->total_inspected }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-purple-700">Porcentaje de Calidad</p>
                                    <p class="text-2xl font-bold text-purple-900" id="summary_quality">{{ number_format($quality->quality_percentage, 1) }}%</p>
                                </div>
                                <div>
                                    <p class="text-xs text-purple-700">Tasa de Rechazo</p>
                                    <p class="text-2xl font-bold text-purple-900" id="summary_reject">{{ number_format($quality->reject_rate, 1) }}%</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                            <a href="{{ route('quality.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition shadow-md">
                                Actualizar Inspección
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function calculateTotal() {
            const approved = parseInt(document.getElementById('approved_units').value) || 0;
            const rejected = parseInt(document.getElementById('rejected_units').value) || 0;
            const total = approved + rejected;

            document.getElementById('total_inspected').value = total;
            updateSummary(total, approved, rejected);
        }

        function updateSummary(total, approved, rejected) {
            document.getElementById('summary_total').textContent = total;

            if (total > 0) {
                const qualityPercent = (approved / total * 100).toFixed(1);
                const rejectPercent = (rejected / total * 100).toFixed(1);

                document.getElementById('summary_quality').textContent = qualityPercent + '%';
                document.getElementById('summary_reject').textContent = rejectPercent + '%';
            } else {
                document.getElementById('summary_quality').textContent = '--%';
                document.getElementById('summary_reject').textContent = '--%';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
</body>
</html>
