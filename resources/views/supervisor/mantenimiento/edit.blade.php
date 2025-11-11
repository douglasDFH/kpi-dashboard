<x-layouts.app title="Editar Mantenimiento">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Editar Registro de Mantenimiento</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('supervisor.mantenimiento.update', $registro) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Mantenimiento *</label>
                <select id="tipo" name="tipo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    <option value="preventivo" @selected($registro->tipo === 'preventivo')>Preventivo</option>
                    <option value="correctivo" @selected($registro->tipo === 'correctivo')>Correctivo</option>
                    <option value="predictivo" @selected($registro->tipo === 'predictivo')>Predictivo</option>
                </select>
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                <textarea id="descripcion" name="descripcion" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>{{ $registro->descripcion }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="duracion_minutos" class="block text-sm font-medium text-gray-700 mb-2">Duración (minutos)</label>
                    <input type="number" id="duracion_minutos" name="duracion_minutos" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="1" max="1440" value="{{ $registro->duracion_minutos }}">
                </div>
                <div>
                    <label for="piezas_reemplazadas" class="block text-sm font-medium text-gray-700 mb-2">Piezas Reemplazadas</label>
                    <input type="text" id="piezas_reemplazadas" name="piezas_reemplazadas" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ $registro->piezas_reemplazadas ?? '' }}">
                </div>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">Guardar Cambios</button>
                <a href="{{ route('supervisor.mantenimiento.show', $registro) }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>
