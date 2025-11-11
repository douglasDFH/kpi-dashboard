@extends('components.layouts.app')

@section('title', 'Nueva Máquina')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Nueva Máquina</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.maquinas.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                    <input type="text" name="nombre" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ old('nombre') }}">
                    @error('nombre')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código *</label>
                    <input type="text" name="codigo" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ old('codigo') }}">
                    @error('codigo')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Área *</label>
                <select name="area_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">-- Selecciona área --</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" @selected(old('area_id') === $area->id)>{{ $area->nombre }}</option>
                    @endforeach
                </select>
                @error('area_id')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fabricante</label>
                    <input type="text" name="fabricante" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ old('fabricante') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modelo</label>
                    <input type="text" name="modelo" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ old('modelo') }}">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('descripcion') }}</textarea>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">Crear Máquina</button>
                <a href="{{ route('admin.maquinas.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
