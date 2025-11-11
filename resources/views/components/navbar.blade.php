@php
    $user = Auth::user();
    $isAdmin = $user->hasRole('admin') || $user->hasRole('superadmin');
    $isSupervisor = $user->hasRole('supervisor');
@endphp

<nav class="bg-gradient-to-r from-slate-800 to-slate-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 hover:text-blue-400 transition">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.5 1.5H3a1.5 1.5 0 00-1.5 1.5v12a1.5 1.5 0 001.5 1.5h14a1.5 1.5 0 001.5-1.5V8.5"/>
                        <path stroke="currentColor" stroke-width="2" d="M10 6v8M6 10h8"/>
                    </svg>
                    <span class="text-xl font-bold">KPI Dashboard</span>
                </a>
            </div>

            <!-- Menu Principal -->
            <div class="hidden md:flex items-center space-x-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium hover:bg-slate-700 transition {{ request()->routeIs('dashboard') ? 'bg-blue-600' : '' }}">
                    <span class="flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"/>
                            <path d="M3 10a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"/>
                        </svg>
                        <span>Dashboard</span>
                    </span>
                </a>

                @if($isSupervisor)
                    <!-- Jornadas (Supervisor) -->
                    <div class="relative group">
                        <button class="px-3 py-2 rounded-md text-sm font-medium hover:bg-slate-700 transition flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                            </svg>
                            <span>Operaciones</span>
                        </button>
                        <div class="absolute left-0 mt-2 w-48 bg-slate-700 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <a href="{{ route('supervisor.jornadas.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-600 first:rounded-t-md">
                                📊 Jornadas
                            </a>
                            <a href="{{ route('supervisor.mantenimiento.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-600 last:rounded-b-md">
                                🔧 Mantenimiento
                            </a>
                        </div>
                    </div>
                @endif

                @if($isAdmin)
                    <!-- Admin (Admin) -->
                    <div class="relative group">
                        <button class="px-3 py-2 rounded-md text-sm font-medium hover:bg-slate-700 transition flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 17v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381zM14.6 7H12V4.6L9.4 10H12v2.4l2.6-5.4z"/>
                            </svg>
                            <span>Administración</span>
                        </button>
                        <div class="absolute left-0 mt-2 w-48 bg-slate-700 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <a href="{{ route('admin.maquinas.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-600 first:rounded-t-md">
                                ⚙️ Máquinas
                            </a>
                            <a href="{{ route('admin.planes.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-600">
                                📋 Planes Producción
                            </a>
                            <a href="{{ route('admin.areas.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-600 last:rounded-b-md">
                                🏭 Áreas
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Usuario + Logout -->
            <div class="flex items-center space-x-4">
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400">
                        @if(Auth::user()->role)
                            {{ Auth::user()->role->display_name }}
                        @else
                            Usuario
                        @endif
                    </p>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded-md text-sm font-medium bg-red-600 hover:bg-red-700 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile Menu (Burger) -->
    <div class="md:hidden px-2 pt-2 pb-3 space-y-1">
        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-slate-700">Dashboard</a>
        
        @if($isSupervisor)
            <a href="{{ route('supervisor.jornadas.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-slate-700">📊 Jornadas</a>
            <a href="{{ route('supervisor.mantenimiento.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-slate-700">🔧 Mantenimiento</a>
        @endif

        @if($isAdmin)
            <a href="{{ route('admin.maquinas.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-slate-700">⚙️ Máquinas</a>
            <a href="{{ route('admin.planes.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-slate-700">📋 Planes</a>
            <a href="{{ route('admin.areas.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-slate-700">🏭 Áreas</a>
        @endif
    </div>
</nav>
