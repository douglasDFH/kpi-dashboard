<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes y Análisis - Metalúrgica Precision S.A.</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Metalúrgica Precision S.A.</h1>
                        <p class="text-gray-600">Reportes y Análisis</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Centro de Reportes</h2>
                <p class="text-gray-600 mt-1">Seleccione el tipo de reporte que desea generar</p>
            </div>

            <!-- Reports Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- OEE Report Card -->
                <a href="{{ route('reports.oee') }}" class="bg-white rounded-lg shadow-md hover:shadow-xl transition p-6 group">
                    <div class="flex items-start">
                        <div class="p-3 rounded-lg bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition">Reporte OEE</h3>
                            <p class="text-sm text-gray-600 mt-1">Eficiencia general de equipos por período</p>
                            <ul class="mt-3 text-xs text-gray-500 space-y-1">
                                <li>• Disponibilidad</li>
                                <li>• Rendimiento</li>
                                <li>• Calidad</li>
                            </ul>
                        </div>
                    </div>
                </a>

                <!-- Production Report Card -->
                <a href="{{ route('reports.production') }}" class="bg-white rounded-lg shadow-md hover:shadow-xl transition p-6 group">
                    <div class="flex items-start">
                        <div class="p-3 rounded-lg bg-green-100 text-green-600 group-hover:bg-green-600 group-hover:text-white transition">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition">Reporte de Producción</h3>
                            <p class="text-sm text-gray-600 mt-1">Análisis detallado de producción</p>
                            <ul class="mt-3 text-xs text-gray-500 space-y-1">
                                <li>• Producción planificada vs real</li>
                                <li>• Unidades defectuosas</li>
                                <li>• Eficiencia por equipo</li>
                            </ul>
                        </div>
                    </div>
                </a>

                <!-- Quality Report Card -->
                <a href="{{ route('reports.quality') }}" class="bg-white rounded-lg shadow-md hover:shadow-xl transition p-6 group">
                    <div class="flex items-start">
                        <div class="p-3 rounded-lg bg-purple-100 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition">Reporte de Calidad</h3>
                            <p class="text-sm text-gray-600 mt-1">Inspecciones y defectos</p>
                            <ul class="mt-3 text-xs text-gray-500 space-y-1">
                                <li>• Tasa de aprobación</li>
                                <li>• Defectos por tipo</li>
                                <li>• Tendencias de calidad</li>
                            </ul>
                        </div>
                    </div>
                </a>

                <!-- Downtime Report Card -->
                <a href="{{ route('reports.downtime') }}" class="bg-white rounded-lg shadow-md hover:shadow-xl transition p-6 group">
                    <div class="flex items-start">
                        <div class="p-3 rounded-lg bg-red-100 text-red-600 group-hover:bg-red-600 group-hover:text-white transition">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-red-600 transition">Reporte de Tiempos Muertos</h3>
                            <p class="text-sm text-gray-600 mt-1">Análisis de paros e inactividad</p>
                            <ul class="mt-3 text-xs text-gray-500 space-y-1">
                                <li>• Planificados vs no planificados</li>
                                <li>• Razones principales</li>
                                <li>• Impacto en disponibilidad</li>
                            </ul>
                        </div>
                    </div>
                </a>

                <!-- Comparative Report Card -->
                <a href="{{ route('reports.comparative') }}" class="bg-white rounded-lg shadow-md hover:shadow-xl transition p-6 group">
                    <div class="flex items-start">
                        <div class="p-3 rounded-lg bg-orange-100 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-orange-600 transition">Reporte Comparativo</h3>
                            <p class="text-sm text-gray-600 mt-1">Comparación entre equipos</p>
                            <ul class="mt-3 text-xs text-gray-500 space-y-1">
                                <li>• OEE por equipo</li>
                                <li>• Ranking de desempeño</li>
                                <li>• Identificar mejores prácticas</li>
                            </ul>
                        </div>
                    </div>
                </a>

                <!-- Custom Report Card -->
                <a href="{{ route('reports.custom') }}" class="bg-white rounded-lg shadow-md hover:shadow-xl transition p-6 group border-2 border-indigo-200">
                    <div class="flex items-start">
                        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-indigo-600 transition">Reportes Personalizados</h3>
                            <p class="text-sm text-gray-600 mt-1">Constructor avanzado de reportes</p>
                            <ul class="mt-3 text-xs text-gray-500 space-y-1">
                                <li>• Filtros avanzados y métricas</li>
                                <li>• Múltiples equipos y períodos</li>
                                <li>• Exportación a PDF/Excel</li>
                            </ul>
                            <span class="inline-block mt-2 px-2 py-1 bg-indigo-100 text-indigo-600 text-xs font-semibold rounded">¡Nuevo!</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Info Box -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex">
                    <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">Acerca de los Reportes</h4>
                        <ul class="mt-2 text-sm text-blue-700 space-y-1">
                            <li>• Todos los reportes pueden filtrarse por rango de fechas y equipos</li>
                            <li>• Los datos se actualizan en tiempo real</li>
                            <li>• Use los gráficos interactivos para análisis visual</li>
                            <li>• Exporte los reportes para presentaciones (próximamente)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
