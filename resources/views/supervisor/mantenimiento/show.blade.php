<x-layouts.app title="Detalle Mantenimiento">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Registro de Mantenimiento</h1>
        <p class="text-gray-600 mt-2">{{ $registro->maquina->nombre }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información General</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-600">Máquina</label>
                    <p class="text-gray-900">{{ $registro->maquina->nombre }} ({{ $registro->maquina->codigo }})</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Tipo</label>
                    <p>
                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full
                            {{ $registro->tipo === 'preventivo' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $registro->tipo === 'correctivo' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $registro->tipo === 'predictivo' ? 'bg-purple-100 text-purple-800' : '' }}
                        ">
                            {{ ucfirst($registro->tipo) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Fecha</label>
                    <p class="text-gray-900">{{ $registro->fecha->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Supervisor</label>
                    <p class="text-gray-900">{{ $registro->supervisor->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalles del Mantenimiento</h3>
            <div class="space-y-3">
                @if($registro->duracion_minutos)
                    <div>
                        <label class="text-sm font-medium text-gray-600">Duración</label>
                        <p class="text-gray-900">{{ $registro->duracion_minutos }} minutos</p>
                    </div>
                @endif
                @if($registro->piezas_reemplazadas)
                    <div>
                        <label class="text-sm font-medium text-gray-600">Piezas Reemplazadas</label>
                        <p class="text-gray-900">{{ $registro->piezas_reemplazadas }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Descripción</h3>
        <p class="text-gray-700 whitespace-pre-wrap">{{ $registro->descripcion }}</p>
    </div>

    <div class="flex space-x-4">
        <a href="{{ route('supervisor.mantenimiento.edit', $registro) }}" class="btn-secondary">Editar</a>
        <form action="{{ route('supervisor.mantenimiento.destroy', $registro) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('¿Eliminar este registro?')" class="btn-danger">Eliminar</button>
        </form>
        <a href="{{ route('supervisor.mantenimiento.index') }}" class="btn-secondary">Volver</a>
    </div>
</div>
</x-layouts.app>
