<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jornada #{{ $shift->id }} - KPI Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen" x-data="shiftMonitor">
        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center space-x-4">
                            <h1 class="text-3xl font-bold text-gray-800">⏱️ Jornada #{{ $shift->id }}</h1>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $shift->status == 'active' ? 'bg-blue-100 text-blue-800 animate-pulse' : '' }}
                                {{ $shift->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $shift->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ $shift->status == 'active' ? '▶️ Activo' : '' }}
                                {{ $shift->status == 'completed' ? '✅ Completado' : '' }}
                                {{ $shift->status == 'cancelled' ? '❌ Cancelado' : '' }}
                            </span>
                        </div>
                        <p class="text-gray-600 mt-1">{{ $shift->equipment->name }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('work-shifts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver
                        </a>

                        @if($shift->status == 'active')
                            <form method="POST" action="{{ route('work-shifts.end', $shift) }}" class="inline" onsubmit="return confirm('¿Está seguro de finalizar esta jornada?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Finalizar Jornada
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <!-- Alerts -->
            <div x-show="alert.show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 :class="alert.type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'"
                 class="border px-4 py-3 rounded mb-4">
                <p x-text="alert.message"></p>
            </div>

            <!-- Real-time Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Production Counter -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Producción Total</p>
                            <p class="text-3xl font-bold text-gray-900" x-text="actualProduction"></p>
                            <p class="text-xs text-gray-500 mt-1">
                                de <span x-text="targetQuantity"></span> unidades
                            </p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Progress -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Progreso</p>
                            <p class="text-3xl font-bold text-blue-600" x-text="progress + '%'"></p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" :style="`width: ${progress}%`"></div>
                            </div>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Quality Rate -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Tasa de Calidad</p>
                            <p class="text-3xl font-bold" 
                               :class="{
                                   'text-green-600': qualityRate >= 95,
                                   'text-yellow-600': qualityRate >= 90 && qualityRate < 95,
                                   'text-red-600': qualityRate < 90
                               }"
                               x-text="qualityRate.toFixed(1) + '%'"></p>
                            <p class="text-xs text-gray-500 mt-1">
                                ✅ <span x-text="goodUnits"></span> | ❌ <span x-text="defectiveUnits"></span>
                            </p>
                        </div>
                        <div class="p-3 rounded-full"
                             :class="{
                                 'bg-green-100': qualityRate >= 95,
                                 'bg-yellow-100': qualityRate >= 90 && qualityRate < 95,
                                 'bg-red-100': qualityRate < 90
                             }">
                            <svg class="h-8 w-8"
                                 :class="{
                                     'text-green-600': qualityRate >= 95,
                                     'text-yellow-600': qualityRate >= 90 && qualityRate < 95,
                                     'text-red-600': qualityRate < 90
                                 }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Duration -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Duración</p>
                            <p class="text-3xl font-bold text-gray-900" x-text="durationFormatted"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="shiftTimeRange"></p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Shift Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Info Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Información de la Jornada</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Equipo</p>
                                <p class="text-lg font-semibold">{{ $shift->equipment->name }}</p>
                                <p class="text-sm text-gray-500">{{ $shift->equipment->code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Turno</p>
                                <p class="text-lg font-semibold">
                                    {{ $shift->shift_type == 'morning' ? '🌅 Mañana' : '' }}
                                    {{ $shift->shift_type == 'afternoon' ? '☀️ Tarde' : '' }}
                                    {{ $shift->shift_type == 'night' ? '🌙 Noche' : '' }}
                                </p>
                            </div>
                            @if($shift->plan)
                                <div>
                                    <p class="text-sm text-gray-600">Plan de Producción</p>
                                    <a href="{{ route('production-plans.show', $shift->plan) }}" class="text-lg font-semibold text-blue-600 hover:underline">
                                        #{{ $shift->plan->id }} - {{ $shift->plan->product_name }}
                                    </a>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Producto</p>
                                    <p class="text-lg font-semibold">{{ $shift->target_snapshot['product_name'] ?? 'N/A' }}</p>
                                    @if(isset($shift->target_snapshot['product_code']))
                                        <p class="text-sm text-gray-500">{{ $shift->target_snapshot['product_code'] }}</p>
                                    @endif
                                </div>
                            @endif
                            @if($shift->operator)
                                <div>
                                    <p class="text-sm text-gray-600">Operador</p>
                                    <p class="text-lg font-semibold">{{ $shift->operator->name }}</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-600">Inicio</p>
                                <p class="text-lg font-semibold">{{ $shift->start_time->format('d/m/Y H:i') }}</p>
                            </div>
                            @if($shift->end_time)
                                <div>
                                    <p class="text-sm text-gray-600">Fin</p>
                                    <p class="text-lg font-semibold">{{ $shift->end_time->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif
                        </div>

                        @if($shift->notes)
                            <div class="mt-4 pt-4 border-t">
                                <p class="text-sm text-gray-600 mb-2">Notas</p>
                                <p class="text-gray-800">{{ $shift->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Production Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Progreso de Producción</h2>
                        
                        <!-- Debug Info (solo visible si hay problemas) -->
                        <div id="chartDebug" class="hidden mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-sm text-yellow-800">
                                <strong>⚠️ Información de depuración:</strong>
                                <br>Presiona F12 para ver la consola del navegador.
                            </p>
                        </div>
                        
                        <canvas id="productionChart"></canvas>
                    </div>
                </div>

                <!-- Production Recording (Only for Active Shifts) -->
                @if($shift->status == 'active')
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">📊 Registrar Producción</h2>
                            
                            <form @submit.prevent="recordProduction" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Cantidad Total <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        x-model="form.quantity"
                                        min="1"
                                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="100"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Unidades Buenas <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        x-model="form.good_units"
                                        @input="form.defective_units = Math.max(0, form.quantity - form.good_units)"
                                        min="0"
                                        :max="form.quantity"
                                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="95"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Unidades Defectuosas <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        x-model="form.defective_units"
                                        @input="form.good_units = Math.max(0, form.quantity - form.defective_units)"
                                        min="0"
                                        :max="form.quantity"
                                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="5"
                                        required>
                                </div>

                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-600">
                                        <strong>Validación:</strong><br>
                                        Total = Buenas + Defectuosas<br>
                                        <span x-text="form.quantity"></span> = 
                                        <span x-text="form.good_units"></span> + 
                                        <span x-text="form.defective_units"></span>
                                        <span x-show="!isFormValid" class="text-red-600 block mt-1">
                                            ⚠️ Los valores no coinciden
                                        </span>
                                    </p>
                                </div>

                                <button 
                                    type="submit" 
                                    :disabled="!isFormValid || submitting"
                                    class="w-full px-4 py-3 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white font-medium rounded-lg transition">
                                    <span x-show="!submitting">Registrar</span>
                                    <span x-show="submitting" class="flex items-center justify-center">
                                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Registrando...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">📊 Métricas Finales</h2>
                            <div class="space-y-4">
                                <div class="flex justify-center">
                                    <canvas id="qualityChart" width="200" height="200"></canvas>
                                </div>
                                <div class="text-center pt-4 border-t">
                                    <p class="text-sm text-gray-600">Estado Final</p>
                                    <p class="text-lg font-bold text-green-600">✅ Completada</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <script>
        // Debug: Verificar que Chart.js esté disponible
        console.log('🔍 Verificando dependencias:');
        console.log('Chart.js disponible:', typeof Chart !== 'undefined');
        console.log('Alpine.js disponible:', typeof Alpine !== 'undefined');
        
        // Debug: Mostrar datos del shift
        console.log('📊 Datos del shift:', {
            id: {{ $shift->id }},
            status: '{{ $shift->status }}',
            plan_id: {{ $shift->plan_id ?? 'null' }},
            actualProduction: {{ $shift->actual_production }},
            targetQuantity: {{ $shift->target_snapshot['target_quantity'] ?? 0 }},
            target_snapshot: @json($shift->target_snapshot)
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('shiftMonitor', () => ({
                // State
                actualProduction: {{ $shift->actual_production }},
                goodUnits: {{ $shift->good_units }},
                defectiveUnits: {{ $shift->defective_units }},
                targetQuantity: {{ $shift->target_snapshot['target_quantity'] ?? 0 }},
                startTime: new Date('{{ $shift->start_time->toIso8601String() }}'),
                shiftType: '{{ $shift->shift_type }}',
                
                // Form
                form: {
                    quantity: 0,
                    good_units: 0,
                    defective_units: 0
                },
                submitting: false,
                
                // Alert
                alert: {
                    show: false,
                    type: 'success',
                    message: ''
                },

                // Charts
                productionChart: null,
                qualityChart: null,

                // Computed
                get progress() {
                    return this.targetQuantity > 0 
                        ? Math.min(100, (this.actualProduction / this.targetQuantity) * 100) 
                        : 0;
                },

                get qualityRate() {
                    return this.actualProduction > 0 
                        ? (this.goodUnits / this.actualProduction) * 100 
                        : 100;
                },

                get durationFormatted() {
                    const now = new Date();
                    const diff = Math.floor((now - this.startTime) / 1000 / 60); // minutes
                    const hours = Math.floor(diff / 60);
                    const minutes = diff % 60;
                    return `${hours}h ${minutes}m`;
                },

                get shiftTimeRange() {
                    const times = {
                        morning: '6:00 - 14:00',
                        afternoon: '14:00 - 22:00',
                        night: '22:00 - 6:00'
                    };
                    return times[this.shiftType] || '';
                },

                get isFormValid() {
                    return this.form.quantity > 0 && 
                           this.form.quantity === (this.form.good_units + this.form.defective_units);
                },

                // Methods
                init() {
                    this.initCharts();
                    this.startDurationUpdate();
                    @if($shift->status == 'active')
                        this.listenForUpdates();
                    @endif
                },

                initCharts() {
                    console.log('📈 Inicializando gráficos...');
                    console.log('Valores actuales:', {
                        actualProduction: this.actualProduction,
                        targetQuantity: this.targetQuantity,
                        goodUnits: this.goodUnits,
                        defectiveUnits: this.defectiveUnits
                    });

                    // Production Progress Chart
                    const ctxProd = document.getElementById('productionChart');
                    
                    if (!ctxProd) {
                        console.error('❌ Canvas productionChart no encontrado');
                        return;
                    }
                    
                    if (typeof Chart === 'undefined') {
                        console.error('❌ Chart.js no está disponible');
                        return;
                    }
                    
                    console.log('✅ Canvas encontrado, creando gráfico...');
                    
                    this.productionChart = new Chart(ctxProd, {
                        type: 'bar',
                        data: {
                            labels: ['Producido', 'Pendiente', 'Buenas', 'Defectuosas'],
                            datasets: [{
                                data: [
                                    this.actualProduction,
                                    Math.max(0, this.targetQuantity - this.actualProduction),
                                    this.goodUnits,
                                    this.defectiveUnits
                                ],
                                backgroundColor: ['#3b82f6', '#e5e7eb', '#10b981', '#ef4444']
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });

                    // Quality Chart (only for completed shifts)
                    @if($shift->status != 'active')
                        const ctxQuality = document.getElementById('qualityChart');
                        this.qualityChart = new Chart(ctxQuality, {
                            type: 'doughnut',
                            data: {
                                labels: ['Buenas', 'Defectuosas'],
                                datasets: [{
                                    data: [this.goodUnits, this.defectiveUnits],
                                    backgroundColor: ['#10b981', '#ef4444']
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                cutout: '70%'
                            }
                        });
                    @endif
                },

                async recordProduction() {
                    if (!this.isFormValid || this.submitting) return;

                    this.submitting = true;

                    try {
                        const response = await fetch('{{ route('work-shifts.record-production', $shift) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update state
                            this.actualProduction = data.data.actual_production;
                            this.goodUnits = data.data.good_units;
                            this.defectiveUnits = data.data.defective_units;

                            // Update chart
                            this.productionChart.data.datasets[0].data = [
                                this.actualProduction,
                                Math.max(0, this.targetQuantity - this.actualProduction),
                                this.goodUnits,
                                this.defectiveUnits
                            ];
                            this.productionChart.update();

                            // Reset form
                            this.form = { quantity: 0, good_units: 0, defective_units: 0 };

                            // Show success alert
                            this.showAlert('success', data.message);
                        } else {
                            this.showAlert('error', data.message || 'Error al registrar producción');
                        }
                    } catch (error) {
                        this.showAlert('error', 'Error de conexión');
                    } finally {
                        this.submitting = false;
                    }
                },

                showAlert(type, message) {
                    this.alert = { show: true, type, message };
                    setTimeout(() => {
                        this.alert.show = false;
                    }, 5000);
                },

                startDurationUpdate() {
                    setInterval(() => {
                        // Force update duration display
                        this.$nextTick();
                    }, 60000); // Update every minute
                },

                listenForUpdates() {
                    // Echo listener for real-time updates
                    // window.Echo.channel('work-shift.{{ $shift->id }}')
                    //     .listen('ProductionRecorded', (e) => {
                    //         this.actualProduction = e.actual_production;
                    //         this.goodUnits = e.good_units;
                    //         this.defectiveUnits = e.defective_units;
                    //         this.updateChart();
                    //     });
                }
            }));
        });
    </script>
</body>
</html>
