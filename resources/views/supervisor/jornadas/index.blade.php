<x-layouts.app title="Jornadas de Producción">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Jornadas de Producción</h1>
            <p class="text-gray-600 mt-2">Gestión de jornadas de producción por máquina</p>
        </div>
        <a href="{{ route('supervisor.jornadas.create') }}" class="btn-primary">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nueva Jornada
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Fecha Inicio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Producción</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($jornadas as $jornada)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $jornada->maquina->nombre }}</div>
                            <div class="text-sm text-gray-500">{{ $jornada->maquina->codigo }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $jornada->inicio_real->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $jornada->status === 'running' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $jornada->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $jornada->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $jornada->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                            ">
                                {{ ucfirst($jornada->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $jornada->cantidad_producida ?? 0 }} unidades
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                            <a href="{{ route('supervisor.jornadas.show', $jornada) }}" class="text-blue-600 hover:text-blue-900">
                                Ver
                            </a>
                            @if($jornada->status === 'running')
                                <form action="{{ route('supervisor.jornadas.pausar', $jornada) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900">Pausar</button>
                                </form>
                            @endif
                            @if($jornada->status === 'paused')
                                <form action="{{ route('supervisor.jornadas.reanudar', $jornada) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900">Reanudar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No hay jornadas registradas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $jornadas->links() }}
    </div>
</div>
</x-layouts.app>
