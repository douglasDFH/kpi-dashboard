<x-layouts.app title="Detalle Máquina">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $maquina->nombre }}</h1>
        <p class="text-gray-600 mt-2">{{ $maquina->codigo }} - {{ $maquina->area->nombre ?? 'N/A' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Fabricante</div>
            <div class="text-lg font-bold text-gray-900">{{ $maquina->fabricante ?? '-' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Modelo</div>
            <div class="text-lg font-bold text-gray-900">{{ $maquina->modelo ?? '-' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Jornadas</div>
            <div class="text-lg font-bold text-gray-900">{{ $maquina->jornadasProduccion->count() }}</div>
        </div>
    </div>

    @if($maquina->descripcion)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Descripción</h3>
            <p class="text-gray-700">{{ $maquina->descripcion }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mantenimiento Reciente</h3>
        @if($maquina->registrosMantenimiento->count())
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($maquina->registrosMantenimiento->take(10) as $registro)
                    <div class="flex justify-between items-center pb-2 border-b">
                        <div>
                            <div class="font-semibold text-gray-900">{{ ucfirst($registro->tipo) }}</div>
                            <div class="text-sm text-gray-600">{{ $registro->fecha->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $registro->supervisor->name ?? 'N/A' }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">Sin registros de mantenimiento</p>
        @endif
    </div>

    <div class="flex space-x-4">
        <a href="{{ route('admin.maquinas.edit', $maquina) }}" class="btn-secondary">Editar</a>
        <form action="{{ route('admin.maquinas.destroy', $maquina) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('¿Eliminar esta máquina?')" class="btn-danger">Eliminar</button>
        </form>
        <a href="{{ route('admin.maquinas.index') }}" class="btn-secondary">Volver</a>
    </div>
</div>
</x-layouts.app>
