@extends('layouts.report')

@section('title', 'Reporte OEE')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Reporte de OEE (Eficiencia General de Equipos)</h2>
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
        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Filtrar</button>
    </form>
</div>

<!-- OEE Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    @foreach($oeeData as $data)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-600">{{ $data['equipment']->name }}</h3>
        <div class="mt-3">
            <div class="text-3xl font-bold text-blue-600">{{ number_format($data['kpis']['oee'], 1) }}%</div>
            <div class="text-xs text-gray-500 mt-1">OEE</div>
        </div>
        <div class="mt-4 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Disponibilidad:</span>
                <span class="font-semibold text-green-600">{{ number_format($data['kpis']['availability'], 1) }}%</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Rendimiento:</span>
                <span class="font-semibold text-orange-600">{{ number_format($data['kpis']['performance'], 1) }}%</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Calidad:</span>
                <span class="font-semibold text-purple-600">{{ number_format($data['kpis']['quality'], 1) }}%</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Detailed Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">OEE</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disponibilidad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rendimiento</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Calidad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producción</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Downtime (min)</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($oeeData as $data)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $data['equipment']->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">{{ number_format($data['kpis']['oee'], 1) }}%</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ number_format($data['kpis']['availability'], 1) }}%</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600">{{ number_format($data['kpis']['performance'], 1) }}%</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-600">{{ number_format($data['kpis']['quality'], 1) }}%</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($data['metrics']['total_production']) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ number_format($data['metrics']['total_downtime_minutes']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
