@extends('layouts.report')

@section('title', 'Reporte de Producción')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Reporte de Producción</h2>
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
            <button type="submit" class="w-full px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">Filtrar</button>
        </div>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Producción Planificada</h3>
        <div class="text-3xl font-bold text-blue-600">{{ number_format($totals['planned']) }}</div>
        <p class="text-xs text-gray-500 mt-1">unidades</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Producción Real</h3>
        <div class="text-3xl font-bold text-green-600">{{ number_format($totals['actual']) }}</div>
        <p class="text-xs text-gray-500 mt-1">unidades</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Unidades Buenas</h3>
        <div class="text-3xl font-bold text-purple-600">{{ number_format($totals['good']) }}</div>
        <p class="text-xs text-gray-500 mt-1">unidades</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Eficiencia General</h3>
        <div class="text-3xl font-bold text-orange-600">{{ number_format($totals['efficiency'], 1) }}%</div>
        <p class="text-xs text-gray-500 mt-1">real vs planificado</p>
    </div>
</div>

<!-- Production Chart -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Gráfico de Producción</h3>
    <canvas id="productionChart" height="80"></canvas>
</div>

<!-- Detailed Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Detalle de Producción</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Planificada</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Real</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buenas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Defectuosas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Eficiencia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tasa Calidad</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($productionData as $prod)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $prod->production_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $prod->equipment->name }}</div>
                        <div class="text-xs text-gray-500">{{ $prod->equipment->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($prod->planned_production) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">{{ number_format($prod->actual_production) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-600">{{ number_format($prod->good_units) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ number_format($prod->defective_units) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $efficiency = $prod->planned_production > 0 ? ($prod->actual_production / $prod->planned_production * 100) : 0;
                            $colorClass = $efficiency >= 90 ? 'text-green-600' : ($efficiency >= 75 ? 'text-yellow-600' : 'text-red-600');
                        @endphp
                        <span class="text-sm font-semibold {{ $colorClass }}">{{ number_format($efficiency, 1) }}%</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $qualityRate = $prod->actual_production > 0 ? ($prod->good_units / $prod->actual_production * 100) : 0;
                            $qColorClass = $qualityRate >= 95 ? 'text-green-600' : ($qualityRate >= 90 ? 'text-yellow-600' : 'text-red-600');
                        @endphp
                        <span class="text-sm font-semibold {{ $qColorClass }}">{{ number_format($qualityRate, 1) }}%</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        No hay datos de producción en el período seleccionado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    const ctx = document.getElementById('productionChart').getContext('2d');
    
    const productionData = {!! json_encode($chartData) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: productionData.map(d => d.date + ' - ' + d.equipment),
            datasets: [{
                label: 'Planificada',
                data: productionData.map(d => d.planned),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }, {
                label: 'Real',
                data: productionData.map(d => d.actual),
                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }, {
                label: 'Buenas',
                data: productionData.map(d => d.good),
                backgroundColor: 'rgba(168, 85, 247, 0.7)',
                borderColor: 'rgb(168, 85, 247)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
</script>
@endsection
