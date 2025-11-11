<x-layouts.app title="Dashboard KPI">
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Selector de Máquina -->
        <div class="mb-8 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Seleccionar Equipo</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($maquinas as $maquina)
                    <a href="?maquina_id={{ $maquina->id }}"
                       class="px-4 py-3 rounded-lg transition {{ $maquinaSeleccionada && $maquina->id == $maquinaSeleccionada->id ? 'bg-blue-600 text-white ring-4 ring-blue-300' : 'bg-blue-500 text-white hover:bg-blue-600' }}">
                        <div class="text-sm font-medium">{{ $maquina->nombre }}</div>
                        <div class="text-xs opacity-75">{{ $maquina->codigo ?? 'N/A' }}</div>
                    </a>
                @empty
                    <div class="col-span-4 text-center py-4 text-gray-500">
                        No hay máquinas disponibles
                    </div>
                @endforelse
            </div>
        </div>

        @if($maquinaSeleccionada)
            <!-- Estado de la Jornada -->
            <div class="mb-8 bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Estado de la Jornada</h2>
                @if($jornadaActiva)
                    <div class="flex items-center p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-900">Jornada Activa</p>
                            <p class="text-sm text-green-700">Iniciada: {{ $jornadaActiva->fecha_inicio->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Sin Jornada Activa</p>
                            <p class="text-sm text-gray-500">No hay jornada en curso para este equipo</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- KPIs Principales (Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- OEE -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">OEE</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($kpis['oee'] ?? 0, 1) }}%</p>
                        </div>
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Overall Equipment Effectiveness</p>
                </div>

                <!-- Disponibilidad -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Disponibilidad</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($kpis['availability'] ?? 0, 1) }}%</p>
                        </div>
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100">
                            <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Tiempo activo vs planificado</p>
                </div>

                <!-- Rendimiento -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Rendimiento</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($kpis['performance'] ?? 0, 1) }}%</p>
                        </div>
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-100">
                            <svg class="h-6 w-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Producción real vs planificada</p>
                </div>

                <!-- Calidad -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Calidad</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($kpis['quality'] ?? 0, 1) }}%</p>
                        </div>
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-100">
                            <svg class="h-6 w-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Unidades buenas vs total</p>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Componentes del OEE -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Componentes del OEE</h3>
                    <canvas id="oeeChart" width="400" height="300"></canvas>
                </div>

                <!-- Resumen de Producción -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Producción vs Defectos</h3>
                    <canvas id="productionChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- Métricas Adicionales -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Métricas Adicionales</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Producción Total</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $metricas['total_produccion'] ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Defectuosas</p>
                            <p class="mt-1 text-2xl font-bold text-red-600">{{ $metricas['defectuosas'] ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Downtime (min)</p>
                            <p class="mt-1 text-2xl font-bold text-orange-600">{{ $metricas['downtime'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indicador de actualización en tiempo real -->
            <div class="flex justify-center mb-8">
                <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 text-sm font-medium rounded-full border border-green-300">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></div>
                    Actualización en tiempo real activada
                </div>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                <p class="text-blue-900">Selecciona una máquina para ver su dashboard de KPIs</p>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($maquinaSeleccionada)
        // Gráfico de componentes OEE
        const oeeCtx = document.getElementById('oeeChart').getContext('2d');
        new Chart(oeeCtx, {
            type: 'bar',
            data: {
                labels: ['Disponibilidad', 'Rendimiento', 'Calidad'],
                datasets: [{
                    label: 'Porcentaje (%)',
                    data: [{{ $kpis['availability'] ?? 0 }}, {{ $kpis['performance'] ?? 0 }}, {{ $kpis['quality'] ?? 0 }}],
                    backgroundColor: ['#10b981', '#f59e0b', '#a855f7'],
                    borderColor: ['#059669', '#d97706', '#9333ea'],
                    borderWidth: 2,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Gráfico de resumen de producción
        const productionCtx = document.getElementById('productionChart').getContext('2d');
        new Chart(productionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Unidades Buenas', 'Unidades Defectuosas'],
                datasets: [{
                    data: [{{ ($metricas['total_produccion'] ?? 0) - ($metricas['defectuosas'] ?? 0) }}, {{ $metricas['defectuosas'] ?? 0 }}],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderColor: ['#059669', '#dc2626'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });
    @endif
});
</script>
</x-layouts.app>