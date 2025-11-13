<?php

namespace App\Http\Controllers;

use App\Models\ProductionPlan;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductionPlan::with(['equipment', 'creator']);

        // Filtrar por equipo si se especifica
        if ($request->has('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar por turno
        if ($request->has('shift')) {
            $query->where('shift', $request->shift);
        }

        $plans = $query->latest()->paginate(15);
        $equipment = Equipment::where('is_active', true)->get();

        return view('production-plans.index', compact('plans', 'equipment'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $equipment = Equipment::where('is_active', true)->get();
        return view('production-plans.create', compact('equipment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'product_name' => 'required|string|max:255',
            'product_code' => 'nullable|string|max:100',
            'target_quantity' => 'required|integer|min:1',
            'shift' => 'required|in:morning,afternoon,night',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';

        $plan = ProductionPlan::create($validated);

        return redirect()->route('production-plans.index')
            ->with('success', 'Plan de producción creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductionPlan $productionPlan)
    {
        $productionPlan->load(['equipment', 'creator', 'workShifts.operator']);
        $plan = $productionPlan; // Alias for view compatibility
        return view('production-plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductionPlan $productionPlan)
    {
        // Solo se pueden editar planes pending
        if ($productionPlan->status !== 'pending') {
            return redirect()->route('production-plans.index')
                ->with('error', 'Solo se pueden editar planes pendientes.');
        }

        $equipment = Equipment::where('is_active', true)->get();
        return view('production-plans.edit', compact('productionPlan', 'equipment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductionPlan $productionPlan)
    {
        // Solo se pueden editar planes pending
        if ($productionPlan->status !== 'pending') {
            return redirect()->route('production-plans.index')
                ->with('error', 'Solo se pueden editar planes pendientes.');
        }

        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'product_name' => 'required|string|max:255',
            'product_code' => 'nullable|string|max:100',
            'target_quantity' => 'required|integer|min:1',
            'shift' => 'required|in:morning,afternoon,night',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $productionPlan->update($validated);

        return redirect()->route('production-plans.index')
            ->with('success', 'Plan de producción actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductionPlan $productionPlan)
    {
        // Solo se pueden eliminar planes pending
        if ($productionPlan->status !== 'pending') {
            return redirect()->route('production-plans.index')
                ->with('error', 'Solo se pueden eliminar planes pendientes.');
        }

        $productionPlan->delete();

        return redirect()->route('production-plans.index')
            ->with('success', 'Plan de producción eliminado exitosamente.');
    }

    /**
     * Activar un plan
     */
    public function activate(ProductionPlan $productionPlan)
    {
        if ($productionPlan->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Solo se pueden activar planes pendientes.');
        }

        $productionPlan->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'Plan de producción activado.');
    }

    /**
     * Completar un plan
     */
    public function complete(ProductionPlan $productionPlan)
    {
        if ($productionPlan->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Solo se pueden completar planes activos.');
        }

        $productionPlan->complete();

        return redirect()->back()
            ->with('success', 'Plan de producción completado.');
    }

    /**
     * Cancelar un plan
     */
    public function cancel(ProductionPlan $productionPlan)
    {
        if (in_array($productionPlan->status, ['completed', 'cancelled'])) {
            return redirect()->back()
                ->with('error', 'No se puede cancelar este plan.');
        }

        $productionPlan->cancel();

        return redirect()->back()
            ->with('success', 'Plan de producción cancelado.');
    }
}

