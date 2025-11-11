<x-layouts.app title="Emulador - {{ $maquina->nombre }}">
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">
                                🤖 {{ $maquina->nombre }}
                            </h2>
                            <p class="text-sm text-gray-500">{{ $maquina->area->nombre ?? 'Sin área' }}</p>
                        </div>
                        <a href="{{ route('emulador.index') }}"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm">
                            ← Volver
                        </a>
                    </div>

                    <!-- Emulador de Máquina Individual -->
                    <div x-data="emuladorMaquina('{{ $maquina->id }}', '{{ $maquina->nombre }}')" class="space-y-6">

                        <!-- Estado de Conexión -->
                        <div class="border rounded-lg p-4"
                            :class="conectado ? 'border-green-500 bg-green-50' : 'border-gray-300 bg-gray-50'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 rounded-full"
                                        :class="conectado ? 'bg-green-500 animate-pulse' : 'bg-gray-400'"></div>
                                    <span class="font-semibold"
                                        x-text="conectado ? '✅ Conectado' : '⚪ Desconectado'"></span>
                                </div>
                                <button type="button" @click="conectarMaquina" :disabled="conectado || conectando"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg text-sm font-medium transition-colors">
                                    <span x-show="!conectando && !conectado">🔌 Conectar</span>
                                    <span x-show="conectando">⏳ Conectando...</span>
                                    <span x-show="conectado">✓ Conectado</span>
                                </button>
                            </div>
                        </div>

                        <!-- Estado de Jornada -->
                        @if ($maquina->jornadasProduccion->isNotEmpty())
                            @php $jornada = $maquina->jornadasProduccion->first(); @endphp
                            <div class="border rounded-lg p-4 bg-blue-50 border-blue-300">
                                <h3 class="font-semibold text-blue-900 mb-3">📋 Jornada Activa</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-blue-700 font-medium">Objetivo:</p>
                                        <p class="text-lg font-bold text-blue-900">
                                            {{ $jornada->objetivo_unidades_copiado }} unidades</p>
                                    </div>
                                    <div>
                                        <p class="text-blue-700 font-medium">Producidas:</p>
                                        <p class="text-lg font-bold text-blue-900" id="unidades-producidas">
                                            {{ $jornada->total_unidades_producidas }}</p>
                                    </div>
                                    <div>
                                        <p class="text-blue-700 font-medium">Progreso:</p>
                                        <p class="text-lg font-bold text-blue-900" id="progreso">
                                            {{ $jornada->objetivo_unidades_copiado > 0 ? round(($jornada->total_unidades_producidas / $jornada->objetivo_unidades_copiado) * 100, 1) : 0 }}%
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-blue-700 font-medium">Estado:</p>
                                        <p class="text-lg font-bold text-blue-900">{{ ucfirst($jornada->status) }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="border rounded-lg p-4 bg-yellow-50 border-yellow-300">
                                <p class="text-sm text-yellow-800">⚠️ No hay jornada activa para esta máquina</p>
                            </div>
                        @endif

                        <!-- Panel de Control de Producción -->
                        <div class="border rounded-lg p-6 bg-white">
                            <h3 class="font-semibold text-gray-900 mb-4">🎛️ Control de Producción</h3>

                            <!-- Inputs de Producción -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Total Producidas
                                        </label>
                                        <input type="number" x-model="cantidadProducida" min="1"
                                            class="w-full px-4 py-2 border rounded-lg text-center text-lg font-semibold">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            ✓ Buenas
                                        </label>
                                        <input type="number" x-model="cantidadBuena" min="0"
                                            class="w-full px-4 py-2 border rounded-lg text-center text-lg font-semibold text-green-600">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            ✗ Malas
                                        </label>
                                        <input type="number" x-model="cantidadMala" min="0"
                                            class="w-full px-4 py-2 border rounded-lg text-center text-lg font-semibold text-red-600">
                                    </div>
                                </div>

                                <!-- Botones de Control -->
                                <div class="flex gap-4">
                                    <button type="button" @click="toggleAutoMode" :disabled="!conectado"
                                        class="flex-1 py-3 rounded-lg text-white font-semibold text-lg transition-colors"
                                        :class="modoAuto ? 'bg-orange-600 hover:bg-orange-700' : 'bg-blue-600 hover:bg-blue-700'"
                                        class:disabled="!conectado">
                                        <span x-show="!modoAuto">▶️ Iniciar Modo Auto (15s)</span>
                                        <span x-show="modoAuto">⏸️ Detener Modo Auto</span>
                                    </button>

                                    <button type="button" @click="enviarProduccionManual"
                                        :disabled="!conectado || enviando || modoAuto"
                                        class="flex-1 py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg font-semibold text-lg transition-colors">
                                        <span x-show="!enviando">📤 Enviar Manual</span>
                                        <span x-show="enviando">⏳ Enviando...</span>
                                    </button>
                                </div>

                                <!-- Indicador de Modo Auto -->
                                <div x-show="modoAuto" class="bg-orange-50 border border-orange-300 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 bg-orange-500 rounded-full animate-pulse"></div>
                                            <span class="font-semibold text-orange-900">🔄 Modo Automático Activo</span>
                                        </div>
                                        <div class="text-sm text-orange-700">
                                            Próximo envío en: <span x-text="countdown" class="font-bold"></span>s
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Log de Actividad -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h3 class="font-semibold text-gray-900 mb-3">📜 Log de Actividad</h3>
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                <template x-for="(log, index) in logs" :key="index">
                                    <div class="text-xs p-2 rounded"
                                        :class="log.tipo === 'success' ? 'bg-green-100 text-green-800' :
                                            log.tipo === 'error' ? 'bg-red-100 text-red-800' :
                                            'bg-blue-100 text-blue-800'">
                                        <span class="font-semibold" x-text="log.timestamp"></span> -
                                        <span x-text="log.mensaje"></span>
                                    </div>
                                </template>
                                <div x-show="logs.length === 0" class="text-center text-gray-500 text-sm py-4">
                                    Sin actividad aún
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        @vite(['resources/js/echo.js'])
    
        <script>
            function emuladorMaquina(maquinaId, nombre) {
                return {
                        maquinaId: maquinaId,
                        nombre: nombre,
                        conectado: false,
                        conectando: false,
                        enviando: false,
                        modoAuto: false,
                        countdown: 15,
                        intervalId: null,
                        countdownId: null,
                        cantidadProducida: 10,
                        cantidadBuena: 9,
                        cantidadMala: 1,
                        logs: [],
    
                        init() {
                            console.log(`🤖 Emulador de ${this.nombre} inicializado`);
                            this.addLog('info', `Emulador inicializado para ${this.nombre}`);
                        },
    
                        addLog(tipo, mensaje) {
                            const timestamp = new Date().toLocaleTimeString();
                            this.logs.unshift({
                                tipo,
                                mensaje,
                                timestamp
                            });
                            if (this.logs.length > 50) this.logs.pop();
                        },
    
                        async conectarMaquina() {
                            this.conectando = true;
                            this.addLog('info', 'Intentando conectar...');
    
                            try {
                                const response = await fetch('/emulador/conectar', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        maquina_id: this.maquinaId
                                    })
                                });
    
                                const data = await response.json();
    
                                if (data.success) {
                                    this.conectado = true;
                                    this.addLog('success', '✅ Conectado exitosamente al servidor');
                                    console.log(`✅ ${this.nombre} conectado`);
                                } else {
                                    this.addLog('error', `❌ Error: ${data.message}`);
                                }
                            } catch (error) {
                                this.addLog('error', `❌ Error de conexión: ${error.message}`);
                            } finally {
                                this.conectando = false;
                            }
                        },
    
                        toggleAutoMode() {
                            if (this.modoAuto) {
                                this.detenerModoAuto();
                            } else {
                                this.iniciarModoAuto();
                            }
                        },
    
                        iniciarModoAuto() {
                            this.modoAuto = true;
                            this.countdown = 15;
                            this.addLog('info', '▶️ Modo automático iniciado - Envío cada 15s');
    
                            // Enviar inmediatamente el primero
                            this.enviarProduccionAuto();
    
                            // Configurar intervalo de 15 segundos
                            this.intervalId = setInterval(() => {
                                this.enviarProduccionAuto();
                                this.countdown = 15;
                            }, 15000);
    
                            // Countdown visual
                            this.countdownId = setInterval(() => {
                                if (this.countdown > 0) {
                                    this.countdown--;
                                }
                            }, 1000);
                        },
    
                        detenerModoAuto() {
                            this.modoAuto = false;
                            if (this.intervalId) clearInterval(this.intervalId);
                            if (this.countdownId) clearInterval(this.countdownId);
                            this.addLog('info', '⏸️ Modo automático detenido');
                        },
    
                        async enviarProduccionAuto() {
                            // Generar valores aleatorios para simular producción real
                            this.cantidadProducida = Math.floor(Math.random() * 8) + 8; // 8-15
                            this.cantidadMala = Math.floor(Math.random() * 3); // 0-2
                            this.cantidadBuena = this.cantidadProducida - this.cantidadMala;
    
                            await this.enviarProduccion();
                        },
    
                        async enviarProduccionManual() {
                            await this.enviarProduccion();
                        },
    
                        async enviarProduccion() {
                            if (this.enviando) return;
    
                            this.enviando = true;
                            this.addLog('info',
                                `📤 Enviando: ${this.cantidadProducida} (✓${this.cantidadBuena} ✗${this.cantidadMala})`);
    
                            try {
                                const response = await fetch('/emulador/emular', {
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
                                });
    
                                const data = await response.json();
    
                                if (data.success) {
                                    this.addLog('success', '✅ Producción registrada exitosamente');
                                } else {
                                    this.addLog('error', `❌ Error: ${data.message}`);
                                    // Si hay error, detener modo auto
                                    if (this.modoAuto) this.detenerModoAuto();
                                }
                            } catch (error) {
                                this.addLog('error', `❌ Error de red: ${error.message}`);
                                if (this.modoAuto) this.detenerModoAuto();
                            } finally {
                                this.enviando = false;
                            }
                        }
                    }
                }
        </script>
    @endpush
</x-layouts.app>

