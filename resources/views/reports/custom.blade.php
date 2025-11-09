@extends('layouts.report')

@section('title', 'Reportes Personalizados')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Sidebar - Configuration -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <svg class="h-6 w-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Configuración del Reporte
                        </h2>

                        <form id="reportForm" class="space-y-6">
                            @csrf

                            <!-- Equipment Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Equipos
                                    </span>
                                </label>
                                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                                    @foreach($equipment as $eq)
                                        <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                            <input type="checkbox" name="equipment_ids[]" value="{{ $eq->id }}" class="equipment-checkbox w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $eq->name }} <span class="text-xs text-gray-500">({{ $eq->code }})</span></span>
                                        </label>
                                    @endforeach
                                </div>
                                <button type="button" id="selectAllEquipment" class="mt-2 text-xs text-blue-600 hover:text-blue-800">Seleccionar todos</button>
                            </div>

                            <!-- Date Range -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ now()->subDays(7)->format('Y-m-d') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ now()->format('Y-m-d') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" required>
                                </div>
                            </div>

                            <!-- Quick Date Filters -->
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="quick-date px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded transition" data-days="1">Hoy</button>
                                <button type="button" class="quick-date px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded transition" data-days="7">7 días</button>
                                <button type="button" class="quick-date px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded transition" data-days="30">30 días</button>
                                <button type="button" class="quick-date px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded transition" data-days="90">90 días</button>
                            </div>

                            <!-- Metrics Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Métricas a Incluir
                                    </span>
                                </label>
                                <div class="space-y-2">
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-blue-50 cursor-pointer">
                                        <input type="checkbox" name="metrics[]" value="oee" class="metric-checkbox w-4 h-4 text-blue-600 rounded focus:ring-blue-500" checked>
                                        <div class="ml-3 flex-1">
                                            <span class="text-sm font-medium text-gray-700">OEE</span>
                                            <p class="text-xs text-gray-500">Disponibilidad, Rendimiento, Calidad</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-green-50 cursor-pointer">
                                        <input type="checkbox" name="metrics[]" value="production" class="metric-checkbox w-4 h-4 text-blue-600 rounded focus:ring-blue-500" checked>
                                        <div class="ml-3 flex-1">
                                            <span class="text-sm font-medium text-gray-700">Producción</span>
                                            <p class="text-xs text-gray-500">Planificada vs Real, Eficiencia</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-purple-50 cursor-pointer">
                                        <input type="checkbox" name="metrics[]" value="quality" class="metric-checkbox w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                        <div class="ml-3 flex-1">
                                            <span class="text-sm font-medium text-gray-700">Calidad</span>
                                            <p class="text-xs text-gray-500">Aprobadas, Rechazadas, Tasa</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-red-50 cursor-pointer">
                                        <input type="checkbox" name="metrics[]" value="downtime" class="metric-checkbox w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                        <div class="ml-3 flex-1">
                                            <span class="text-sm font-medium text-gray-700">Tiempos Muertos</span>
                                            <p class="text-xs text-gray-500">Planificados, No Planificados</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Generate Button -->
                            <button type="submit" id="generateBtn" class="w-full px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition shadow-md flex items-center justify-center">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Generar Reporte
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Content - Results -->
                <div class="lg:col-span-2">
                    <!-- Empty State -->
                    <div id="emptyState" class="bg-white rounded-lg shadow-md p-12 text-center">
                        <svg class="h-24 w-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Construye tu Reporte Personalizado</h3>
                        <p class="text-gray-500 mb-6">Selecciona equipos, rango de fechas y métricas para generar tu reporte</p>
                        <div class="max-w-md mx-auto text-left">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Pasos para generar:</h4>
                            <ol class="text-sm text-gray-600 space-y-2">
                                <li class="flex items-start">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-600 text-xs font-bold mr-2 flex-shrink-0">1</span>
                                    <span>Selecciona uno o más equipos de la lista</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-600 text-xs font-bold mr-2 flex-shrink-0">2</span>
                                    <span>Define el rango de fechas para el análisis</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-600 text-xs font-bold mr-2 flex-shrink-0">3</span>
                                    <span>Elige las métricas que quieres incluir</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-600 text-xs font-bold mr-2 flex-shrink-0">4</span>
                                    <span>Haz clic en "Generar Reporte"</span>
                                </li>
                            </ol>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="loadingState" class="bg-white rounded-lg shadow-md p-12 text-center hidden">
                        <div class="animate-pulse">
                            <svg class="h-16 w-16 mx-auto text-orange-600 mb-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Generando reporte...</h3>
                            <p class="text-gray-500 mt-2">Por favor espere</p>
                        </div>
                    </div>

                    <!-- Results Container -->
                    <div id="resultsContainer" class="hidden">
                        <!-- Report Header -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Reporte Personalizado</h2>
                                    <p class="text-sm text-gray-600" id="reportPeriod"></p>
                                    <p class="text-xs text-gray-500 mt-1" id="reportGenerated"></p>
                                </div>
                                <div class="flex space-x-2">
                                    <button id="exportPdfBtn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        PDF
                                    </button>
                                    <button id="exportExcelBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        CSV
                                    </button>
                                    <button id="printBtn" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Report Content -->
                        <div id="reportContent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    // CSRF Token Setup
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Select All Equipment
        document.getElementById('selectAllEquipment').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.equipment-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            this.textContent = allChecked ? 'Seleccionar todos' : 'Deseleccionar todos';
        });

        // Quick Date Filters
        document.querySelectorAll('.quick-date').forEach(btn => {
            btn.addEventListener('click', function() {
                const days = parseInt(this.dataset.days);
                const endDate = new Date();
                const startDate = new Date();
                startDate.setDate(startDate.getDate() - days + 1);
                
                document.getElementById('start_date').value = startDate.toISOString().split('T')[0];
                document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
            });
        });

        // Form Submission
        document.getElementById('reportForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate equipment selection
            const selectedEquipment = Array.from(document.querySelectorAll('.equipment-checkbox:checked'));
            if (selectedEquipment.length === 0) {
                alert('Por favor seleccione al menos un equipo');
                return;
            }

            // Validate metrics selection
            const selectedMetrics = Array.from(document.querySelectorAll('.metric-checkbox:checked'));
            if (selectedMetrics.length === 0) {
                alert('Por favor seleccione al menos una métrica');
                return;
            }

            // Show loading state
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('resultsContainer').classList.add('hidden');
            document.getElementById('loadingState').classList.remove('hidden');

            try {
                const formData = new FormData(this);
                const response = await axios.post('{{ route("reports.custom.generate") }}', formData);

                if (response.data.success) {
                    displayReport(response.data);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al generar el reporte: ' + (error.response?.data?.message || error.message));
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('emptyState').classList.remove('hidden');
            }
        });

        function displayReport(data) {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('resultsContainer').classList.remove('hidden');

            // Update header
            document.getElementById('reportPeriod').textContent = `Período: ${data.period.start} - ${data.period.end}`;
            document.getElementById('reportGenerated').textContent = `Generado: ${new Date().toLocaleString('es-ES')}`;

            // Build report content
            let html = '';
            
            data.data.forEach((equipmentData, index) => {
                html += `
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <svg class="h-6 w-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            ${equipmentData.equipment.name} <span class="text-sm font-normal text-gray-500 ml-2">(${equipmentData.equipment.code})</span>
                        </h3>
                `;

                // OEE Section
                if (equipmentData.oee) {
                    html += `
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="w-1 h-6 bg-blue-500 mr-2"></span>
                                Indicadores OEE
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium uppercase">OEE</p>
                                    <p class="text-3xl font-bold text-blue-700">${equipmentData.oee.oee}%</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-green-600 font-medium uppercase">Disponibilidad</p>
                                    <p class="text-3xl font-bold text-green-700">${equipmentData.oee.availability}%</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-orange-600 font-medium uppercase">Rendimiento</p>
                                    <p class="text-3xl font-bold text-orange-700">${equipmentData.oee.performance}%</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-purple-600 font-medium uppercase">Calidad</p>
                                    <p class="text-3xl font-bold text-purple-700">${equipmentData.oee.quality}%</p>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Production Section
                if (equipmentData.production) {
                    html += `
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="w-1 h-6 bg-green-500 mr-2"></span>
                                Métricas de Producción
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">Planificado</p>
                                    <p class="text-2xl font-bold text-gray-800">${equipmentData.production.total_planned.toLocaleString()}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">Producido</p>
                                    <p class="text-2xl font-bold text-green-600">${equipmentData.production.total_actual.toLocaleString()}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">Unidades Buenas</p>
                                    <p class="text-2xl font-bold text-green-600">${equipmentData.production.total_good.toLocaleString()}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">Eficiencia</p>
                                    <p class="text-2xl font-bold text-blue-600">${equipmentData.production.efficiency.toFixed(1)}%</p>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Quality Section
                if (equipmentData.quality) {
                    html += `
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="w-1 h-6 bg-purple-500 mr-2"></span>
                                Métricas de Calidad
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">Inspeccionado</p>
                                    <p class="text-2xl font-bold text-gray-800">${equipmentData.quality.total_inspected.toLocaleString()}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">Aprobadas</p>
                                    <p class="text-2xl font-bold text-green-600">${equipmentData.quality.total_approved.toLocaleString()}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">Rechazadas</p>
                                    <p class="text-2xl font-bold text-red-600">${equipmentData.quality.total_rejected.toLocaleString()}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">Tasa de Calidad</p>
                                    <p class="text-2xl font-bold text-purple-600">${equipmentData.quality.quality_rate.toFixed(1)}%</p>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Downtime Section
                if (equipmentData.downtime) {
                    html += `
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="w-1 h-6 bg-red-500 mr-2"></span>
                                Tiempos Muertos
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">Total Minutos</p>
                                    <p class="text-2xl font-bold text-red-600">${equipmentData.downtime.total_minutes.toLocaleString()}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">Total Horas</p>
                                    <p class="text-2xl font-bold text-red-600">${equipmentData.downtime.total_hours}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">Planificado</p>
                                    <p class="text-2xl font-bold text-orange-600">${equipmentData.downtime.planned.toLocaleString()}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <p class="text-xs text-gray-600 font-medium">No Planificado</p>
                                    <p class="text-2xl font-bold text-red-600">${equipmentData.downtime.unplanned.toLocaleString()}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }

                html += `</div>`;
            });

            document.getElementById('reportContent').innerHTML = html;

            // Store report data for export
            window.currentReportData = data;
        }

        // Export handlers
        document.getElementById('exportPdfBtn')?.addEventListener('click', async function() {
            if (!window.currentReportData) {
                alert('Por favor genera un reporte primero');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('format', 'pdf');
                formData.append('report_data', JSON.stringify(window.currentReportData));

                // Crear un formulario temporal para enviar POST y descargar
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("reports.custom.export") }}';
                form.target = '_blank';

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfInput);

                const formatInput = document.createElement('input');
                formatInput.type = 'hidden';
                formatInput.name = 'format';
                formatInput.value = 'pdf';
                form.appendChild(formatInput);

                const dataInput = document.createElement('input');
                dataInput.type = 'hidden';
                dataInput.name = 'report_data';
                dataInput.value = JSON.stringify(window.currentReportData);
                form.appendChild(dataInput);

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            } catch (error) {
                console.error('Error:', error);
                alert('Error al exportar a PDF: ' + error.message);
            }
        });

        document.getElementById('exportExcelBtn')?.addEventListener('click', async function() {
            if (!window.currentReportData) {
                alert('Por favor genera un reporte primero');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('format', 'csv');
                formData.append('report_data', JSON.stringify(window.currentReportData));

                // Crear un formulario temporal para enviar POST y descargar
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("reports.custom.export") }}';

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfInput);

                const formatInput = document.createElement('input');
                formatInput.type = 'hidden';
                formatInput.name = 'format';
                formatInput.value = 'csv';
                form.appendChild(formatInput);

                const dataInput = document.createElement('input');
                dataInput.type = 'hidden';
                dataInput.name = 'report_data';
                dataInput.value = JSON.stringify(window.currentReportData);
                form.appendChild(dataInput);

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            } catch (error) {
                console.error('Error:', error);
                alert('Error al exportar a CSV: ' + error.message);
            }
        });

    document.getElementById('printBtn')?.addEventListener('click', function() {
        window.print();
    });
</script>

<style>
    @media print {
        header, #reportForm, .no-print {
            display: none !important;
        }
        #resultsContainer {
            display: block !important;
        }
    }
</style>

@endsection
