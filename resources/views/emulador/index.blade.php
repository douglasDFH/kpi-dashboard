<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">
                            🤖 Emulador de Máquinas
                        </h2>
                        <span class="text-sm text-gray-500">
                            Simulación de producción en tiempo real
                        </span>
                    </div>

                    <!-- Grid de Máquinas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($maquinas as $maquina)
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow"
                                 x-data="maquinaEmulador('{{ $maquina->id }}', '{{ $maquina->nombre }}')">
                                
                                <!-- Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-bold text-lg">{{ $maquina->nombre }}</h3>
                                        <p class="text-xs text-gray-500">{{ $maquina->area->nombre ?? 'Sin área' }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($maquina->status === 'running') bg-green-100 text-green-800
                                        @elseif($maquina->status === 'stopped') bg-red-100 text-red-800
                                        @elseif($maquina->status === 'maintenance') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($maquina->status) }}
                                    </span>
                                </div>

                                <!-- Estado de Jornada -->
                                @if($maquina->jornadasProduccion->isNotEmpty())
                                    @php $jornada = $maquina->jornadasProduccion->first(); @endphp
                                    <div class="mb-4 p-3 bg-blue-50 rounded">
                                        <p class="text-xs font-semibold text-blue-800 mb-1">Jornada Activa</p>
                                        <div class="text-xs text-blue-700">
                                            <p>Objetivo: {{ $jornada->objetivo_unidades_copiado }} unidades</p>
                                            <p>Producidas: {{ $jornada->total_unidades_producidas }}</p>
                                            <p>Progreso: {{ round(($jornada->total_unidades_producidas / $jornada->objetivo_unidades_copiado) * 100, 1) }}%</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-4 p-3 bg-yellow-50 rounded">
                                        <p class="text-xs text-yellow-800">⚠️ No hay jornada activa</p>
                                    </div>
                                @endif

                                <!-- Formulario de Emulación -->
                                <form @submit.prevent="enviarProduccion" class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Cantidad Producida
                                        </label>
                                        <input type="number" x-model="cantidadProducida" 
                                               min="1" required
                                               class="w-full px-3 py-2 border rounded-md text-sm">
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                                Buenas
                                            </label>
                                            <input type="number" x-model="cantidadBuena" 
                                                   min="0" required
                                                   class="w-full px-3 py-2 border rounded-md text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                                Malas
                                            </label>
                                            <input type="number" x-model="cantidadMala" 
                                                   min="0" required
                                                   class="w-full px-3 py-2 border rounded-md text-sm">
                                        </div>
                                    </div>

                                    <button type="submit" 
                                            :disabled="enviando"
                                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white py-2 rounded-md text-sm font-medium transition-colors">
                                        <span x-show="!enviando">▶️ Enviar Producción</span>
                                        <span x-show="enviando">⏳ Enviando...</span>
                                    </button>
                                </form>

                                <!-- Log de Respuestas -->
                                <div x-show="ultimaRespuesta" class="mt-4 p-2 rounded text-xs" 
                                     :class="respuestaExitosa ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'">
                                    <p class="font-semibold" x-text="respuestaExitosa ? '✓ Éxito' : '✗ Error'"></p>
                                    <p x-text="ultimaRespuesta" class="mt-1"></p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($maquinas->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500">No hay máquinas disponibles</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function maquinaEmulador(maquinaId, nombre) {
            return {
                maquinaId: maquinaId,
                nombre: nombre,
                cantidadProducida: 10,
                cantidadBuena: 9,
                cantidadMala: 1,
                enviando: false,
                ultimaRespuesta: null,
                respuestaExitosa: false,

                enviarProduccion() {
                    this.enviando = true;
                    this.ultimaRespuesta = null;

                    fetch('/emulador/emular', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            maquina_id: this.maquinaId,
                            cantidad_producida: parseInt(this.cantidadProducida),
                            cantidad_buena: parseInt(this.cantidadBuena),
                            cantidad_mala: parseInt(this.cantidadMala)
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.respuestaExitosa = data.success;
                        this.ultimaRespuesta = data.message;
                        
                        if (data.success) {
                            // Generar nuevos valores aleatorios
                            this.cantidadProducida = Math.floor(Math.random() * 5) + 8; // 8-12
                            this.cantidadMala = Math.floor(Math.random() * 3); // 0-2
                            this.cantidadBuena = this.cantidadProducida - this.cantidadMala;
                        }
                    })
                    .catch(error => {
                        this.respuestaExitosa = false;
                        this.ultimaRespuesta = 'Error de red: ' + error.message;
                    })
                    .finally(() => {
                        this.enviando = false;
                    });
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>
