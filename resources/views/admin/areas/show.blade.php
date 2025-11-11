<x-layouts.app title="Detalle Área"></x-layouts.app>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $area->nombre }}</h1>
        <p class="text-gray-600 mt-2">{{ $area->codigo ?? 'Sin código' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Máquinas</div>
            <div class="text-lg font-bold text-gray-900">{{ $area->maquinas->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Gerente</div>
            <div class="text-lg font-bold text-gray-900">{{ $area->gerente_responsable ?? '-' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Estado</div>
            <div class="text-lg font-bold {{ $area->deleted_at ? 'text-red-600' : 'text-green-600' }}">
                {{ $area->deleted_at ? 'Eliminada' : 'Activa' }}
            </div>
        </div>
    </div>

    @if($area->descripcion)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Descripción</h3>
            <p class="text-gray-700">{{ $area->descripcion }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Máquinas del Área</h3>
        @if($area->maquinas->count())
            <div class="space-y-2">
                @foreach($area->maquinas as $maquina)
                    <div class="flex justify-between items-center pb-2 border-b">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $maquina->nombre }}</div>
                            <div class="text-sm text-gray-600">{{ $maquina->codigo ?? 'N/A' }}</div>
                        </div>
                        <div class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $maquina->modelo ?? 'N/A' }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">Sin máquinas registradas</p>
        @endif
    </div>

    <div class="flex space-x-4">
        <a href="{{ route('admin.areas.edit', $area) }}" class="btn-secondary">Editar</a>
        @if(!$area->trashed() && $area->maquinas->count() == 0)
            <form action="{{ route('admin.areas.destroy', $area) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('¿Eliminar esta área?')" class="btn-danger">Eliminar</button>
            </form>
        @endif
        <a href="{{ route('admin.areas.index') }}" class="btn-secondary">Volver</a>
    </div>
</div>
</x-layouts.app>
