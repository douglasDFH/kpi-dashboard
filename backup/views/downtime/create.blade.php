<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Tiempo Muerto - Metalúrgica Precision S.A.</title>
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
                        <p class="text-gray-600">Registrar Tiempo Muerto</p>
                    </div>
                    <a href="{{ route('downtime.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
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
                        <h2 class="text-2xl font-bold text-gray-800">Registro de Tiempo Muerto (Downtime)</h2>
                        <p class="text-gray-600 mt-1">Registre los paros y tiempos de inactividad del equipo</p>
                    </div>

                    <form action="{{ route('downtime.store') }}" method="POST" id="downtimeForm">
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
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('equipment_id') border-red-500 @enderror"
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

                            <!-- Start Time -->
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hora de Inicio <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    id="start_time"
                                    name="start_time"
                                    value="{{ old('start_time', now()->format('Y-m-d\TH:i')) }}"
                                    required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('start_time') border-red-500 @enderror"
                                    onchange="calculateDuration()"
                                >
                                @error('start_time')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Time -->
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hora de Fin
                                </label>
                                <input
                                    type="datetime-local"
                                    id="end_time"
                                    name="end_time"
                                    value="{{ old('end_time') }}"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('end_time') border-red-500 @enderror"
                                    onchange="calculateDuration()"
                                >
                                @error('end_time')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Dejar vacío si el paro aún está en curso</p>
                            </div>

                            <!-- Duration -->
                            <div>
                                <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Duración (minutos)
                                </label>
                                <input
                                    type="number"
                                    id="duration_minutes"
                                    name="duration_minutes"
                                    value="{{ old('duration_minutes') }}"
                                    min="1"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('duration_minutes') border-red-500 @enderror"
                                    placeholder="Se calcula automáticamente"
                                    readonly
                                >
                                @error('duration_minutes')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Calculado desde hora inicio/fin</p>
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                    Categoría <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="category"
                                    name="category"
                                    required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('category') border-red-500 @enderror"
                                >
                                    <option value="">Seleccione categoría</option>
                                    <option value="planificado" {{ old('category') == 'planificado' ? 'selected' : '' }}>Planificado (Mantenimiento programado)</option>
                                    <option value="no planificado" {{ old('category') == 'no planificado' ? 'selected' : '' }}>No Planificado (Fallas, averías)</option>
                                </select>
                                @error('category')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reason -->
                            <div class="md:col-span-2">
                                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Razón del Paro <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="reason"
                                    name="reason"
                                    required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('reason') border-red-500 @enderror"
                                >
                                    <option value="">Seleccione razón</option>
                                    <optgroup label="Mantenimiento">
                                        <option value="Mantenimiento Preventivo" {{ old('reason') == 'Mantenimiento Preventivo' ? 'selected' : '' }}>Mantenimiento Preventivo</option>
                                        <option value="Mantenimiento Correctivo" {{ old('reason') == 'Mantenimiento Correctivo' ? 'selected' : '' }}>Mantenimiento Correctivo</option>
                                        <option value="Calibración" {{ old('reason') == 'Calibración' ? 'selected' : '' }}>Calibración</option>
                                    </optgroup>
                                    <optgroup label="Fallas">
                                        <option value="Falla Mecánica" {{ old('reason') == 'Falla Mecánica' ? 'selected' : '' }}>Falla Mecánica</option>
                                        <option value="Falla Eléctrica" {{ old('reason') == 'Falla Eléctrica' ? 'selected' : '' }}>Falla Eléctrica</option>
                                        <option value="Falla Hidráulica" {{ old('reason') == 'Falla Hidráulica' ? 'selected' : '' }}>Falla Hidráulica</option>
                                        <option value="Falla Neumática" {{ old('reason') == 'Falla Neumática' ? 'selected' : '' }}>Falla Neumática</option>
                                    </optgroup>
                                    <optgroup label="Operación">
                                        <option value="Cambio de Herramienta" {{ old('reason') == 'Cambio de Herramienta' ? 'selected' : '' }}>Cambio de Herramienta</option>
                                        <option value="Ajuste de Máquina" {{ old('reason') == 'Ajuste de Máquina' ? 'selected' : '' }}>Ajuste de Máquina</option>
                                        <option value="Falta de Material" {{ old('reason') == 'Falta de Material' ? 'selected' : '' }}>Falta de Material</option>
                                        <option value="Falta de Personal" {{ old('reason') == 'Falta de Personal' ? 'selected' : '' }}>Falta de Personal</option>
                                        <option value="Cambio de Producto" {{ old('reason') == 'Cambio de Producto' ? 'selected' : '' }}>Cambio de Producto</option>
                                    </optgroup>
                                    <optgroup label="Otros">
                                        <option value="Corte de Energía" {{ old('reason') == 'Corte de Energía' ? 'selected' : '' }}>Corte de Energía</option>
                                        <option value="Otros" {{ old('reason') == 'Otros' ? 'selected' : '' }}>Otros</option>
                                    </optgroup>
                                </select>
                                @error('reason')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Descripción Detallada
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="4"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                    placeholder="Describa detalles adicionales del paro..."
                                >{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Información adicional sobre la causa o solución del paro</p>
                            </div>
                        </div>

                        <!-- Info Summary -->
                        <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-6">
                            <h3 class="text-sm font-medium text-red-900 mb-3">Resumen del Tiempo Muerto</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs text-red-700">Duración</p>
                                    <p class="text-2xl font-bold text-red-900" id="duration_display">-- min</p>
                                </div>
                                <div>
                                    <p class="text-xs text-red-700">Impacto en Disponibilidad</p>
                                    <p class="text-lg font-bold text-red-900">Alto</p>
                                </div>
                                <div>
                                    <p class="text-xs text-red-700">Tipo</p>
                                    <p class="text-lg font-bold text-red-900" id="category_display">--</p>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Box -->
                        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="text-sm text-yellow-700">
                                    <p class="font-medium">Importante:</p>
                                    <ul class="mt-1 list-disc list-inside">
                                        <li>Registre los tiempos muertos inmediatamente cuando ocurran</li>
                                        <li>Si el paro continúa, puede dejar la hora de fin vacía</li>
                                        <li>La duración se calcula automáticamente desde inicio/fin</li>
                                        <li>Los tiempos muertos afectan directamente la Disponibilidad del equipo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                            <a href="{{ route('downtime.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition shadow-md">
                                Guardar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function calculateDuration() {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;

            if (startTime && endTime) {
                const start = new Date(startTime);
                const end = new Date(endTime);

                // Calculate difference in minutes
                const diffMs = end - start;
                const diffMinutes = Math.floor(diffMs / 60000);

                if (diffMinutes > 0) {
                    document.getElementById('duration_minutes').value = diffMinutes;
                    document.getElementById('duration_display').textContent = diffMinutes + ' min';
                } else {
                    document.getElementById('duration_minutes').value = '';
                    document.getElementById('duration_display').textContent = '-- min';
                }
            } else {
                document.getElementById('duration_display').textContent = 'En curso';
            }
        }

        // Update category display
        document.getElementById('category').addEventListener('change', function() {
            const categoryDisplay = document.getElementById('category_display');
            if (this.value === 'planificado') {
                categoryDisplay.textContent = 'Planificado';
            } else if (this.value === 'no planificado') {
                categoryDisplay.textContent = 'No Planificado';
            } else {
                categoryDisplay.textContent = '--';
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            calculateDuration();
        });
    </script>
</body>
</html>
