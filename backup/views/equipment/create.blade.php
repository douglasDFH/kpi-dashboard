<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Equipo - Metalúrgica Precision S.A.</title>
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
                        <p class="text-gray-600">Nuevo Equipo Industrial</p>
                    </div>
                    <a href="{{ route('equipment.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Registrar Nuevo Equipo</h2>
                        <p class="text-gray-600 mt-1">Complete los datos del equipo industrial</p>
                    </div>

                    <form action="{{ route('equipment.store') }}" method="POST">
                        @csrf

                        <!-- Equipment Name -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Equipo <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                placeholder="Ej: Prensa Hidráulica 1"
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Nombre descriptivo del equipo</p>
                        </div>

                        <!-- Equipment Code -->
                        <div class="mb-6">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                Código del Equipo <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="code"
                                name="code"
                                value="{{ old('code') }}"
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror"
                                placeholder="Ej: PH-001"
                            >
                            @error('code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Código único de identificación (Ej: PH-001, TC-002)</p>
                        </div>

                        <!-- Equipment Type -->
                        <div class="mb-6">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Equipo <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="type"
                                name="type"
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror"
                            >
                                <option value="">Seleccione un tipo</option>
                                <option value="Prensa" {{ old('type') == 'Prensa' ? 'selected' : '' }}>Prensa Hidráulica</option>
                                <option value="Torno" {{ old('type') == 'Torno' ? 'selected' : '' }}>Torno CNC</option>
                                <option value="Fresadora" {{ old('type') == 'Fresadora' ? 'selected' : '' }}>Fresadora Industrial</option>
                                <option value="Línea de Ensamblaje" {{ old('type') == 'Línea de Ensamblaje' ? 'selected' : '' }}>Línea de Ensamblaje</option>
                                <option value="Rectificadora" {{ old('type') == 'Rectificadora' ? 'selected' : '' }}>Rectificadora</option>
                                <option value="Taladro" {{ old('type') == 'Taladro' ? 'selected' : '' }}>Taladro Industrial</option>
                                <option value="Soldadora" {{ old('type') == 'Soldadora' ? 'selected' : '' }}>Máquina de Soldar</option>
                                <option value="Otro" {{ old('type') == 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Equipment Location -->
                        <div class="mb-6">
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Ubicación <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="location"
                                name="location"
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                            >
                                <option value="">Seleccione una ubicación</option>
                                <option value="Área de Producción A" {{ old('location') == 'Área de Producción A' ? 'selected' : '' }}>Área de Producción A</option>
                                <option value="Área de Producción B" {{ old('location') == 'Área de Producción B' ? 'selected' : '' }}>Área de Producción B</option>
                                <option value="Área de Producción C" {{ old('location') == 'Área de Producción C' ? 'selected' : '' }}>Área de Producción C</option>
                                <option value="Área de Ensamblaje" {{ old('location') == 'Área de Ensamblaje' ? 'selected' : '' }}>Área de Ensamblaje</option>
                                <option value="Área de Mecanizado" {{ old('location') == 'Área de Mecanizado' ? 'selected' : '' }}>Área de Mecanizado</option>
                                <option value="Área de Acabado" {{ old('location') == 'Área de Acabado' ? 'selected' : '' }}>Área de Acabado</option>
                            </select>
                            @error('location')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Equipment Status -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                    class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <span class="ml-2 text-sm font-medium text-gray-700">
                                    Equipo activo
                                </span>
                            </label>
                            <p class="mt-1 ml-7 text-xs text-gray-500">Marque si el equipo está operativo</p>
                        </div>

                        <!-- Info Box -->
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium">Metalúrgica Precision S.A.</p>
                                    <p class="mt-1">Este equipo será utilizado para la producción de componentes mecanizados para la industria automotriz.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('equipment.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-md">
                                Guardar Equipo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
