<x-layouts.app title="Áreas">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestión de Áreas</h1>
            <p class="text-gray-600 mt-2">Administra las áreas de producción</p>
        </div>
        <a href="{{ route('admin.areas.create') }}" class="btn-primary">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nueva Área
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Máquinas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Gerente</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($areas as $area)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $area->nombre }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $area->codigo ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-100 text-blue-800 text-xs font-bold">
                                {{ $area->maquinas_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $area->gerente_responsable ?? '-' }}</td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.areas.show', $area) }}" class="text-blue-600">Ver</a>
                            <a href="{{ route('admin.areas.edit', $area) }}" class="text-gray-600">Editar</a>
                            @if(!$area->trashed() && $area->maquinas_count == 0)
                                <form action="{{ route('admin.areas.destroy', $area) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Eliminar?')" class="text-red-600">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Sin áreas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $areas->links() }}</div>
</div>
</x-layouts.app>
