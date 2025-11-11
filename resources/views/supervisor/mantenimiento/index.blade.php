<x-layouts.app title="Mantenimiento">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Registros de Mantenimiento</h1>
            <p class="text-gray-600 mt-2">Historial y seguimiento de mantenimientos</p>
        </div>
        <a href="{{ route('supervisor.mantenimiento.create') }}" class="btn-primary">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Registro
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Máquina</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Supervisor</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($registros as $registro)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $registro->maquina->nombre }}</div>
                            <div class="text-sm text-gray-500">{{ $registro->maquina->codigo }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $registro->tipo === 'preventivo' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $registro->tipo === 'correctivo' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $registro->tipo === 'predictivo' ? 'bg-purple-100 text-purple-800' : '' }}
                            ">
                                {{ ucfirst($registro->tipo) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $registro->fecha->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $registro->supervisor->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                            <a href="{{ route('supervisor.mantenimiento.show', $registro) }}" class="text-blue-600 hover:text-blue-900">
                                Ver
                            </a>
                            <a href="{{ route('supervisor.mantenimiento.edit', $registro) }}" class="text-gray-600 hover:text-gray-900">
                                Editar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No hay registros de mantenimiento
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $registros->links() }}
    </div>
</div>
</x-layouts.app>
