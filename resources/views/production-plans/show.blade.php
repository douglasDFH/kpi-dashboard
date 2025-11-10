<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan #{{ $plan->id }} - {{ $plan->product_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen" x-data="planDetails">
        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center space-x-4">
                            <h1 class="text-3xl font-bold text-gray-800">📋 Plan #{{ $plan->id }}</h1>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $plan->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $plan->status == 'active' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $plan->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $plan->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ $plan->status == 'pending' ? '⏳ Pendiente' : '' }}
                                {{ $plan->status == 'active' ? '▶️ Activo' : '' }}
                                {{ $plan->status == 'completed' ? '✅ Completado' : '' }}
                                {{ $plan->status == 'cancelled' ? '❌ Cancelado' : '' }}
                            </span>
                        </div>
                        <p class="text-gray-600 mt-1">{{ $plan->product_name }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('production-plans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver
                        </a>

                        @if($plan->status == 'pending')
                            <a href="{{ route('production-plans.edit', $plan) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>
                            <form method="POST" action="{{ route('production-plans.activate', $plan) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Activar
                                </button>
                            </form>
                        @endif

                        @if($plan->status == 'active')
                            <form method="POST" action="{{ route('production-plans.complete', $plan) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Completar
                                </button>
                            </form>
                        @endif

                        @if(in_array($plan->status, ['pending', 'active']))
                            <form method="POST" action="{{ route('production-plans.cancel', $plan) }}" class="inline" onsubmit="return confirm('¿Está seguro de cancelar este plan?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Cancelar Plan
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
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Plan Details & Progress -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Plan Info -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Información del Plan</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Equipo</p>
                            <p class="text-lg font-semibold">{{ $plan->equipment->name }}</p>
                            <p class="text-sm text-gray-500">{{ $plan->equipment->code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Producto</p>
                            <p class="text-lg font-semibold">{{ $plan->product_name }}</p>
                            @if($plan->product_code)
                                <p class="text-sm text-gray-500">{{ $plan->product_code }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Objetivo</p>
                            <p class="text-lg font-semibold">{{ number_format($plan->target_quantity) }} unidades</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Turno</p>
                            <p class="text-lg font-semibold">
                                {{ $plan->shift == 'morning' ? '🌅 Mañana (6:00 - 14:00)' : '' }}
                                {{ $plan->shift == 'afternoon' ? '☀️ Tarde (14:00 - 22:00)' : '' }}
                                {{ $plan->shift == 'night' ? '🌙 Noche (22:00 - 6:00)' : '' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Fecha Inicio</p>
                            <p class="text-lg font-semibold">{{ $plan->start_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Fecha Fin</p>
                            <p class="text-lg font-semibold">{{ $plan->end_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Creado por</p>
                            <p class="text-lg font-semibold">{{ $plan->creator->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Fecha Creación</p>
                            <p class="text-lg font-semibold">{{ $plan->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($plan->notes)
                        <div class="mt-4 pt-4 border-t">
                            <p class="text-sm text-gray-600 mb-2">Notas</p>
                            <p class="text-gray-800">{{ $plan->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Progress Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Progreso</h2>
                    <div class="flex justify-center mb-4">
                        <canvas id="progressChart" width="200" height="200"></canvas>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Producido</span>
                            <span class="text-lg font-bold text-gray-800" x-text="totalProduced"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Objetivo</span>
                            <span class="text-lg font-bold text-gray-800">{{ number_format($plan->target_quantity) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t">
                            <span class="text-sm text-gray-600">Progreso</span>
                            <span class="text-2xl font-bold text-blue-600" x-text="progress + '%'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Shifts -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Jornadas de Trabajo</h2>
                    @if($plan->status == 'active')
                        <a href="{{ route('work-shifts.create', ['plan_id' => $plan->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Iniciar Jornada
                        </a>
                    @endif
                </div>

                @if($plan->workShifts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Turno</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inicio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Calidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progreso</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($plan->workShifts as $shift)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ $shift->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $shift->shift_type == 'morning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $shift->shift_type == 'afternoon' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $shift->shift_type == 'night' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                                {{ $shift->shift_type == 'morning' ? '🌅 Mañana' : '' }}
                                                {{ $shift->shift_type == 'afternoon' ? '☀️ Tarde' : '' }}
                                                {{ $shift->shift_type == 'night' ? '🌙 Noche' : '' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $shift->start_time->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $shift->end_time ? $shift->end_time->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ number_format($shift->actual_production) }} unidades</div>
                                            <div class="text-xs text-gray-500">
                                                ✅ {{ number_format($shift->good_units) }} | ❌ {{ number_format($shift->defective_units) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-semibold 
                                                {{ $shift->quality_rate >= 95 ? 'text-green-600' : '' }}
                                                {{ $shift->quality_rate >= 90 && $shift->quality_rate < 95 ? 'text-yellow-600' : '' }}
                                                {{ $shift->quality_rate < 90 ? 'text-red-600' : '' }}">
                                                {{ number_format($shift->quality_rate, 1) }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $shift->progress }}%"></div>
                                                </div>
                                                <span class="text-sm font-medium">{{ number_format($shift->progress, 1) }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $shift->status == 'active' ? 'bg-blue-100 text-blue-800 animate-pulse' : '' }}
                                                {{ $shift->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $shift->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $shift->status == 'active' ? '▶️ Activo' : '' }}
                                                {{ $shift->status == 'completed' ? '✅ Completado' : '' }}
                                                {{ $shift->status == 'cancelled' ? '❌ Cancelado' : '' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('work-shifts.show', $shift) }}" class="text-blue-600 hover:text-blue-900">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-4 text-lg text-gray-500">No hay jornadas de trabajo registradas</p>
                        @if($plan->status == 'active')
                            <p class="mt-2 text-gray-500">Inicia la primera jornada para comenzar la producción</p>
                        @endif
                    </div>
                @endif
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('planDetails', () => ({
                totalProduced: {{ $plan->workShifts()->where('status', 'completed')->sum('actual_production') }},
                targetQuantity: {{ $plan->target_quantity }},
                progress: {{ number_format($plan->progress, 1) }},
                chart: null,

                init() {
                    this.initChart();
                    this.listenForUpdates();
                },

                initChart() {
                    const ctx = document.getElementById('progressChart');
                    const progressValue = parseFloat(this.progress);
                    const remaining = 100 - progressValue;

                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Completado', 'Pendiente'],
                            datasets: [{
                                data: [progressValue, remaining],
                                backgroundColor: [
                                    progressValue >= 100 ? '#10b981' : '#3b82f6',
                                    '#e5e7eb'
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.parsed.toFixed(1) + '%';
                                        }
                                    }
                                }
                            }
                        }
                    });
                },

                listenForUpdates() {
                    // Echo listener for real-time updates
                    // window.Echo.channel('production-plan.{{ $plan->id }}')
                    //     .listen('ProductionUpdated', (e) => {
                    //         this.updateProgress(e.totalProduced, e.progress);
                    //     });
                },

                updateProgress(produced, progress) {
                    this.totalProduced = produced;
                    this.progress = progress.toFixed(1);
                    
                    // Update chart
                    this.chart.data.datasets[0].data = [progress, 100 - progress];
                    this.chart.data.datasets[0].backgroundColor[0] = progress >= 100 ? '#10b981' : '#3b82f6';
                    this.chart.update();
                }
            }));
        });
    </script>
</body>
</html>
