<x-layouts.app title="Detalle Usuario">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
        <p class="text-gray-600 mt-2">{{ $user->email }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Rol</div>
            <div class="text-lg font-bold text-gray-900">{{ $user->role->display_name ?? 'Sin rol' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Estado</div>
            <div class="text-lg font-bold {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                {{ $user->is_active ? 'Activo' : 'Inactivo' }}
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Último Login</div>
            <div class="text-lg font-bold text-gray-900">
                {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Usuario</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->phone ?? 'No especificado' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Cargo</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->position ?? 'No especificado' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="flex space-x-4">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary">Editar</a>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Volver</a>
        @if(!$user->trashed())
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('¿Eliminar este usuario?')" class="btn-danger">Eliminar</button>
            </form>
        @endif
    </div>
</div>
</x-layouts.app>