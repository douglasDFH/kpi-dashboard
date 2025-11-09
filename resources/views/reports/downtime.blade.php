@extends('layouts.report')

@section('title', 'Reporte de Tiempos Muertos')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Reporte de Tiempos Muertos (Downtime)</h2>
    <p class="text-gray-600">Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Equipo</label>
            <select name="equipment_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Todos los equipos</option>
                @foreach($equipment as $eq)
                    <option value="{{ $eq->id }}" {{ $equipmentId == $eq->id ? 'selected' : '' }}>
                        {{ $eq->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Filtrar</button>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Total Downtime</h3>
        <div class="text-3xl font-bold text-red-600">{{ number_format($totals['total_downtime']) }}</div>
        <p class="text-xs text-gray-500 mt-1">minutos</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Total en Horas</h3>
        <div class="text-3xl font-bold text-orange-600">{{ number_format($totals['total_downtime_hours'], 1) }}</div>
        <p class="text-xs text-gray-500 mt-1">horas</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Planificado</h3>
        <div class="text-3xl font-bold text-blue-600">{{ number_format($totals['planned']) }}</div>
        <p class="text-xs text-gray-500 mt-1">minutos</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">No Planificado</h3>
        <div class="text-3xl font-bold text-red-600">{{ number_format($totals['unplanned']) }}</div>
        <p class="text-xs text-gray-500 mt-1">minutos</p>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Downtime by Category Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Downtime por Categoría</h3>
        <canvas id="categoryChart"></canvas>
    </div>

    <!-- Downtime by Reason Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Principales Razones de Paro</h3>
        <canvas id="reasonChart"></canvas>
    </div>
</div>

<!-- Top Reasons Summary -->
@if($downtimeByReason->isNotEmpty())
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Top 10 Razones de Paro</h3>
    <div class="space-y-3">
        @foreach($downtimeByReason->take(10) as $reason => $minutes)
        <div class="flex items-center">
            <div class="flex-1">
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">{{ $reason }}</span>
                    <span class="text-sm font-bold text-red-600">{{ number_format($minutes) }} min ({{ number_format($minutes / 60, 1) }}h)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full" style="width: {{ ($minutes / $totals['total_downtime']) * 100 }}%"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Detailed Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Detalle de Paros</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inicio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duración</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Razón</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($downtimeData as $downtime)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $downtime->start_time->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $downtime->equipment->name }}</div>
                        <div class="text-xs text-gray-500">{{ $downtime->equipment->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-red-600">{{ number_format($downtime->duration_minutes) }} min</div>
                        <div class="text-xs text-gray-500">{{ number_format($downtime->duration_minutes / 60, 1) }} horas</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($downtime->category == 'planificado')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                Planificado
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                No Planificado
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $downtime->reason }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($downtime->end_time)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Finalizado
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                En Curso
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        No hay tiempos muertos en el período seleccionado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'pie',
        data: {
            labels: ['Planificado', 'No Planificado'],
            datasets: [{
                data: [{{ $totals['planned'] }}, {{ $totals['unplanned'] }}],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });

    // Reason Chart
    const reasonCtx = document.getElementById('reasonChart').getContext('2d');
    const reasonData = @json($downtimeByReason->take(8));

    new Chart(reasonCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(reasonData),
            datasets: [{
                label: 'Minutos',
                data: Object.values(reasonData),
                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                borderColor: 'rgb(239, 68, 68)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
