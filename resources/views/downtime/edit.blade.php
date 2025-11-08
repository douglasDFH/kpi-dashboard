<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tiempo Muerto - Metalúrgica Precision S.A.</title>
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
                        <p class="text-gray-600">Editar Tiempo Muerto</p>
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
                        <h2 class="text-2xl font-bold text-gray-800">Editar Registro de Tiempo Muerto</h2>
                        <p class="text-gray-600 mt-1">Actualice los datos del tiempo de inactividad</p>
                    </div>

                    <form action="{{ route('downtime.update', $downtime) }}" method="POST" id="downtimeForm">
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
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('equipment_id') border-red-500 @enderror"
                                >
                                    <option value="">Seleccione un equipo</option>
                                    @foreach($equipment as $eq)
                                        <option value="{{ $eq->id }}" {{ old('equipment_id', $downtime->equipment_id) == $eq->id ? 'selected' : '' }}>
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
                                    value="{{ old('start_time', $downtime->start_time->format('Y-m-d\TH:i')) }}"
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
                                    value="{{ old('end_time', $downtime->end_time ? $downtime->end_time->format('Y-m-d\TH:i') : '') }}"
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
                                    value="{{ old('duration_minutes', $downtime->duration_minutes) }}"
                                    min="1"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('duration_minutes') border-red-500 @enderror"
                                    readonly
                                >
                                @error('duration_minutes')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                    <option value="planificado" {{ old('category', $downtime->category) == 'planificado' ? 'selected' : '' }}>Planificado</option>
                                    <option value="no planificado" {{ old('category', $downtime->category) == 'no planificado' ? 'selected' : '' }}>No Planificado</option>
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
                                        <option value="Mantenimiento Preventivo" {{ old('reason', $downtime->reason) == 'Mantenimiento Preventivo' ? 'selected' : '' }}>Mantenimiento Preventivo</option>
                                        <option value="Mantenimiento Correctivo" {{ old('reason', $downtime->reason) == 'Mantenimiento Correctivo' ? 'selected' : '' }}>Mantenimiento Correctivo</option>
                                        <option value="Calibración" {{ old('reason', $downtime->reason) == 'Calibración' ? 'selected' : '' }}>Calibración</option>
                                    </optgroup>
                                    <optgroup label="Fallas">
                                        <option value="Falla Mecánica" {{ old('reason', $downtime->reason) == 'Falla Mecánica' ? 'selected' : '' }}>Falla Mecánica</option>
                                        <option value="Falla Eléctrica" {{ old('reason', $downtime->reason) == 'Falla Eléctrica' ? 'selected' : '' }}>Falla Eléctrica</option>
                                        <option value="Falla Hidráulica" {{ old('reason', $downtime->reason) == 'Falla Hidráulica' ? 'selected' : '' }}>Falla Hidráulica</option>
                                        <option value="Falla Neumática" {{ old('reason', $downtime->reason) == 'Falla Neumática' ? 'selected' : '' }}>Falla Neumática</option>
                                    </optgroup>
                                    <optgroup label="Operación">
                                        <option value="Cambio de Herramienta" {{ old('reason', $downtime->reason) == 'Cambio de Herramienta' ? 'selected' : '' }}>Cambio de Herramienta</option>
                                        <option value="Ajuste de Máquina" {{ old('reason', $downtime->reason) == 'Ajuste de Máquina' ? 'selected' : '' }}>Ajuste de Máquina</option>
                                        <option value="Falta de Material" {{ old('reason', $downtime->reason) == 'Falta de Material' ? 'selected' : '' }}>Falta de Material</option>
                                        <option value="Falta de Personal" {{ old('reason', $downtime->reason) == 'Falta de Personal' ? 'selected' : '' }}>Falta de Personal</option>
                                        <option value="Cambio de Producto" {{ old('reason', $downtime->reason) == 'Cambio de Producto' ? 'selected' : '' }}>Cambio de Producto</option>
                                    </optgroup>
                                    <optgroup label="Otros">
                                        <option value="Corte de Energía" {{ old('reason', $downtime->reason) == 'Corte de Energía' ? 'selected' : '' }}>Corte de Energía</option>
                                        <option value="Otros" {{ old('reason', $downtime->reason) == 'Otros' ? 'selected' : '' }}>Otros</option>
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
                                >{{ old('description', $downtime->description) }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                            <a href="{{ route('downtime.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition shadow-md">
                                Actualizar Registro
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

                const diffMs = end - start;
                const diffMinutes = Math.floor(diffMs / 60000);

                if (diffMinutes > 0) {
                    document.getElementById('duration_minutes').value = diffMinutes;
                } else {
                    document.getElementById('duration_minutes').value = '';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            calculateDuration();
        });
    </script>
</body>
</html>
