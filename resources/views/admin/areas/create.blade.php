<x-layouts.app title="Nueva Área">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Crear Nueva Área</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.areas.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                    <input type="text" name="nombre" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ old('nombre') }}">
                    @error('nombre')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código</label>
                    <input type="text" name="codigo" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ old('codigo') }}">
                    @error('codigo')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gerente Responsable</label>
                <input type="text" name="gerente_responsable" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ old('gerente_responsable') }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea name="descripcion" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('descripcion') }}</textarea>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">Crear Área</button>
                <a href="{{ route('admin.areas.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>
