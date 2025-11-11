<x-layouts.app title="Editar Máquina">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Máquina</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.maquinas.update', $maquina) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                    <input type="text" name="nombre" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ $maquina->nombre }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código *</label>
                    <input type="text" name="codigo" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ $maquina->codigo }}">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Área *</label>
                <select name="area_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" @selected($maquina->area_id === $area->id)>{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fabricante</label>
                    <input type="text" name="fabricante" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ $maquina->fabricante ?? '' }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modelo</label>
                    <input type="text" name="modelo" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ $maquina->modelo ?? '' }}">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ $maquina->descripcion ?? '' }}</textarea>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">Guardar Cambios</button>
                <a href="{{ route('admin.maquinas.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>
