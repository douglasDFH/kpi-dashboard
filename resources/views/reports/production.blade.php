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

<!-- Plan Statistics -->
@if(isset($planStats))
<div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
        <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Estadísticas de Planes de Producción (Período)
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4">
            <p class="text-white text-opacity-80 text-sm">Total Planes</p>
            <p class="text-3xl font-bold text-white">{{ $planStats['total'] }}</p>
        </div>
        <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4">
            <p class="text-white text-opacity-80 text-sm">Completados</p>
            <p class="text-3xl font-bold text-green-200">{{ $planStats['completed'] }}</p>
        </div>
        <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4">
            <p class="text-white text-opacity-80 text-sm">Activos</p>
            <p class="text-3xl font-bold text-yellow-200">{{ $planStats['active'] }}</p>
        </div>
        <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4">
            <p class="text-white text-opacity-80 text-sm">Cancelados</p>
            <p class="text-3xl font-bold text-red-200">{{ $planStats['cancelled'] }}</p>
        </div>
        <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4">
            <p class="text-white text-opacity-80 text-sm">Meta Total</p>
            <p class="text-3xl font-bold text-white">{{ number_format($planStats['target_total']) }}</p>
            <p class="text-xs text-white text-opacity-70">unidades</p>
        </div>
    </div>
    
    <!-- Plan Details -->
    @if($plans->isNotEmpty())
    <div class="mt-6 bg-white rounded-lg p-4 max-h-64 overflow-y-auto">
        <h4 class="text-sm font-semibold text-gray-800 mb-3">Planes en el Período</h4>
        <div class="space-y-2">
            @foreach($plans as $plan)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div class="flex-1">
                    <a href="{{ route('production-plans.show', $plan) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                        {{ $plan->product_name }}
                    </a>
                    <p class="text-xs text-gray-500">{{ $plan->equipment->name }} • {{ ucfirst($plan->shift) }}</p>
                </div>
                <div class="text-right mr-4">
                    <p class="text-sm font-semibold text-gray-700">{{ number_format($plan->target_quantity) }}</p>
                    <p class="text-xs text-gray-500">unidades</p>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-gray-100 text-gray-800',
                            'active' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusLabels = [
                            'pending' => 'Pendiente',
                            'active' => 'Activo',
                            'completed' => 'Completado',
                            'cancelled' => 'Cancelado',
                        ];
                    @endphp
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$plan->status] }}">
                        {{ $statusLabels[$plan->status] }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif

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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan/Jornada</th>
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($prod->plan)
                            <a href="{{ route('production-plans.show', $prod->plan) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                <div class="font-medium">📋 {{ Str::limit($prod->plan->product_name, 20) }}</div>
                                <div class="text-xs text-gray-500">Plan #{{ $prod->plan->id }}</div>
                            </a>
                        @elseif($prod->workShift)
                            <a href="{{ route('work-shifts.show', $prod->workShift) }}" class="text-purple-600 hover:text-purple-800 hover:underline">
                                <div class="font-medium">⏱️ Jornada #{{ $prod->workShift->id }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($prod->workShift->shift_type) }}</div>
                            </a>
                        @else
                            <span class="text-gray-400 text-xs">Manual</span>
                        @endif
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
                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
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
