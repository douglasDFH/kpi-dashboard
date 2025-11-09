<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Usuario - KPI Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Detalle de Usuario</h1>
                        <p class="text-gray-600">Información completa del usuario</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar
                        </a>
                        <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <div class="max-w-5xl mx-auto">
                <!-- User Info Card -->
                <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                    <div class="flex items-start space-x-6">
                        <div class="flex-shrink-0">
                            <div class="h-24 w-24 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-bold text-3xl">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $user->name }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="text-gray-900">{{ $user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Rol</p>
                                    @if($user->role)
                                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                                            @if($user->role->name === 'superadmin') bg-red-100 text-red-800
                                            @elseif($user->role->name === 'admin') bg-orange-100 text-orange-800
                                            @elseif($user->role->name === 'gerente') bg-blue-100 text-blue-800
                                            @elseif($user->role->name === 'supervisor') bg-green-100 text-green-800
                                            @elseif($user->role->name === 'operador') bg-purple-100 text-purple-800
                                            @elseif($user->role->name === 'calidad') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $user->role->display_name }}
                                        </span>
                                    @endif
                                </div>
                                @if($user->phone)
                                    <div>
                                        <p class="text-sm text-gray-500">Teléfono</p>
                                        <p class="text-gray-900">{{ $user->phone }}</p>
                                    </div>
                                @endif
                                @if($user->position)
                                    <div>
                                        <p class="text-sm text-gray-500">Cargo</p>
                                        <p class="text-gray-900">{{ $user->position }}</p>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm text-gray-500">Estado</p>
                                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Último Login</p>
                                    <p class="text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Registrado</p>
                                    <p class="text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Última Actualización</p>
                                    <p class="text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Card -->
                @if($user->role && $user->role->permissions->isNotEmpty())
                    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Permisos del Rol</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($user->role->permissions->groupBy('module') as $module => $permissions)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-700 mb-2 capitalize">{{ ucfirst($module) }}</h4>
                                    <ul class="space-y-1">
                                        @foreach($permissions as $permission)
                                            <li class="flex items-center text-sm text-gray-600">
                                                <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                {{ $permission->action }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Audit Log -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Historial de Actividad</h3>
                    @if($auditLogs->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($auditLogs as $log)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $log->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($log->action === 'created') bg-green-100 text-green-800
                                                    @elseif($log->action === 'updated') bg-blue-100 text-blue-800
                                                    @elseif($log->action === 'deleted') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $log->action }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $log->description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $log->ip_address }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($auditLogs->hasPages())
                            <div class="mt-4">
                                {{ $auditLogs->links() }}
                            </div>
                        @endif
                    @else
                        <p class="text-gray-500 text-center py-8">No hay actividad registrada para este usuario</p>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>
