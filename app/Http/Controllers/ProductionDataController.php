<?php

namespace App\Http\Controllers;

use App\Models\ProductionData;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductionDataController extends Controller
{
    /**
     * Display a listing of the production data.
     */
    public function index(Request $request)
    {
        $query = ProductionData::with('equipment');

        // Filter by equipment
        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('production_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('production_date', '<=', $request->end_date);
        }

        $productionData = $query->orderBy('production_date', 'desc')->paginate(15);
        $equipment = Equipment::where('is_active', true)->get();

        return view('production.index', compact('productionData', 'equipment'));
    }

    /**
     * Show the form for creating new production data.
     */
    public function create()
    {
        $equipment = Equipment::where('is_active', true)->get();
        return view('production.create', compact('equipment'));
    }

    /**
     * Store a newly created production data in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'planned_production' => 'required|integer|min:1',
            'actual_production' => 'required|integer|min:0',
            'good_units' => 'required|integer|min:0',
            'defective_units' => 'required|integer|min:0',
            'cycle_time' => 'required|numeric|min:0.1',
            'production_date' => 'required|date',
        ]);

        // Validar que good_units + defective_units = actual_production
        if (($validated['good_units'] + $validated['defective_units']) != $validated['actual_production']) {
            return back()->withErrors([
                'actual_production' => 'La suma de unidades buenas y defectuosas debe ser igual a la producción real.'
            ])->withInput();
        }

        ProductionData::create($validated);

        return redirect()->route('production.index')
            ->with('success', 'Datos de producción registrados exitosamente.');
    }

    /**
     * Show the form for editing the specified production data.
     */
    public function edit(ProductionData $production)
    {
        $equipment = Equipment::where('is_active', true)->get();
        return view('production.edit', compact('production', 'equipment'));
    }

    /**
     * Update the specified production data in storage.
     */
    public function update(Request $request, ProductionData $production)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'planned_production' => 'required|integer|min:1',
            'actual_production' => 'required|integer|min:0',
            'good_units' => 'required|integer|min:0',
            'defective_units' => 'required|integer|min:0',
            'cycle_time' => 'required|numeric|min:0.1',
            'production_date' => 'required|date',
        ]);

        // Validar que good_units + defective_units = actual_production
        if (($validated['good_units'] + $validated['defective_units']) != $validated['actual_production']) {
            return back()->withErrors([
                'actual_production' => 'La suma de unidades buenas y defectuosas debe ser igual a la producción real.'
            ])->withInput();
        }

        $production->update($validated);

        return redirect()->route('production.index')
            ->with('success', 'Datos de producción actualizados exitosamente.');
    }

    /**
     * Remove the specified production data from storage.
     */
    public function destroy(ProductionData $production)
    {
        $production->delete();

        return redirect()->route('production.index')
            ->with('success', 'Registro de producción eliminado exitosamente.');
    }
}
