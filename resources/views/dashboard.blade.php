<x-layouts.app title="Dashboard KPI">
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard de Producción</h1>
                    <p class="mt-1 text-sm text-gray-500">Monitoreo en tiempo real de KPIs industriales</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Cerrar Sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Selector de Máquina -->
        <div class="mb-8">
            <label for="maquina-select" class="block text-sm font-medium text-gray-700 mb-2">
                Seleccionar Equipo
            </label>
            <select id="maquina-select" name="maquina_id"
                    onchange="window.location.href='?maquina_id='+this.value"
                    class="block w-full max-w-xs px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                @foreach($maquinas as $maq)
                    <option value="{{ $maq->id }}" {{ $maquinaSeleccionada && $maq->id == $maquinaSeleccionada->id ? 'selected' : '' }}>
                        {{ $maq->name ?? $maq->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        @if($maquinaSeleccionada)
        <!-- Estado de la Jornada -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Estado de la Jornada</h2>
                @if($jornadaActiva)
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Jornada Activa</p>
                            <p class="text-sm text-gray-500">Iniciada: {{ $jornadaActiva->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center">
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
        </div>

        <!-- KPIs Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- OEE -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white font-bold text-sm">OEE</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">Overall Equipment Effectiveness</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $kpis['oee'] ?? 0 }}%</dd>
                    </div>
                </div>
            </div>

            <!-- Disponibilidad -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">Disponibilidad</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $kpis['availability'] ?? 0 }}%</dd>
                    </div>
                </div>
            </div>

            <!-- Rendimiento -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">Rendimiento</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $kpis['performance'] ?? 0 }}%</dd>
                    </div>
                </div>
            </div>

            <!-- Calidad -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 truncate">Calidad</dt>
                        <dd class="text-2xl font-semibold text-gray-900">{{ $kpis['quality'] ?? 0 }}%</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Componentes del OEE -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Componentes del OEE</h3>
                <canvas id="oeeChart" width="400" height="300"></canvas>
            </div>

            <!-- Resumen de Producción -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Producción</h3>
                <canvas id="productionChart" width="400" height="300"></canvas>
            </div>
        </div>

        <!-- Indicador de actualización en tiempo real -->
        <div class="mt-8 text-center">
            <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></div>
                Actualización en tiempo real activada
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de componentes OEE
    const oeeCtx = document.getElementById('oeeChart').getContext('2d');
    new Chart(oeeCtx, {
        type: 'bar',
        data: {
            labels: ['Disponibilidad', 'Rendimiento', 'Calidad'],
            datasets: [{
                label: 'Porcentaje',
                data: [{{ $kpis['availability'] ?? 0 }}, {{ $kpis['performance'] ?? 0 }}, {{ $kpis['quality'] ?? 0 }}],
                backgroundColor: ['#10b981', '#f59e0b', '#a855f7'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
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
                data: [85, 15], // Datos de ejemplo
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
});
</script>
@endif
</x-layouts.app>