<x-layouts.app title="Nuevo Mantenimiento">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Registrar Mantenimiento</h1>
        <p class="text-gray-600 mt-2">Documenta el mantenimiento realizado a una máquina</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('supervisor.mantenimiento.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="maquina_id" class="block text-sm font-medium text-gray-700 mb-2">Máquina *</label>
                <select id="maquina_id" name="maquina_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('maquina_id') border-red-500 @enderror" required>
                    <option value="">-- Selecciona una máquina --</option>
                    @foreach($maquinas as $maquina)
                        <option value="{{ $maquina->id }}" @selected(old('maquina_id') === $maquina->id)>
                            {{ $maquina->nombre }} ({{ $maquina->codigo }})
                        </option>
                    @endforeach
                </select>
                @error('maquina_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Mantenimiento *</label>
                <select id="tipo" name="tipo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('tipo') border-red-500 @enderror" required>
                    <option value="">-- Selecciona tipo --</option>
                    <option value="preventivo" @selected(old('tipo') === 'preventivo')>Preventivo</option>
                    <option value="correctivo" @selected(old('tipo') === 'correctivo')>Correctivo</option>
                    <option value="predictivo" @selected(old('tipo') === 'predictivo')>Predictivo</option>
                </select>
                @error('tipo')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                <textarea id="descripcion" name="descripcion" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('descripcion') border-red-500 @enderror" required>{{ old('descripcion') }}</textarea>
                @error('descripcion')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="duracion_minutos" class="block text-sm font-medium text-gray-700 mb-2">Duración (minutos)</label>
                    <input type="number" id="duracion_minutos" name="duracion_minutos" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" min="1" max="1440" value="{{ old('duracion_minutos') }}">
                </div>
                <div>
                    <label for="piezas_reemplazadas" class="block text-sm font-medium text-gray-700 mb-2">Piezas Reemplazadas</label>
                    <input type="text" id="piezas_reemplazadas" name="piezas_reemplazadas" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('piezas_reemplazadas') }}">
                </div>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">Registrar Mantenimiento</button>
                <a href="{{ route('supervisor.mantenimiento.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>
