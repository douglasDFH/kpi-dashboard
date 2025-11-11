<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaController extends Controller
{
    /**
     * Display a listing of areas
     */
    public function index(): View
    {
        $areas = Area::withTrashed()
            ->withCount('maquinas')
            ->orderBy('nombre')
            ->paginate(20);

        return view('admin.areas.index', [
            'areas' => $areas,
        ]);
    }

    /**
     * Show the form for creating a new area
     */
    public function create(): View
    {
        return view('admin.areas.create');
    }

    /**
     * Store a newly created area
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:areas',
            'codigo' => 'nullable|string|max:50|unique:areas',
            'descripcion' => 'nullable|string|max:500',
            'gerente_responsable' => 'nullable|string|max:100',
        ]);

        try {
            $area = Area::create($validated);

            return redirect()
                ->route('admin.areas.show', $area)
                ->with('success', 'Área creada exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified area
     */
    public function show(Area $area): View
    {
        $area->load('maquinas');

        return view('admin.areas.show', [
            'area' => $area,
        ]);
    }

    /**
     * Show the form for editing area
     */
    public function edit(Area $area): View
    {
        return view('admin.areas.edit', [
            'area' => $area,
        ]);
    }

    /**
     * Update the specified area
     */
    public function update(Request $request, Area $area): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:areas,nombre,' . $area->id,
            'codigo' => 'nullable|string|max:50|unique:areas,codigo,' . $area->id,
            'descripcion' => 'nullable|string|max:500',
            'gerente_responsable' => 'nullable|string|max:100',
        ]);

        try {
            $area->update($validated);

            return redirect()
                ->route('admin.areas.show', $area)
                ->with('success', 'Área actualizada exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Soft delete the specified area
     */
    public function destroy(Area $area): RedirectResponse
    {
        if ($area->maquinas()->count() > 0) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'No se puede eliminar un área que contiene máquinas']);
        }

        $area->delete();

        return redirect()
            ->route('admin.areas.index')
            ->with('success', 'Área eliminada');
    }

    /**
     * Restore a soft-deleted area
     */
    public function restore(string $id): RedirectResponse
    {
        $area = Area::withTrashed()->findOrFail($id);
        $area->restore();

        return redirect()
            ->route('admin.areas.index')
            ->with('success', 'Área restaurada');
    }

    /**
     * Force delete an area permanently
     */
    public function forceDelete(string $id): RedirectResponse
    {
        $area = Area::withTrashed()->findOrFail($id);
        $area->forceDelete();

        return redirect()
            ->route('admin.areas.index')
            ->with('success', 'Área eliminada permanentemente');
    }
}
