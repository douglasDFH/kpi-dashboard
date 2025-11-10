<?php

namespace App\Http\Controllers;

use App\Models\QualityData;
use App\Models\Equipment;
use App\Events\ProductionDataUpdated;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\AuthorizesPermissions;

class QualityDataController extends Controller
{
    use AuthorizesPermissions;

    /**
     * Display a listing of the quality inspections.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('quality.view', 'No tienes permiso para ver datos de calidad.');
        
        $query = QualityData::with('equipment')->orderBy('inspection_date', 'desc');

        // Filtro por equipo
        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        // Filtro por tipo de defecto
        if ($request->filled('defect_type')) {
            $query->where('defect_type', $request->defect_type);
        }

        // Filtro por rango de fechas
        if ($request->filled('start_date')) {
            $query->whereDate('inspection_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('inspection_date', '<=', $request->end_date);
        }

        $qualityData = $query->paginate(15)->appends($request->except('page'));
        $equipment = Equipment::where('is_active', true)->get();

        // Tipos de defectos disponibles
        $defectTypes = QualityData::select('defect_type')
            ->whereNotNull('defect_type')
            ->distinct()
            ->pluck('defect_type')
            ->toArray();

        return view('quality.index', compact('qualityData', 'equipment', 'defectTypes'));
    }

    /**
     * Show the form for creating a new quality inspection.
     */
    public function create()
    {
        $this->authorizePermission('quality.create', 'No tienes permiso para crear datos de calidad.');

        $equipment = Equipment::where('is_active', true)->get();
        return view('quality.create', compact('equipment'));
    }

    /**
     * Store a newly created quality inspection in storage.
     */
    public function store(Request $request)
    {
        $this->authorizePermission('quality.create', 'No tienes permiso para crear datos de calidad.');

        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'total_inspected' => 'required|integer|min:1',
            'approved_units' => 'required|integer|min:0',
            'rejected_units' => 'required|integer|min:0',
            'defect_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'inspection_date' => 'required|date',
        ]);

        // Validar que aprobadas + rechazadas = total
        if (($validated['approved_units'] + $validated['rejected_units']) != $validated['total_inspected']) {
            return back()->withErrors([
                'total_inspected' => 'La suma de unidades aprobadas y rechazadas debe ser igual al total inspeccionado.'
            ])->withInput();
        }

        $qualityData = QualityData::create($validated);

        // Disparar evento para actualizar dashboard en tiempo real
        ProductionDataUpdated::dispatch($qualityData);

        return redirect()->route('quality.index')
            ->with('success', 'Inspección de calidad registrada exitosamente.');
    }

    /**
     * Display the specified quality inspection.
     */
    public function show(QualityData $quality)
    {
        $quality->load('equipment');
        return view('quality.show', compact('quality'));
    }

    /**
     * Show the form for editing the specified quality inspection.
     */
    public function edit(QualityData $quality)
    {
        $this->authorizePermission('quality.edit', 'No tienes permiso para editar datos de calidad.');

        $equipment = Equipment::where('is_active', true)->get();
        return view('quality.edit', compact('quality', 'equipment'));
    }

    /**
     * Update the specified quality inspection in storage.
     */
    public function update(Request $request, QualityData $quality)
    {
        $this->authorizePermission('quality.edit', 'No tienes permiso para actualizar datos de calidad.');
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'total_inspected' => 'required|integer|min:1',
            'approved_units' => 'required|integer|min:0',
            'rejected_units' => 'required|integer|min:0',
            'defect_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'inspection_date' => 'required|date',
        ]);

        // Validar que aprobadas + rechazadas = total
        if (($validated['approved_units'] + $validated['rejected_units']) != $validated['total_inspected']) {
            return back()->withErrors([
                'total_inspected' => 'La suma de unidades aprobadas y rechazadas debe ser igual al total inspeccionado.'
            ])->withInput();
        }

        $quality->update($validated);

        // Disparar evento para actualizar dashboard en tiempo real
        ProductionDataUpdated::dispatch($quality);

        return redirect()->route('quality.index')
            ->with('success', 'Inspección de calidad actualizada exitosamente.');
    }

    /**
     * Remove the specified quality inspection from storage.
     */
    public function destroy(QualityData $quality)
    {
        $this->authorizePermission('quality.delete', 'No tienes permiso para eliminar datos de calidad.');
        $quality->delete();

        return redirect()->route('quality.index')
            ->with('success', 'Inspección de calidad eliminada exitosamente.');
    }
}
