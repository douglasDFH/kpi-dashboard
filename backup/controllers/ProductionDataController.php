<?php

namespace App\Http\Controllers;

use App\Models\ProductionData;
use App\Models\Equipment;
use App\Events\ProductionDataUpdated;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Traits\AuthorizesPermissions;

class ProductionDataController extends Controller
{
    use AuthorizesPermissions;

    /**
     * Display a listing of the production data.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('production.view', 'No tienes permiso para ver datos de producción.');

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

        $productionData = $query->orderBy('production_date', 'desc')->paginate(15)->appends($request->except('page'));
        $equipment = Equipment::where('is_active', true)->get();

        return view('production.index', compact('productionData', 'equipment'));
    }

    /**
     * Show the form for creating new production data.
     */
    public function create()
    {
        $this->authorizePermission('production.create', 'No tienes permiso para crear datos de producción.');

        $equipment = Equipment::where('is_active', true)->get();
        return view('production.create', compact('equipment'));
    }

    /**
     * Store a newly created production data in storage.
     */
    public function store(Request $request)
    {
        $this->authorizePermission('production.create', 'No tienes permiso para crear datos de producción.');

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

        $productionData = ProductionData::create($validated);

        // Disparar evento para actualizar dashboard en tiempo real
        ProductionDataUpdated::dispatch($productionData);

        return redirect()->route('production.index')
            ->with('success', 'Datos de producción registrados exitosamente.');
    }

    /**
     * Show the form for editing the specified production data.
     */
    public function edit(ProductionData $production)
    {
        $this->authorizePermission('production.edit', 'No tienes permiso para editar datos de producción.');

        $equipment = Equipment::where('is_active', true)->get();
        return view('production.edit', compact('production', 'equipment'));
    }

    /**
     * Update the specified production data in storage.
     */
    public function update(Request $request, ProductionData $production)
    {
        $this->authorizePermission('production.edit', 'No tienes permiso para actualizar datos de producción.');

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

        // Disparar evento para actualizar dashboard en tiempo real
        ProductionDataUpdated::dispatch($production);

        return redirect()->route('production.index')
            ->with('success', 'Datos de producción actualizados exitosamente.');
    }

    /**
     * Remove the specified production data from storage.
     */
    public function destroy(ProductionData $production)
    {
        $this->authorizePermission('production.delete', 'No tienes permiso para eliminar datos de producción.');

        $production->delete();

        return redirect()->route('production.index')
            ->with('success', 'Registro de producción eliminado exitosamente.');
    }
}
