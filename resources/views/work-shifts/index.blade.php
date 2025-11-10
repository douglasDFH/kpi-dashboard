<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jornadas de Trabajo - KPI Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">⏱️ Jornadas de Trabajo</h1>
                        <p class="text-gray-600">Gestión y seguimiento de jornadas de producción</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('work-shifts.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Iniciar Jornada
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <!-- Alerts -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" action="{{ route('work-shifts.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Equipo</label>
                        <select name="equipment_id" class="w-full border-gray-300 rounded-lg">
                            <option value="">Todos los equipos</option>
                            @foreach($equipment as $eq)
                                <option value="{{ $eq->id }}" {{ request('equipment_id') == $eq->id ? 'selected' : '' }}>
                                    {{ $eq->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg">
                            <option value="">Todos los estados</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Turno</label>
                        <select name="shift_type" class="w-full border-gray-300 rounded-lg">
                            <option value="">Todos los turnos</option>
                            <option value="morning" {{ request('shift_type') == 'morning' ? 'selected' : '' }}>Mañana</option>
                            <option value="afternoon" {{ request('shift_type') == 'afternoon' ? 'selected' : '' }}>Tarde</option>
                            <option value="night" {{ request('shift_type') == 'night' ? 'selected' : '' }}>Noche</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="relative">
                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                @if($shifts->where('status', 'active')->count() > 0)
                                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Jornadas Activas</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $shifts->where('status', 'active')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border-l-4 border-green-500 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Completadas Hoy</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $shifts->where('status', 'completed')->where('end_time', '>=', now()->startOfDay())->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 border-l-4 border-purple-500 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Producción Total Hoy</p>
                            <p class="text-2xl font-bold text-gray-800">
                                {{ number_format($shifts->where('start_time', '>=', now()->startOfDay())->sum('actual_production')) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Shifts Alert -->
            @if($shifts->where('status', 'active')->count() > 0)
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">
                                <strong>{{ $shifts->where('status', 'active')->count() }}</strong> jornada(s) en progreso. Haz clic en "Ver" para registrar producción.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Shifts Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turno</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inicio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progreso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($shifts as $shift)
                                <tr class="hover:bg-gray-50 {{ $shift->status == 'active' ? 'bg-blue-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ $shift->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $shift->equipment->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $shift->equipment->code }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($shift->plan)
                                            <a href="{{ route('production-plans.show', $shift->plan) }}" class="text-sm text-blue-600 hover:underline">
                                                #{{ $shift->plan->id }}
                                            </a>
                                            <div class="text-sm text-gray-500">{{ $shift->plan->product_name }}</div>
                                        @else
                                            <span class="text-sm text-gray-400">Sin plan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $shift->shift_type == 'morning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $shift->shift_type == 'afternoon' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $shift->shift_type == 'night' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                            {{ $shift->shift_type == 'morning' ? '🌅 Mañana' : '' }}
                                            {{ $shift->shift_type == 'afternoon' ? '☀️ Tarde' : '' }}
                                            {{ $shift->shift_type == 'night' ? '🌙 Noche' : '' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $shift->start_time->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $shift->end_time ? $shift->end_time->format('d/m/Y H:i') : '-' }}
                                        @if($shift->status == 'active' && $shift->duration_minutes)
                                            <div class="text-xs text-gray-500">
                                                {{ floor($shift->duration_minutes / 60) }}h {{ $shift->duration_minutes % 60 }}m
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($shift->actual_production) }}</div>
                                        <div class="text-xs text-gray-500">
                                            ✅ {{ number_format($shift->good_units) }} | ❌ {{ number_format($shift->defective_units) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $shift->progress) }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ number_format($shift->progress, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold 
                                            {{ $shift->quality_rate >= 95 ? 'text-green-600' : '' }}
                                            {{ $shift->quality_rate >= 90 && $shift->quality_rate < 95 ? 'text-yellow-600' : '' }}
                                            {{ $shift->quality_rate < 90 ? 'text-red-600' : '' }}">
                                            {{ number_format($shift->quality_rate, 1) }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $shift->status == 'active' ? 'bg-blue-100 text-blue-800 animate-pulse' : '' }}
                                            {{ $shift->status == 'pending_registration' ? 'bg-yellow-100 text-yellow-800 animate-bounce' : '' }}
                                            {{ $shift->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $shift->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $shift->status == 'active' ? '▶️ En Producción' : '' }}
                                            {{ $shift->status == 'pending_registration' ? '📝 Registrar' : '' }}
                                            {{ $shift->status == 'completed' ? '✅ Completado' : '' }}
                                            {{ $shift->status == 'cancelled' ? '❌ Cancelado' : '' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('work-shifts.show', $shift) }}" class="text-blue-600 hover:text-blue-900" title="Ver">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            @if($shift->status == 'active')
                                                <form method="POST" action="{{ route('work-shifts.end', $shift) }}" class="inline" onsubmit="return confirm('¿Está seguro de finalizar esta jornada?')">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Finalizar">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                </form>

                                                @if($shift->actual_production == 0)
                                                    <form method="POST" action="{{ route('work-shifts.destroy', $shift) }}" class="inline" onsubmit="return confirm('¿Está seguro de eliminar esta jornada?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-4 text-lg">No hay jornadas de trabajo registradas</p>
                                        <p class="mt-2">Inicia una nueva jornada haciendo clic en "Iniciar Jornada"</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($shifts->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $shifts->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
