<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maquina;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaquinaController extends Controller
{
    /**
     * Display a listing of machines
     */
    public function index(): View
    {
        $maquinas = Maquina::with('area', 'planesMaquina')
            ->withTrashed()
            ->orderBy('nombre')
            ->paginate(20);

        return view('admin.maquinas.index', [
            'maquinas' => $maquinas,
        ]);
    }

    /**
     * Show the form for creating a new machine
     */
    public function create(): View
    {
        $areas = Area::active()->get();

        return view('admin.maquinas.create', [
            'areas' => $areas,
        ]);
    }

    /**
     * Store a newly created machine
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:maquinas',
            'codigo' => 'required|string|max:50|unique:maquinas',
            'area_id' => 'required|uuid|exists:areas,id',
            'fabricante' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:500',
        ]);

        try {
            $maquina = Maquina::create($validated);

            return redirect()
                ->route('admin.maquinas.show', $maquina)
                ->with('success', 'Máquina creada exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified machine
     */
    public function show(Maquina $maquina): View
    {
        $maquina->load('area', 'planesMaquina', 'jornadas', 'registrosMantenimiento');

        return view('admin.maquinas.show', [
            'maquina' => $maquina,
        ]);
    }

    /**
     * Show the form for editing machine
     */
    public function edit(Maquina $maquina): View
    {
        $areas = Area::active()->get();

        return view('admin.maquinas.edit', [
            'maquina' => $maquina,
            'areas' => $areas,
        ]);
    }

    /**
     * Update the specified machine
     */
    public function update(Request $request, Maquina $maquina): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:maquinas,nombre,' . $maquina->id,
            'codigo' => 'required|string|max:50|unique:maquinas,codigo,' . $maquina->id,
            'area_id' => 'required|uuid|exists:areas,id',
            'fabricante' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'required|in:activa,mantenimiento,inactiva',
        ]);

        try {
            $maquina->update($validated);

            return redirect()
                ->route('admin.maquinas.show', $maquina)
                ->with('success', 'Máquina actualizada exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Soft delete the specified machine
     */
    public function destroy(Maquina $maquina): RedirectResponse
    {
        $maquina->delete();

        return redirect()
            ->route('admin.maquinas.index')
            ->with('success', 'Máquina eliminada');
    }

    /**
     * Restore a soft-deleted machine
     */
    public function restore(string $id): RedirectResponse
    {
        $maquina = Maquina::withTrashed()->findOrFail($id);
        $maquina->restore();

        return redirect()
            ->route('admin.maquinas.index')
            ->with('success', 'Máquina restaurada');
    }

    /**
     * Force delete a machine permanently
     */
    public function forceDelete(string $id): RedirectResponse
    {
        $maquina = Maquina::withTrashed()->findOrFail($id);
        $maquina->forceDelete();

        return redirect()
            ->route('admin.maquinas.index')
            ->with('success', 'Máquina eliminada permanentemente');
    }
}
