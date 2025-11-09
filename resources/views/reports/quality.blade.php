@extends('layouts.report')

@section('title', 'Reporte de Calidad')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Reporte de Inspecciones de Calidad</h2>
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
            <button type="submit" class="w-full px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">Filtrar</button>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Total Inspeccionado</h3>
        <div class="text-3xl font-bold text-blue-600">{{ number_format($totals['total_inspected']) }}</div>
        <p class="text-xs text-gray-500 mt-1">unidades</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Aprobadas</h3>
        <div class="text-3xl font-bold text-green-600">{{ number_format($totals['approved']) }}</div>
        <p class="text-xs text-gray-500 mt-1">unidades</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Rechazadas</h3>
        <div class="text-3xl font-bold text-red-600">{{ number_format($totals['rejected']) }}</div>
        <p class="text-xs text-gray-500 mt-1">unidades</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Tasa de Calidad</h3>
        <div class="text-3xl font-bold text-purple-600">{{ number_format($totals['quality_rate'], 1) }}%</div>
        <p class="text-xs text-gray-500 mt-1">aprobación</p>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Quality Trend Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Tendencia de Calidad</h3>
        <canvas id="qualityTrendChart"></canvas>
    </div>

    <!-- Defects by Type Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Defectos por Tipo</h3>
        <canvas id="defectsChart"></canvas>
    </div>
</div>

<!-- Defects Summary -->
@if($defectsByType->isNotEmpty())
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Resumen de Defectos</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($defectsByType->take(6) as $type => $count)
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">{{ $type }}</span>
                <span class="text-lg font-bold text-red-600">{{ number_format($count) }}</span>
            </div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                <div class="bg-red-600 h-2 rounded-full" style="width: {{ ($count / $totals['rejected']) * 100 }}%"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Detailed Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Detalle de Inspecciones</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inspeccionadas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aprobadas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rechazadas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% Calidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo Defecto</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($qualityData as $quality)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $quality->inspection_date->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $quality->equipment->name }}</div>
                        <div class="text-xs text-gray-500">{{ $quality->equipment->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($quality->total_inspected) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">{{ number_format($quality->approved_units) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">{{ number_format($quality->rejected_units) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $qualityPercent = $quality->quality_percentage;
                            $colorClass = $qualityPercent >= 95 ? 'text-green-600' : ($qualityPercent >= 90 ? 'text-yellow-600' : 'text-red-600');
                        @endphp
                        <span class="text-sm font-bold {{ $colorClass }}">{{ number_format($qualityPercent, 1) }}%</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($quality->defect_type)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                {{ $quality->defect_type }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">Sin defectos</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No hay inspecciones de calidad en el período seleccionado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Quality Trend Chart
    const trendCtx = document.getElementById('qualityTrendChart').getContext('2d');
    const qualityTrendData = @json($qualityData->map(function($q) {
        return [
            'date' => $q->inspection_date->format('d/m'),
            'rate' => $q->quality_percentage
        ];
    }));

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: qualityTrendData.map(d => d.date),
            datasets: [{
                label: 'Tasa de Calidad (%)',
                data: qualityTrendData.map(d => d.rate),
                borderColor: 'rgb(168, 85, 247)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                tension: 0.4,
                fill: true
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

    // Defects by Type Chart
    const defectsCtx = document.getElementById('defectsChart').getContext('2d');
    const defectsData = @json($defectsByType);

    new Chart(defectsCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(defectsData),
            datasets: [{
                data: Object.values(defectsData),
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
</script>
@endsection
