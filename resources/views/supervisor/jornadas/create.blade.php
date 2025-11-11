@extends('components.layouts.app')

@section('title', 'Nueva Jornada')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Iniciar Nueva Jornada</h1>
        <p class="text-gray-600 mt-2">Selecciona una máquina para comenzar la producción</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('supervisor.jornadas.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="maquina_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Máquina
                </label>
                <select id="maquina_id" name="maquina_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('maquina_id') border-red-500 @enderror" required>
                    <option value="">-- Selecciona una máquina --</option>
                    @foreach($maquinas as $maquina)
                        <option value="{{ $maquina->id }}" @selected(old('maquina_id') === $maquina->id)>
                            {{ $maquina->nombre }} ({{ $maquina->codigo }})
                        </option>
                    @endforeach
                </select>
                @error('maquina_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Información de la Jornada</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>✓ Se iniciará con estado "En Ejecución"</li>
                    <li>✓ La fecha y hora actual serán registradas</li>
                    <li>✓ Podrás registrar producción durante la jornada</li>
                    <li>✓ Puedes pausar la máquina cuando sea necesario</li>
                </ul>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">
                    Iniciar Jornada
                </button>
                <a href="{{ route('supervisor.jornadas.index') }}" class="btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
