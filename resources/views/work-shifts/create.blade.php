<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Jornada de Trabajo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen" x-data="shiftForm">
        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">⏱️ Iniciar Jornada de Trabajo</h1>
                        <p class="text-gray-600">Registra el inicio de una nueva jornada de producción</p>
                    </div>
                    <a href="{{ route('work-shifts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
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

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('work-shifts.store') }}" class="bg-white rounded-lg shadow-md p-6" @submit="loading = true">
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
                            <div x-show="equipmentId" x-transition class="mt-3">
                                <div x-show="hasActiveShift" class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <p class="text-sm text-red-700">
                                        <strong>⚠️ Advertencia:</strong> Este equipo ya tiene una jornada activa. 
                                        Debe finalizar la jornada actual antes de iniciar una nueva.
                                    </p>
                                </div>
                                <div x-show="!hasActiveShift && equipmentInfo.status" class="p-3 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-gray-700">
                                        <strong>Estado:</strong> <span x-text="equipmentInfo.status"></span> | 
                                        <strong>Capacidad:</strong> <span x-text="equipmentInfo.capacity"></span> unidades/hora
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Plan Selection (Optional) -->
                        <div>
                            <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Plan de Producción (Opcional)
                            </label>
                            <select 
                                id="plan_id" 
                                name="plan_id" 
                                x-model="planId"
                                @change="loadPlanInfo"
                                :disabled="!equipmentId"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'bg-gray-100': !equipmentId }">
                                <option value="">Sin plan asignado</option>
                                <template x-if="equipmentId">
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" data-equipment="{{ $plan->equipment_id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                            Plan #{{ $plan->id }} - {{ $plan->product_name }} ({{ $plan->shift }})
                                        </option>
                                    @endforeach
                                </template>
                            </select>
                            @error('plan_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <p class="mt-2 text-xs text-gray-500">
                                Si seleccionas un plan, la jornada quedará vinculada y se capturará una copia del objetivo.
                            </p>

                            <!-- Plan Preview -->
                            <div x-show="planId && planInfo.product_name" x-transition class="mt-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm font-semibold text-green-800 mb-2">📋 Datos del Plan que se capturarán:</p>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-600">Producto:</span>
                                        <span class="font-semibold ml-2" x-text="planInfo.product_name"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Objetivo:</span>
                                        <span class="font-semibold ml-2" x-text="planInfo.target_quantity"></span> unidades
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shift Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Turno <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition
                                    {{ old('shift_type') == 'morning' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input 
                                        type="radio" 
                                        name="shift_type" 
                                        value="morning"
                                        class="sr-only"
                                        {{ old('shift_type') == 'morning' ? 'checked' : '' }}
                                        required>
                                    <div class="flex-1">
                                        <div class="text-2xl mb-1">🌅</div>
                                        <p class="font-semibold text-gray-900">Mañana</p>
                                        <p class="text-sm text-gray-600">6:00 - 14:00</p>
                                        <p class="text-xs text-gray-500 mt-1">8 horas</p>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition
                                    {{ old('shift_type') == 'afternoon' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input 
                                        type="radio" 
                                        name="shift_type" 
                                        value="afternoon"
                                        class="sr-only"
                                        {{ old('shift_type') == 'afternoon' ? 'checked' : '' }}
                                        required>
                                    <div class="flex-1">
                                        <div class="text-2xl mb-1">☀️</div>
                                        <p class="font-semibold text-gray-900">Tarde</p>
                                        <p class="text-sm text-gray-600">14:00 - 22:00</p>
                                        <p class="text-xs text-gray-500 mt-1">8 horas</p>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition
                                    {{ old('shift_type') == 'night' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input 
                                        type="radio" 
                                        name="shift_type" 
                                        value="night"
                                        class="sr-only"
                                        {{ old('shift_type') == 'night' ? 'checked' : '' }}
                                        required>
                                    <div class="flex-1">
                                        <div class="text-2xl mb-1">🌙</div>
                                        <p class="font-semibold text-gray-900">Noche</p>
                                        <p class="text-sm text-gray-600">22:00 - 6:00</p>
                                        <p class="text-xs text-gray-500 mt-1">8 horas</p>
                                    </div>
                                </label>
                            </div>
                            @error('shift_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Operator Selection -->
                        <div>
                            <label for="operator_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Operador
                            </label>
                            <select 
                                id="operator_id" 
                                name="operator_id" 
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sin operador asignado</option>
                                @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}" {{ old('operator_id') == $operator->id ? 'selected' : '' }}>
                                        {{ $operator->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('operator_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notas
                            </label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                rows="3"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Información adicional sobre la jornada...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Important Info -->
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Importante:</strong> Al iniciar la jornada:
                                    </p>
                                    <ul class="list-disc list-inside text-sm text-yellow-700 mt-2">
                                        <li>Solo puede haber UNA jornada activa por equipo</li>
                                        <li>La hora de inicio se registrará automáticamente</li>
                                        <li>Si hay un plan asociado, se capturará el objetivo actual</li>
                                        <li>Podrás registrar producción desde la vista de detalle</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-4 pt-4 border-t">
                            <a href="{{ route('work-shifts.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Cancelar
                            </a>
                            <button 
                                type="submit" 
                                class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition disabled:bg-gray-300 disabled:cursor-not-allowed"
                                :disabled="loading || hasActiveShift">
                                <span x-show="!loading">Iniciar Jornada</span>
                                <span x-show="loading" class="flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Iniciando...
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
            Alpine.data('shiftForm', () => ({
                equipmentId: '{{ old('equipment_id') }}',
                equipmentInfo: {
                    status: '',
                    capacity: 0
                },
                hasActiveShift: false,
                planId: '{{ old('plan_id') }}',
                planInfo: {
                    product_name: '',
                    target_quantity: 0
                },
                loading: false,

                init() {
                    if (this.equipmentId) {
                        this.loadEquipmentInfo();
                    }
                    if (this.planId) {
                        this.loadPlanInfo();
                    }
                },

                loadEquipmentInfo() {
                    // Equipment data
                    const equipmentData = @json($equipment->keyBy('id'));
                    
                    if (this.equipmentId && equipmentData[this.equipmentId]) {
                        const eq = equipmentData[this.equipmentId];
                        this.equipmentInfo = {
                            status: eq.is_active ? 'Activo' : 'Inactivo',
                            capacity: eq.capacity || 100
                        };

                        // Check for active shift
                        this.checkActiveShift();
                    }

                    // Reset plan when equipment changes
                    this.planId = '';
                    this.planInfo = { product_name: '', target_quantity: 0 };
                },

                async checkActiveShift() {
                    // Simulated check - In production, make AJAX call
                    const activeShifts = @json($activeShifts->keyBy('equipment_id'));
                    this.hasActiveShift = activeShifts.hasOwnProperty(this.equipmentId);
                },

                loadPlanInfo() {
                    const plansData = @json($plans->keyBy('id'));
                    
                    if (this.planId && plansData[this.planId]) {
                        const plan = plansData[this.planId];
                        
                        // Verify plan belongs to selected equipment
                        if (plan.equipment_id != this.equipmentId) {
                            this.planId = '';
                            alert('El plan seleccionado no pertenece a este equipo');
                            return;
                        }

                        this.planInfo = {
                            product_name: plan.product_name,
                            target_quantity: plan.target_quantity
                        };
                    } else {
                        this.planInfo = { product_name: '', target_quantity: 0 };
                    }
                }
            }));
        });
    </script>
</body>
</html>
