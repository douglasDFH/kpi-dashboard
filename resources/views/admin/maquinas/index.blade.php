@extends('components.layouts.app')

@section('title', 'Máquinas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestión de Máquinas</h1>
            <p class="text-gray-600 mt-2">Administra el inventario de máquinas</p>
        </div>
        <a href="{{ route('admin.maquinas.create') }}" class="btn-primary">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nueva Máquina
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Área</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Modelo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($maquinas as $maquina)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $maquina->nombre }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $maquina->codigo }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $maquina->area->nombre ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $maquina->modelo ?? '-' }}</td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.maquinas.show', $maquina) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                            <a href="{{ route('admin.maquinas.edit', $maquina) }}" class="text-gray-600 hover:text-gray-900">Editar</a>
                            @if($maquina->trashed())
                                <form action="{{ route('admin.maquinas.restore', $maquina->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900">Restaurar</button>
                                </form>
                            @else
                                <form action="{{ route('admin.maquinas.destroy', $maquina) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Eliminar?')" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Sin máquinas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $maquinas->links() }}
    </div>
</div>
@endsection
