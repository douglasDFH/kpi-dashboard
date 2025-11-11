@extends('components.layouts.app')

@section('title', 'Detalle Jornada')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $jornada->maquina->nombre }}</h1>
            <p class="text-gray-600 mt-2">Jornada del {{ $jornada->fecha_inicio->format('d/m/Y') }}</p>
        </div>
        <span class="px-4 py-2 inline-flex text-lg leading-5 font-semibold rounded-full
            {{ $jornada->status === 'running' ? 'bg-green-100 text-green-800' : '' }}
            {{ $jornada->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : '' }}
            {{ $jornada->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
        ">
            {{ ucfirst($jornada->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-600 text-sm">Inicio</div>
            <div class="text-2xl font-bold text-gray-900">{{ $jornada->fecha_inicio->format('H:i') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-600 text-sm">Producción</div>
            <div class="text-2xl font-bold text-gray-900">{{ $jornada->cantidad_producida ?? 0 }}</div>
            <div class="text-gray-600 text-xs">unidades</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-600 text-sm">Calidad</div>
            <div class="text-2xl font-bold text-green-600">{{ $jornada->cantidad_buena ?? 0 }}</div>
            <div class="text-gray-600 text-xs">buenas</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-600 text-sm">Defectuosas</div>
            <div class="text-2xl font-bold text-red-600">{{ ($jornada->cantidad_producida ?? 0) - ($jornada->cantidad_buena ?? 0) }}</div>
            <div class="text-gray-600 text-xs">malas</div>
        </div>
    </div>

    @if($jornada->status === 'running' || $jornada->status === 'paused')
        <div class="mb-6 space-x-4">
            @if($jornada->status === 'running')
                <form action="{{ route('supervisor.jornadas.pausar', $jornada) }}" method="POST" class="inline">
                    @csrf
                    <button type="button" onclick="showPausarModal()" class="btn-warning">
                        ⏸ Pausar Producción
                    </button>
                </form>
            @elseif($jornada->status === 'paused')
                <form action="{{ route('supervisor.jornadas.reanudar', $jornada) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-success">
                        ▶ Reanudar Producción
                    </button>
                </form>
            @endif

            <form action="{{ route('supervisor.jornadas.finalizar', $jornada) }}" method="POST" class="inline">
                @csrf
                <button type="submit" onclick="return confirm('¿Finalizar esta jornada?')" class="btn-primary">
                    ✓ Finalizar Jornada
                </button>
            </form>
        </div>
    @endif

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
    <div id="pausarModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Pausar Producción</h3>
            <form action="{{ route('supervisor.jornadas.pausar', $jornada) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="motivo" class="block text-sm font-medium text-gray-700 mb-2">Motivo</label>
                    <select name="motivo" id="motivo" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                        <option value="">-- Selecciona un motivo --</option>
                        <option value="Cambio de turno">Cambio de turno</option>
                        <option value="Fallo de máquina">Fallo de máquina</option>
                        <option value="Falta de materia prima">Falta de materia prima</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                        <option value="Limpieza">Limpieza</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 btn-warning">Confirmar Parada</button>
                    <button type="button" onclick="hidePausarModal()" class="flex-1 btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showPausarModal() {
            document.getElementById('pausarModal').classList.remove('hidden');
        }
        function hidePausarModal() {
            document.getElementById('pausarModal').classList.add('hidden');
        }
    </script>
</div>
@endsection
