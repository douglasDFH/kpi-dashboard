@extends('layouts.report')

@section('title', 'Reporte Comparativo')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Reporte Comparativo entre Equipos</h2>
    <p class="text-gray-600">Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" class="flex items-end gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg">Filtrar</button>
    </form>
</div>

<!-- OEE Comparison Chart -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Comparación de OEE por Equipo</h3>
    <canvas id="oeeComparisonChart" height="80"></canvas>
</div>

<!-- Components Comparison Chart -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Comparación de Componentes del OEE</h3>
    <canvas id="componentsChart" height="80"></canvas>
</div>

<!-- Ranking Table -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- OEE Ranking -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Ranking por OEE</h3>
        <div class="space-y-3">
            @php
                $sortedByOee = collect($comparativeData)->sortByDesc(function($data) {
                    return $data['kpis']['oee'];
                })->values();
            @endphp
            @foreach($sortedByOee as $index => $data)
            <div class="flex items-center p-3 border border-gray-200 rounded-lg">
                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full {{ $index < 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }} font-bold text-sm">
                    {{ $index + 1 }}
                </div>
                <div class="ml-4 flex-1">
                    <div class="text-sm font-medium text-gray-900">{{ $data['equipment']->name }}</div>
                    <div class="text-xs text-gray-500">{{ $data['equipment']->code }}</div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-blue-600">{{ number_format($data['kpis']['oee'], 1) }}%</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Production Ranking -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Ranking por Producción</h3>
        <div class="space-y-3">
            @php
                $sortedByProduction = collect($comparativeData)->sortByDesc(function($data) {
                    return $data['metrics']['total_production'];
                })->values();
            @endphp
            @foreach($sortedByProduction as $index => $data)
            <div class="flex items-center p-3 border border-gray-200 rounded-lg">
                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full {{ $index < 3 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} font-bold text-sm">
                    {{ $index + 1 }}
                </div>
                <div class="ml-4 flex-1">
                    <div class="text-sm font-medium text-gray-900">{{ $data['equipment']->name }}</div>
                    <div class="text-xs text-gray-500">{{ $data['equipment']->code }}</div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-green-600">{{ number_format($data['metrics']['total_production']) }}</div>
                    <div class="text-xs text-gray-500">unidades</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Detailed Comparison Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Comparación Detallada</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">OEE</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disponibilidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rendimiento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Calidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Defectos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Downtime</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($comparativeData as $data)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $data['equipment']->name }}</div>
                        <div class="text-xs text-gray-500">{{ $data['equipment']->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $oee = $data['kpis']['oee'];
                            $oeeColor = $oee >= 85 ? 'text-green-600' : ($oee >= 65 ? 'text-yellow-600' : 'text-red-600');
                        @endphp
                        <span class="text-sm font-bold {{ $oeeColor }}">{{ number_format($oee, 1) }}%</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                        {{ number_format($data['kpis']['availability'], 1) }}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600">
                        {{ number_format($data['kpis']['performance'], 1) }}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-600">
                        {{ number_format($data['kpis']['quality'], 1) }}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($data['metrics']['total_production']) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                        {{ number_format($data['metrics']['defective_units']) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ number_format($data['metrics']['total_downtime_minutes']) }} min</div>
                        <div class="text-xs text-gray-500">{{ number_format($data['metrics']['total_downtime_minutes'] / 60, 1) }}h</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Insights -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    @php
        $bestOee = collect($comparativeData)->sortByDesc(function($d) { return $d['kpis']['oee']; })->first();
        $worstOee = collect($comparativeData)->sortBy(function($d) { return $d['kpis']['oee']; })->first();
        $mostProductive = collect($comparativeData)->sortByDesc(function($d) { return $d['metrics']['total_production']; })->first();
    @endphp

    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h4 class="text-sm font-medium text-green-900 mb-2">🏆 Mejor OEE</h4>
        <p class="text-lg font-bold text-green-700">{{ $bestOee['equipment']->name }}</p>
        <p class="text-2xl font-bold text-green-600">{{ number_format($bestOee['kpis']['oee'], 1) }}%</p>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h4 class="text-sm font-medium text-blue-900 mb-2">📊 Más Productivo</h4>
        <p class="text-lg font-bold text-blue-700">{{ $mostProductive['equipment']->name }}</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($mostProductive['metrics']['total_production']) }} unidades</p>
    </div>

    <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
        <h4 class="text-sm font-medium text-orange-900 mb-2">⚠️ Necesita Atención</h4>
        <p class="text-lg font-bold text-orange-700">{{ $worstOee['equipment']->name }}</p>
        <p class="text-2xl font-bold text-orange-600">{{ number_format($worstOee['kpis']['oee'], 1) }}%</p>
    </div>
</div>

<script>
    // OEE Comparison Chart
    const oeeCtx = document.getElementById('oeeComparisonChart').getContext('2d');
    const equipmentNames = @json(collect($comparativeData)->pluck('equipment.name'));
    const oeeValues = @json(collect($comparativeData)->pluck('kpis.oee'));

    new Chart(oeeCtx, {
        type: 'bar',
        data: {
            labels: equipmentNames,
            datasets: [{
                label: 'OEE (%)',
                data: oeeValues,
                backgroundColor: oeeValues.map(v =>
                    v >= 85 ? 'rgba(34, 197, 94, 0.7)' :
                    v >= 65 ? 'rgba(251, 191, 36, 0.7)' :
                    'rgba(239, 68, 68, 0.7)'
                ),
                borderColor: oeeValues.map(v =>
                    v >= 85 ? 'rgb(34, 197, 94)' :
                    v >= 65 ? 'rgb(251, 191, 36)' :
                    'rgb(239, 68, 68)'
                ),
                borderWidth: 2
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

    // Components Comparison Chart
    const compCtx = document.getElementById('componentsChart').getContext('2d');
    const availabilityValues = @json(collect($comparativeData)->pluck('kpis.availability'));
    const performanceValues = @json(collect($comparativeData)->pluck('kpis.performance'));
    const qualityValues = @json(collect($comparativeData)->pluck('kpis.quality'));

    new Chart(compCtx, {
        type: 'radar',
        data: {
            labels: equipmentNames,
            datasets: [{
                label: 'Disponibilidad',
                data: availabilityValues,
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 2
            }, {
                label: 'Rendimiento',
                data: performanceValues,
                backgroundColor: 'rgba(251, 146, 60, 0.2)',
                borderColor: 'rgb(251, 146, 60)',
                borderWidth: 2
            }, {
                label: 'Calidad',
                data: qualityValues,
                backgroundColor: 'rgba(168, 85, 247, 0.2)',
                borderColor: 'rgb(168, 85, 247)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
</script>
@endsection
