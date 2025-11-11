<x-layouts.app title="Planes de Producción">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Planes de Producción</h1>
            <p class="text-gray-600 mt-2">Gestiona los planes de cada máquina</p>
        </div>
        <a href="{{ route('admin.planes.create') }}" class="btn-primary">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Plan
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Máquina</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Ciclo (seg)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Objetivo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($planes as $plan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $plan->nombre }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $plan->maquina->nombre }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $plan->tiempo_ciclo_ideal_segundos }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $plan->objetivo_produccion_diaria }} u/día</td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.planes.show', $plan) }}" class="text-blue-600">Ver</a>
                            <a href="{{ route('admin.planes.edit', $plan) }}" class="text-gray-600">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Sin planes</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $planes->links() }}</div>
</div>
</x-layouts.app>
