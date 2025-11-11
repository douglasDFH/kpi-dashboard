<x-layouts.app title="Editar Plan de Producción">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Plan de Producción</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.planes.update', $plan) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Máquina *</label>
                <select name="maquina_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required disabled>
                    <option value="{{ $plan->maquina_id }}">{{ $plan->maquina->nombre }}</option>
                </select>
                <input type="hidden" name="maquina_id" value="{{ $plan->maquina_id }}">
                <p class="text-sm text-gray-500 mt-1">La máquina no puede ser cambiada</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Plan *</label>
                <input type="text" name="nombre_plan" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ $plan->nombre_plan }}">
                @error('nombre_plan')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiempo Ciclo Ideal (seg) *</label>
                    <input type="number" name="ideal_cycle_time_seconds" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required min="1" max="3600" value="{{ $plan->ideal_cycle_time_seconds }}">
                    @error('ideal_cycle_time_seconds')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Objetivo Diario *</label>
                    <input type="number" name="objetivo_unidades" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required min="1" value="{{ $plan->objetivo_unidades }}">
                    @error('objetivo_unidades')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unidad de Medida</label>
                    <input type="text" name="unidad_medida" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ $plan->unidad_medida ?? 'unidades' }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Límite Fallos Crítico</label>
                    <input type="number" name="limite_fallos_critico" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="0" value="{{ $plan->limite_fallos_critico ?? 0 }}">
                </div>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="activo" class="rounded border-gray-300" {{ $plan->activo ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Plan Activo</span>
                </label>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">Guardar Cambios</button>
                <a href="{{ route('admin.planes.show', $plan) }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>
