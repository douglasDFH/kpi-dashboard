<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Dashboard - Indicadores en Tiempo Real</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Metalúrgica Precision S.A.</h1>
                        <p class="text-gray-600">Monitor en Tiempo Real de Indicadores Clave de Desempeño</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('equipment.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Equipos
                        </a>
                        <a href="{{ route('production.index') }}" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Producción
                        </a>
                        <a href="{{ route('quality.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Calidad
                        </a>
                        <a href="{{ route('downtime.index') }}" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tiempos Muertos
                        </a>
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Reportes
                        </a>
                        <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Usuarios
                        </a>
                        <a href="{{ route('audit.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Auditoría
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <!-- Equipment Selector -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Seleccionar Equipo</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="equipment-selector">
                    @foreach ($equipment as $eq)
                        <button
                            class="equipment-btn px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                            data-equipment-id="{{ $eq->id }}">
                            <div class="text-sm font-medium">{{ $eq->name }}</div>
                            <div class="text-xs opacity-75">{{ $eq->code }}</div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- OEE Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- OEE Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-gray-600 text-sm font-medium mb-2">OEE (Eficiencia General)</h3>
                    <div class="text-4xl font-bold text-blue-600" id="oee-value">--</div>
                    <p class="text-xs text-gray-500 mt-2">Overall Equipment Effectiveness</p>
                </div>

                <!-- Availability Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-gray-600 text-sm font-medium mb-2">Disponibilidad</h3>
                    <div class="text-4xl font-bold text-green-600" id="availability-value">--</div>
                    <p class="text-xs text-gray-500 mt-2">Tiempo activo vs planificado</p>
                </div>

                <!-- Performance Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-gray-600 text-sm font-medium mb-2">Rendimiento</h3>
                    <div class="text-4xl font-bold text-orange-600" id="performance-value">--</div>
                    <p class="text-xs text-gray-500 mt-2">Produccion real vs planificada</p>
                </div>

                <!-- Quality Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-gray-600 text-sm font-medium mb-2">Calidad</h3>
                    <div class="text-4xl font-bold text-purple-600" id="quality-value">--</div>
                    <p class="text-xs text-gray-500 mt-2">Unidades buenas vs total</p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- OEE Components Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Componentes del OEE</h3>
                    <canvas id="oee-chart"></canvas>
                </div>

                <!-- Production Metrics Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Metricas de Produccion</h3>
                    <canvas id="production-chart"></canvas>
                </div>
            </div>

            <!-- Real-time Updates Indicator -->
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert" id="realtime-indicator" style="display: none;">
                <strong class="font-bold">Actualizacion en tiempo real!</strong>
                <span class="block sm:inline">Nuevos datos recibidos.</span>
            </div>

            <!-- Additional Metrics -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Métricas Adicionales</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-600 text-sm">Produccion Total</p>
                        <p class="text-2xl font-bold text-gray-800" id="total-production">--</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Unidades Defectuosas</p>
                        <p class="text-2xl font-bold text-red-600" id="defective-units">--</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Tiempo de Inactividad (min)</p>
                        <p class="text-2xl font-bold text-orange-600" id="total-downtime">--</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let currentEquipmentId = null; // Se asignará dinámicamente
        let oeeChart = null;
        let productionChart = null;

        // Initialize charts
        function initCharts() {
            const oeeCtx = document.getElementById('oee-chart').getContext('2d');
            oeeChart = new Chart(oeeCtx, {
                type: 'bar',
                data: {
                    labels: ['Disponibilidad', 'Rendimiento', 'Calidad'],
                    datasets: [{
                        label: 'Porcentaje (%)',
                        data: [0, 0, 0],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.7)',
                            'rgba(251, 146, 60, 0.7)',
                            'rgba(168, 85, 247, 0.7)'
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(251, 146, 60)',
                            'rgb(168, 85, 247)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: true
                }
            });

            const prodCtx = document.getElementById('production-chart').getContext('2d');
            productionChart = new Chart(prodCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Unidades Buenas', 'Unidades Defectuosas'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.7)',
                            'rgba(239, 68, 68, 0.7)'
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true
                }
            });
        }

        // Fetch KPI data
        async function fetchKPIData(equipmentId) {
            if (!equipmentId) {
                console.error('❌ No equipment ID provided');
                return;
            }

            try {
                console.log(`📡 Obteniendo datos para equipo ${equipmentId}...`);
                const response = await axios.get(`/api/kpi/${equipmentId}`);
                
                if (response.data.success && response.data.data) {
                    console.log('✅ Datos recibidos:', response.data.data);
                    updateDashboard(response.data.data);
                } else {
                    console.error('❌ Respuesta API inválida:', response.data);
                    alert('Error: No se pudieron obtener los datos del KPI');
                }
            } catch (error) {
                console.error('❌ Error fetching KPI data:', error);
                if (error.response) {
                    console.error('Error response:', error.response.data);
                }
                alert('Error: No se pudo conectar con el servidor');
            }
        }

        // Update dashboard with data
        function updateDashboard(data) {
            console.log('📊 Actualizando dashboard con:', data);
            
            const oee = data.oee || {};
            const metrics = data.metrics || {};

            // Validar que los datos existan antes de actualizar
            if (!oee || Object.keys(oee).length === 0) {
                console.warn('⚠️ Sin datos OEE disponibles aún');
                // Mostrar valores por defecto
                document.getElementById('oee-value').textContent = '0.0%';
                document.getElementById('availability-value').textContent = '0.0%';
                document.getElementById('performance-value').textContent = '0.0%';
                document.getElementById('quality-value').textContent = '0.0%';
                return;
            }

            console.log('✅ Actualizando valores en interfaz...');

            // Update OEE cards
            document.getElementById('oee-value').textContent = (oee.oee || 0).toFixed(1) + '%';
            document.getElementById('availability-value').textContent = (oee.availability || 0).toFixed(1) + '%';
            document.getElementById('performance-value').textContent = (oee.performance || 0).toFixed(1) + '%';
            document.getElementById('quality-value').textContent = (oee.quality || 0).toFixed(1) + '%';

            // Update additional metrics
            document.getElementById('total-production').textContent = metrics.total_production || 0;
            document.getElementById('defective-units').textContent = metrics.defective_units || 0;
            document.getElementById('total-downtime').textContent = metrics.total_downtime_minutes || 0;

            // Update charts
            if (oeeChart) {
                oeeChart.data.datasets[0].data = [
                    (oee.availability || 0).toFixed(1),
                    (oee.performance || 0).toFixed(1),
                    (oee.quality || 0).toFixed(1)
                ];
                oeeChart.update();
                console.log('📊 Gráfico OEE actualizado');
            }

            if (productionChart) {
                const totalProd = metrics.total_production || 0;
                const defective = metrics.defective_units || 0;
                const goodUnits = totalProd - defective;
                productionChart.data.datasets[0].data = [goodUnits, defective];
                productionChart.update();
                console.log('📊 Gráfico de Producción actualizado');
            }
            
            console.log('✅ Dashboard actualizado exitosamente');
        }

        // Select equipment
        function selectEquipment(equipmentId) {
            currentEquipmentId = equipmentId;
            console.log(`🔧 Seleccionando equipo ${equipmentId}`);

            // Update button styles
            document.querySelectorAll('.equipment-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'ring-4', 'ring-blue-300');
                btn.classList.add('bg-blue-500');
            });
            document.querySelector(`[data-equipment-id="${equipmentId}"]`).classList.add('bg-blue-600', 'ring-4', 'ring-blue-300');
            document.querySelector(`[data-equipment-id="${equipmentId}"]`).classList.remove('bg-blue-500');

            console.log('📡 Cargando datos del equipo...');
            fetchKPIData(equipmentId);
        }

        // Show real-time update indicator
        function showRealtimeIndicator() {
            const indicator = document.getElementById('realtime-indicator');
            indicator.style.display = 'block';
            setTimeout(() => {
                indicator.style.display = 'none';
            }, 3000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
            
            // Obtener el primer equipo disponible y seleccionarlo automáticamente
            const firstButton = document.querySelector('.equipment-btn');
            if (firstButton) {
                currentEquipmentId = parseInt(firstButton.getAttribute('data-equipment-id'));
                console.log('✅ Equipo inicial seleccionado:', currentEquipmentId);
                firstButton.click(); // Simular click para cargar datos
            } else {
                console.warn('⚠️ No hay equipos disponibles en el sistema');
                alert('❌ Por favor, cree al menos un equipo en la sección de Equipos');
                return;
            }

            // Add click listeners to equipment buttons
            document.querySelectorAll('.equipment-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const equipmentId = parseInt(this.getAttribute('data-equipment-id'));
                    selectEquipment(equipmentId);
                });
            });

            // Setup WebSocket for real-time updates
            if (window.Echo) {
                console.log('✅ Echo está disponible - Conectando a broadcasting...');
                window.Echo.channel('kpi-dashboard')
                    .listen('.production.updated', (e) => {
                        console.log('📊 Evento de actualización recibido:', e);
                        showRealtimeIndicator();
                        // Esperar 500ms para asegurar que los datos se guardaron en BD
                        setTimeout(() => {
                            if (currentEquipmentId) {
                                fetchKPIData(currentEquipmentId);
                            }
                        }, 500);
                    })
                    .listen('.kpi.updated', (e) => {
                        console.log('📈 KPI actualizado:', e);
                        showRealtimeIndicator();
                        if (e.equipment_id === currentEquipmentId && e.kpi_data) {
                            updateDashboard({ oee: e.kpi_data, metrics: {} });
                        }
                    })
                    .error((error) => {
                        console.error('❌ Error en el canal de broadcasting:', error);
                    });
            } else {
                console.warn('⚠️ Echo no está disponible. Usando solo polling.');
            }

            // Refresh data every 10 seconds as fallback
            setInterval(() => {
                if (currentEquipmentId) {
                    fetchKPIData(currentEquipmentId);
                }
            }, 10000);
        });
    </script>
</body>
</html>
