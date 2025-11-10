<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Plan de Producción</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen" x-data="planForm">
        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">📋 Nuevo Plan de Producción</h1>
                        <p class="text-gray-600">Crea un nuevo plan de producción</p>
                    </div>
                    <a href="{{ route('production-plans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Cancelar
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Alerts -->
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <p class="font-bold mb-2">Por favor corrige los siguientes errores:</p>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('production-plans.store') }}" class="bg-white rounded-lg shadow-md p-6">
                    @csrf

                    <div class="space-y-6">
                        <!-- Equipment Selection -->
                        <div>
                            <label for="equipment_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Equipo <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="equipment_id" 
                                name="equipment_id" 
                                x-model="equipmentId"
                                @change="loadEquipmentInfo"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">Seleccione un equipo</option>
                                @foreach($equipment as $eq)
                                    <option value="{{ $eq->id }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                                        {{ $eq->name }} - {{ $eq->code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('equipment_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Equipment Info -->
                            <div x-show="equipmentId" x-transition class="mt-3 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-gray-700">
                                    <strong>Estado:</strong> <span x-text="equipmentInfo.status"></span> | 
                                    <strong>Capacidad:</strong> <span x-text="equipmentInfo.capacity"></span> unidades/hora
                                </p>
                            </div>
                        </div>

                        <!-- Product Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="product_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre del Producto <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="product_name" 
                                    name="product_name" 
                                    value="{{ old('product_name') }}"
                                    class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Ej: Pieza A100"
                                    required>
                                @error('product_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="product_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Código del Producto
                                </label>
                                <input 
                                    type="text" 
                                    id="product_code" 
                                    name="product_code" 
                                    value="{{ old('product_code') }}"
                                    class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Ej: PRD-1000">
                                @error('product_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Target Quantity -->
                        <div>
                            <label for="target_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Cantidad Objetivo <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    id="target_quantity" 
                                    name="target_quantity" 
                                    value="{{ old('target_quantity') }}"
                                    x-model="targetQuantity"
                                    min="1"
                                    class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="1000"
                                    required>
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">unidades</span>
                            </div>
                            @error('target_quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <p x-show="estimatedHours > 0" x-transition class="mt-2 text-sm text-gray-600">
                                ⏱️ Tiempo estimado: <strong x-text="estimatedHours"></strong> horas
                            </p>
                        </div>

                        <!-- Shift Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Turno <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition
                                    {{ old('shift') == 'morning' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input 
                                        type="radio" 
                                        name="shift" 
                                        value="morning"
                                        class="sr-only"
                                        {{ old('shift') == 'morning' ? 'checked' : '' }}
                                        required>
                                    <div class="flex-1">
                                        <div class="text-2xl mb-1">🌅</div>
                                        <p class="font-semibold text-gray-900">Mañana</p>
                                        <p class="text-sm text-gray-600">6:00 - 14:00</p>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition
                                    {{ old('shift') == 'afternoon' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input 
                                        type="radio" 
                                        name="shift" 
                                        value="afternoon"
                                        class="sr-only"
                                        {{ old('shift') == 'afternoon' ? 'checked' : '' }}
                                        required>
                                    <div class="flex-1">
                                        <div class="text-2xl mb-1">☀️</div>
                                        <p class="font-semibold text-gray-900">Tarde</p>
                                        <p class="text-sm text-gray-600">14:00 - 22:00</p>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition
                                    {{ old('shift') == 'night' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input 
                                        type="radio" 
                                        name="shift" 
                                        value="night"
                                        class="sr-only"
                                        {{ old('shift') == 'night' ? 'checked' : '' }}
                                        required>
                                    <div class="flex-1">
                                        <div class="text-2xl mb-1">🌙</div>
                                        <p class="font-semibold text-gray-900">Noche</p>
                                        <p class="text-sm text-gray-600">22:00 - 6:00</p>
                                    </div>
                                </label>
                            </div>
                            @error('shift')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha de Inicio <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    id="start_date" 
                                    name="start_date" 
                                    value="{{ old('start_date') }}"
                                    x-model="startDate"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha de Fin <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    id="end_date" 
                                    name="end_date" 
                                    value="{{ old('end_date') }}"
                                    x-model="endDate"
                                    :min="startDate || '{{ now()->format('Y-m-d') }}'"
                                    class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notas
                            </label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                rows="4"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Información adicional sobre el plan...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-4 pt-4 border-t">
                            <a href="{{ route('production-plans.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Cancelar
                            </a>
                            <button 
                                type="submit" 
                                class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition"
                                x-bind:disabled="loading"
                                @click="loading = true">
                                <span x-show="!loading">Crear Plan</span>
                                <span x-show="loading" class="flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creando...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('planForm', () => ({
                equipmentId: '{{ old('equipment_id') }}',
                equipmentInfo: {
                    status: '',
                    capacity: 0
                },
                targetQuantity: {{ old('target_quantity', 0) }},
                startDate: '{{ old('start_date') }}',
                endDate: '{{ old('end_date') }}',
                loading: false,

                get estimatedHours() {
                    if (this.targetQuantity > 0 && this.equipmentInfo.capacity > 0) {
                        return (this.targetQuantity / this.equipmentInfo.capacity).toFixed(1);
                    }
                    return 0;
                },

                loadEquipmentInfo() {
                    // Simulated equipment data - In production, fetch from API
                    const equipmentData = @json($equipment->keyBy('id'));
                    
                    if (this.equipmentId && equipmentData[this.equipmentId]) {
                        const eq = equipmentData[this.equipmentId];
                        this.equipmentInfo = {
                            status: eq.status === 'active' ? 'Activo' : 'Inactivo',
                            capacity: eq.capacity || 100
                        };
                    }
                }
            }));
        });
    </script>
</body>
</html>
