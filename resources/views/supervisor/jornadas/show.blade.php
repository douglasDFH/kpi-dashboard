<x-layouts.app title="Detalle Jornada">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-4 h-4 rounded-full" :class="'{{ $jornada->status }}' === 'running' ? 'bg-green-500 animate-pulse' : '{{ $jornada->status }}' === 'paused' ? 'bg-yellow-500' : 'bg-blue-500'"></div>
                <h1 class="text-4xl font-bold text-gray-900">{{ $jornada->maquina->nombre }}</h1>
            </div>
            <p class="text-gray-600 mt-2">
                <span class="font-semibold">{{ $jornada->maquina->codigo }}</span> • 
                Jornada {{ $jornada->fecha_inicio->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="text-right">
            <span class="px-4 py-2 inline-flex text-lg leading-5 font-semibold rounded-full
                {{ $jornada->status === 'running' ? 'bg-green-100 text-green-800' : '' }}
                {{ $jornada->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $jornada->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
            ">
                {{ $jornada->status === 'running' ? '🟢 En Ejecución' : ($jornada->status === 'paused' ? '🟡 Pausada' : '🔵 Finalizada') }}
            </span>
            @if($jornada->status !== 'completed')
                <div class="mt-4 text-sm text-gray-600">
                    <span class="font-semibold">{{ now()->diffInMinutes($jornada->fecha_inicio) }}</span> minutos
                </div>
            @endif
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-linear-to-br from-blue-50 to-blue-100 rounded-lg shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-blue-600 text-sm font-semibold uppercase tracking-wider">Tiempo</div>
                    <div class="text-3xl font-bold text-blue-900 mt-2">
                        {{ $jornada->fecha_inicio->format('H:i') }}
                    </div>
                </div>
                <div class="text-5xl opacity-20">⏱</div>
            </div>
        </div>

        <div class="bg-linear-to-br from-purple-50 to-purple-100 rounded-lg shadow p-5 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-purple-600 text-sm font-semibold uppercase tracking-wider">Producción</div>
                    <div class="text-3xl font-bold text-purple-900 mt-2">
                        {{ $jornada->cantidad_producida ?? 0 }}
                    </div>
                    <div class="text-xs text-purple-700 mt-1">unidades</div>
                </div>
                <div class="text-5xl opacity-20">📦</div>
            </div>
        </div>

        <div class="bg-linear-to-br from-green-50 to-green-100 rounded-lg shadow p-5 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-green-600 text-sm font-semibold uppercase tracking-wider">Conformes</div>
                    <div class="text-3xl font-bold text-green-900 mt-2">
                        {{ $jornada->cantidad_buena ?? 0 }}
                    </div>
                    <div class="text-xs text-green-700 mt-1">
                        @if($jornada->cantidad_producida > 0)
                            {{ round(($jornada->cantidad_buena ?? 0) / $jornada->cantidad_producida * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                </div>
                <div class="text-5xl opacity-20">✓</div>
            </div>
        </div>

        <div class="bg-linear-to-br from-red-50 to-red-100 rounded-lg shadow p-5 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-red-600 text-sm font-semibold uppercase tracking-wider">Defectuosas</div>
                    <div class="text-3xl font-bold text-red-900 mt-2">
                        {{ ($jornada->cantidad_producida ?? 0) - ($jornada->cantidad_buena ?? 0) }}
                    </div>
                    <div class="text-xs text-red-700 mt-1">
                        @if($jornada->cantidad_producida > 0)
                            {{ round(((($jornada->cantidad_producida ?? 0) - ($jornada->cantidad_buena ?? 0)) / $jornada->cantidad_producida) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                </div>
                <div class="text-5xl opacity-20">✗</div>
            </div>
        </div>

        <div class="bg-linear-to-br from-orange-50 to-orange-100 rounded-lg shadow p-5 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-orange-600 text-sm font-semibold uppercase tracking-wider">Paradas</div>
                    <div class="text-3xl font-bold text-orange-900 mt-2">
                        {{ $jornada->eventosParada->count() }}
                    </div>
                    @if($jornada->eventosParada->count() > 0)
                        <div class="text-xs text-orange-700 mt-1">
                            {{ $jornada->eventosParada->sum(fn($e) => $e->fin_parada ? $e->created_at->diffInMinutes($e->fin_parada) : 0) }} min
                        </div>
                    @endif
                </div>
                <div class="text-5xl opacity-20">⏸</div>
            </div>
        </div>
    </div>

    @if($jornada->status === 'running' || $jornada->status === 'paused')
        <div class="mb-6 flex flex-wrap gap-3">
            @if($jornada->status === 'running')
                <button @click="showPausarModal = true" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center space-x-2 transition">
                    <span>⏸</span>
                    <span>Pausar Producción</span>
                </button>
            @elseif($jornada->status === 'paused')
                <form action="{{ route('supervisor.jornadas.reanudar', $jornada) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center space-x-2 transition">
                        <span>▶</span>
                        <span>Reanudar Producción</span>
                    </button>
                </form>
            @endif

            <form action="{{ route('supervisor.jornadas.finalizar', $jornada) }}" method="POST" class="inline">
                @csrf
                <button type="button" @click="if(confirm('¿Finalizar esta jornada?')) $el.closest('form').submit()" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center space-x-2 transition">
                    <span>✓</span>
                    <span>Finalizar Jornada</span>
                </button>
            </form>

            <a href="{{ route('supervisor.jornadas.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center space-x-2 transition">
                <span>←</span>
                <span>Volver</span>
            </a>
        </div>
    @endif

    <!-- Contenido con Alpine.js -->
    <div x-data="{ showPausarModal: false }">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Registros de Producción -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Registros de Producción</h2>
            @if($jornada->registrosProduccion->count())
                <div class="space-y-2">
                    @foreach($jornada->registrosProduccion as $registro)
                        <div class="border-l-4 border-blue-500 pl-4 py-2">
                            <div class="text-sm text-gray-600">{{ $registro->created_at->format('H:i:s') }}</div>
                            <div class="font-semibold text-gray-900">{{ $registro->cantidad_producida }} unidades</div>
                            <div class="text-xs text-gray-500">
                                {{ $registro->cantidad_buena }} buenas, {{ $registro->cantidad_mala }} malas
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Sin registros de producción</p>
            @endif
        </div>

        <!-- Eventos de Parada -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Paradas</h2>
            @if($jornada->eventosParada->count())
                <div class="space-y-2">
                    @foreach($jornada->eventosParada as $evento)
                        <div class="border-l-4 border-yellow-500 pl-4 py-2">
                            <div class="text-sm text-gray-600">{{ $evento->created_at->format('H:i:s') }}</div>
                            <div class="font-semibold text-gray-900">{{ $evento->motivo }}</div>
                            <div class="text-xs text-gray-500">
                                @if($evento->fin_parada)
                                    {{ $evento->created_at->diffInMinutes($evento->fin_parada) }} minutos
                                @else
                                    En progreso...
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Sin paradas registradas</p>
            @endif
        </div>
    </div>

    <!-- Modal Pausar -->
    <div @show="showPausarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;" x-cloak>
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Pausar Producción</h3>
            <form action="{{ route('supervisor.jornadas.pausar', $jornada) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="motivo" class="block text-sm font-medium text-gray-700 mb-2">Motivo de Parada</label>
                    <select name="motivo" id="motivo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" required>
                        <option value="">-- Selecciona un motivo --</option>
                        <option value="Cambio de turno">🔄 Cambio de turno</option>
                        <option value="Fallo de máquina">🔧 Fallo de máquina</option>
                        <option value="Falta de materia prima">📦 Falta de materia prima</option>
                        <option value="Mantenimiento">🛠️ Mantenimiento</option>
                        <option value="Limpieza">🧹 Limpieza</option>
                        <option value="Otro">⚠️ Otro</option>
                    </select>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg transition">
                        Confirmar Parada
                    </button>
                    <button type="button" @click="showPausarModal = false" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold py-2 px-4 rounded-lg transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-layouts.app>
