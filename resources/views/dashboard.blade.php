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
                <h1 class="text-3xl font-bold text-gray-800">KPI Dashboard Industrial</h1>
                <p class="text-gray-600">Monitor en Tiempo Real de Indicadores Clave de Desempeño</p>
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
        let currentEquipmentId = 1; // Default equipment
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
            try {
                const response = await axios.get(`/api/kpi/${equipmentId}`);
                const data = response.data.data;

                updateDashboard(data);
            } catch (error) {
                console.error('Error fetching KPI data:', error);
            }
        }

        // Update dashboard with data
        function updateDashboard(data) {
            const oee = data.oee;
            const metrics = data.metrics;

            // Update OEE cards
            document.getElementById('oee-value').textContent = oee.oee.toFixed(1) + '%';
            document.getElementById('availability-value').textContent = oee.availability.toFixed(1) + '%';
            document.getElementById('performance-value').textContent = oee.performance.toFixed(1) + '%';
            document.getElementById('quality-value').textContent = oee.quality.toFixed(1) + '%';

            // Update additional metrics
            document.getElementById('total-production').textContent = metrics.total_production;
            document.getElementById('defective-units').textContent = metrics.defective_units;
            document.getElementById('total-downtime').textContent = metrics.total_downtime_minutes;

            // Update charts
            if (oeeChart) {
                oeeChart.data.datasets[0].data = [
                    oee.availability.toFixed(1),
                    oee.performance.toFixed(1),
                    oee.quality.toFixed(1)
                ];
                oeeChart.update();
            }

            if (productionChart) {
                const goodUnits = metrics.total_production - metrics.defective_units;
                productionChart.data.datasets[0].data = [goodUnits, metrics.defective_units];
                productionChart.update();
            }
        }

        // Select equipment
        function selectEquipment(equipmentId) {
            currentEquipmentId = equipmentId;

            // Update button styles
            document.querySelectorAll('.equipment-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'ring-4', 'ring-blue-300');
                btn.classList.add('bg-blue-500');
            });
            document.querySelector(`[data-equipment-id="${equipmentId}"]`).classList.add('bg-blue-600', 'ring-4', 'ring-blue-300');
            document.querySelector(`[data-equipment-id="${equipmentId}"]`).classList.remove('bg-blue-500');

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
            fetchKPIData(currentEquipmentId);

            // Add click listeners to equipment buttons
            document.querySelectorAll('.equipment-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const equipmentId = parseInt(this.getAttribute('data-equipment-id'));
                    selectEquipment(equipmentId);
                });
            });

            // Setup WebSocket for real-time updates
            if (window.Echo) {
                window.Echo.channel('kpi-dashboard')
                    .listen('.production.updated', (e) => {
                        console.log('Production data updated:', e);
                        showRealtimeIndicator();
                        fetchKPIData(currentEquipmentId);
                    })
                    .listen('.kpi.updated', (e) => {
                        console.log('KPI updated:', e);
                        showRealtimeIndicator();
                        if (e.equipment_id === currentEquipmentId) {
                            updateDashboard({ oee: e.kpi_data, metrics: {} });
                        }
                    });
            }

            // Refresh data every 30 seconds
            setInterval(() => {
                fetchKPIData(currentEquipmentId);
            }, 30000);
        });
    </script>
</body>
</html>
