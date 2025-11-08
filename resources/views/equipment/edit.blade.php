<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Equipo - Metalúrgica Precision S.A.</title>
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
                        <p class="text-gray-600">Editar Equipo Industrial</p>
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
                        <h2 class="text-2xl font-bold text-gray-800">Editar Equipo: {{ $equipment->code }}</h2>
                        <p class="text-gray-600 mt-1">Actualice los datos del equipo industrial</p>
                    </div>

                    <form action="{{ route('equipment.update', $equipment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Equipment Name -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Equipo <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $equipment->name) }}"
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
                                value="{{ old('code', $equipment->code) }}"
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
                                <option value="Prensa" {{ old('type', $equipment->type) == 'Prensa' ? 'selected' : '' }}>Prensa Hidráulica</option>
                                <option value="Torno" {{ old('type', $equipment->type) == 'Torno' ? 'selected' : '' }}>Torno CNC</option>
                                <option value="Fresadora" {{ old('type', $equipment->type) == 'Fresadora' ? 'selected' : '' }}>Fresadora Industrial</option>
                                <option value="Línea de Ensamblaje" {{ old('type', $equipment->type) == 'Línea de Ensamblaje' ? 'selected' : '' }}>Línea de Ensamblaje</option>
                                <option value="Rectificadora" {{ old('type', $equipment->type) == 'Rectificadora' ? 'selected' : '' }}>Rectificadora</option>
                                <option value="Taladro" {{ old('type', $equipment->type) == 'Taladro' ? 'selected' : '' }}>Taladro Industrial</option>
                                <option value="Soldadora" {{ old('type', $equipment->type) == 'Soldadora' ? 'selected' : '' }}>Máquina de Soldar</option>
                                <option value="Otro" {{ old('type', $equipment->type) == 'Otro' ? 'selected' : '' }}>Otro</option>
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
                                <option value="Área de Producción A" {{ old('location', $equipment->location) == 'Área de Producción A' ? 'selected' : '' }}>Área de Producción A</option>
                                <option value="Área de Producción B" {{ old('location', $equipment->location) == 'Área de Producción B' ? 'selected' : '' }}>Área de Producción B</option>
                                <option value="Área de Producción C" {{ old('location', $equipment->location) == 'Área de Producción C' ? 'selected' : '' }}>Área de Producción C</option>
                                <option value="Área de Ensamblaje" {{ old('location', $equipment->location) == 'Área de Ensamblaje' ? 'selected' : '' }}>Área de Ensamblaje</option>
                                <option value="Área de Mecanizado" {{ old('location', $equipment->location) == 'Área de Mecanizado' ? 'selected' : '' }}>Área de Mecanizado</option>
                                <option value="Área de Acabado" {{ old('location', $equipment->location) == 'Área de Acabado' ? 'selected' : '' }}>Área de Acabado</option>
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
                                    {{ old('is_active', $equipment->is_active) ? 'checked' : '' }}
                                    class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <span class="ml-2 text-sm font-medium text-gray-700">
                                    Equipo activo
                                </span>
                            </label>
                            <p class="mt-1 ml-7 text-xs text-gray-500">Marque si el equipo está operativo</p>
                        </div>

                        <!-- Equipment Stats -->
                        <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Estadísticas del Equipo</h3>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p class="text-2xl font-bold text-blue-600">{{ $equipment->productionData()->count() }}</p>
                                    <p class="text-xs text-gray-600">Registros de Producción</p>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-green-600">{{ $equipment->qualityData()->count() }}</p>
                                    <p class="text-xs text-gray-600">Inspecciones de Calidad</p>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-red-600">{{ $equipment->downtimeData()->count() }}</p>
                                    <p class="text-xs text-gray-600">Eventos de Downtime</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('equipment.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-md">
                                Actualizar Equipo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
