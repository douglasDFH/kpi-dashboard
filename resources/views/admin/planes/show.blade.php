<x-layouts.app title="Detalle Plan de Producción">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $plan->nombre }}</h1>
        <p class="text-gray-600 mt-2">Máquina: {{ $plan->maquina->nombre ?? 'N/A' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Tiempo Ciclo Ideal</div>
            <div class="text-lg font-bold text-gray-900">{{ $plan->ideal_cycle_time_seconds ?? '-' }} seg</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Objetivo Diario</div>
            <div class="text-lg font-bold text-gray-900">{{ $plan->objetivo_unidades ?? '-' }} unidades</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Estado</div>
            <div class="text-lg font-bold {{ $plan->activo ? 'text-green-600' : 'text-red-600' }}">
                {{ $plan->activo ? 'Activo' : 'Inactivo' }}
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jornadas Asociadas</h3>
        @if($plan->jornadasProduccion->count())
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($plan->jornadasProduccion->take(10) as $jornada)
                    <div class="flex justify-between items-center pb-2 border-b">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $jornada->maquina->nombre ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-600">{{ $jornada->fecha_inicio->format('d/m/Y H:i') ?? 'N/A' }}</div>
                        </div>
                        <div class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $jornada->status ?? 'N/A' }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">Sin jornadas asociadas</p>
        @endif
    </div>

    <div class="flex space-x-4">
        <a href="{{ route('admin.planes.edit', $plan) }}" class="btn-secondary">Editar</a>
        <form action="{{ route('admin.planes.destroy', $plan) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('¿Eliminar este plan?')" class="btn-danger">Eliminar</button>
        </form>
        <a href="{{ route('admin.planes.index') }}" class="btn-secondary">Volver</a>
    </div>
</div>
</x-layouts.app>