<x-layouts.app title="Nuevo Plan">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Crear Plan de Producción</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.planes.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Máquina *</label>
                <select name="maquina_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">-- Selecciona máquina --</option>
                    @foreach($maquinas as $m)
                        <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Plan *</label>
                <input type="text" name="nombre" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ old('nombre') }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('descripcion') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiempo Ciclo Ideal (seg) *</label>
                    <input type="number" name="tiempo_ciclo_ideal_segundos" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required min="1" max="3600" value="{{ old('tiempo_ciclo_ideal_segundos') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Objetivo Diario *</label>
                    <input type="number" name="objetivo_produccion_diaria" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required min="1" value="{{ old('objetivo_produccion_diaria') }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio *</label>
                    <input type="date" name="fecha_inicio" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ old('fecha_inicio') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ old('fecha_fin') }}">
                </div>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">Crear Plan</button>
                <a href="{{ route('admin.planes.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>
