<x-layouts.app title="Nuevo Usuario">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Crear Usuario</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ old('name') }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ old('email') }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña *</label>
                <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Contraseña *</label>
                <input type="password" name="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rol *</label>
                <select name="role_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">-- Selecciona rol --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                    <input type="text" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ old('phone') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cargo</label>
                    <input type="text" name="position" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ old('position') }}">
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" class="h-4 w-4 text-blue-600 border-gray-300 rounded" {{ old('is_active') ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Usuario activo</label>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">Crear Usuario</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>