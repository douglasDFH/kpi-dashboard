<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">
                            🤖 Seleccionar Máquina para Emular
                        </h2>
                        <p class="text-gray-600">
                            Cada emulador simula UNA máquina individual con conexión WebSocket
                        </p>
                    </div>

                    <!-- Grid de Máquinas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($maquinas as $maquina)
                            <a href="{{ route('emulador.show', $maquina->id) }}"
                                class="border-2 border-gray-300 rounded-lg p-6 hover:border-blue-500 hover:shadow-lg transition-all group">

                                <!-- Icono -->
                                <div class="flex justify-center mb-4">
                                    <div
                                        class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                        <span class="text-3xl">🏭</span>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="text-center">
                                    <h3 class="font-bold text-lg text-gray-800 mb-1">
                                        {{ $maquina->nombre }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-3">
                                        {{ $maquina->area->nombre ?? 'Sin área' }}
                                    </p>
                                    <span
                                        class="inline-block px-3 py-1 text-xs rounded-full
                                        @if ($maquina->status === 'running') bg-green-100 text-green-800
                                        @elseif($maquina->status === 'stopped') bg-red-100 text-red-800
                                        @elseif($maquina->status === 'maintenance') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($maquina->status) }}
                                    </span>
                                </div>

                                <!-- Acción -->
                                <div class="mt-4 text-center">
                                    <span class="text-blue-600 font-semibold group-hover:text-blue-700">
                                        Abrir Emulador →
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if ($maquinas->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">No hay máquinas disponibles</p>
                        </div>
                    @endif

                    <!-- Instrucciones -->
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h3 class="font-bold text-blue-900 mb-3">📋 Instrucciones:</h3>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
                            <li>Selecciona una máquina para abrir su emulador individual</li>
                            <li>Haz clic en "🔌 Conectar" para establecer conexión WebSocket</li>
                            <li>El dashboard verá la máquina conectada en tiempo real</li>
                            <li>Usa "▶️ Modo Auto" para enviar producción cada 15 segundos automáticamente</li>
                            <li>Modifica los valores entre envíos para simular producción variable</li>
                            <li>Abre múltiples pestañas con diferentes máquinas para simular producción paralela</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>