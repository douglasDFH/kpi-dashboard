<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the equipment.
     */
    public function index()
    {
        $equipment = Equipment::orderBy('created_at', 'desc')->get();
        return view('equipment.index', compact('equipment'));
    }

    /**
     * Show the form for creating a new equipment.
     */
    public function create()
    {
        return view('equipment.create');
    }

    /**
     * Store a newly created equipment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:equipment,code',
            'type' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        Equipment::create($validated);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipo creado exitosamente.');
    }

    /**
     * Show the form for editing the specified equipment.
     */
    public function edit(Equipment $equipment)
    {
        return view('equipment.edit', compact('equipment'));
    }

    /**
     * Update the specified equipment in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:equipment,code,' . $equipment->id,
            'type' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $equipment->update($validated);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipo actualizado exitosamente.');
    }

    /**
     * Remove the specified equipment from storage.
     */
    public function destroy(Equipment $equipment)
    {
        // Verificar si el equipo tiene datos asociados
        $hasProductionData = $equipment->productionData()->exists();
        $hasQualityData = $equipment->qualityData()->exists();
        $hasDowntimeData = $equipment->downtimeData()->exists();

        if ($hasProductionData || $hasQualityData || $hasDowntimeData) {
            return redirect()->route('equipment.index')
                ->with('error', 'No se puede eliminar el equipo porque tiene datos asociados. Considere desactivarlo en su lugar.');
        }

        $equipment->delete();

        return redirect()->route('equipment.index')
            ->with('success', 'Equipo eliminado exitosamente.');
    }
}
