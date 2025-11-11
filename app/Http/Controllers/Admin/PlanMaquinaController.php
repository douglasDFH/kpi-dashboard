<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanMaquina;
use App\Models\Maquina;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanMaquinaController extends Controller
{
    /**
     * Display a listing of production plans
     */
    public function index(): View
    {
        $planes = PlanMaquina::with('maquina')
            ->withTrashed()
            ->orderBy('nombre', 'desc')
            ->paginate(20);

        return view('admin.planes.index', [
            'planes' => $planes,
        ]);
    }

    /**
     * Show the form for creating a new plan
     */
    public function create(): View
    {
        $maquinas = Maquina::active()->get();

        return view('admin.planes.create', [
            'maquinas' => $maquinas,
        ]);
    }

    /**
     * Store a newly created plan
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'maquina_id' => 'required|uuid|exists:maquinas,id',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'tiempo_ciclo_ideal_segundos' => 'required|integer|min:1|max:3600',
            'objetivo_produccion_diaria' => 'required|integer|min:1|max:999999',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
        ]);

        try {
            $plan = PlanMaquina::create($validated);

            return redirect()
                ->route('admin.planes.show', $plan)
                ->with('success', 'Plan de producción creado exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified plan
     */
    public function show(PlanMaquina $plane): View
    {
        $plane->load('maquina', 'jornadasProduccion');

        return view('admin.planes.show', [
            'plan' => $plane,
        ]);
    }

    /**
     * Show the form for editing plan
     */
    public function edit(PlanMaquina $plane): View
    {
        $maquinas = Maquina::active()->get();

        return view('admin.planes.edit', [
            'plan' => $plane,
            'maquinas' => $maquinas,
        ]);
    }

    /**
     * Update the specified plan
     */
    public function update(Request $request, PlanMaquina $plane): RedirectResponse
    {
        $validated = $request->validate([
            'maquina_id' => 'required|uuid|exists:maquinas,id',
            'nombre_plan' => 'required|string|max:100',
            'objetivo_unidades' => 'required|integer|min:1|max:999999',
            'ideal_cycle_time_seconds' => 'required|numeric|min:1|max:3600',
            'unidad_medida' => 'nullable|string|max:50',
            'limite_fallos_critico' => 'nullable|integer|min:0',
            'activo' => 'nullable|boolean',
        ]);

        try {
            $plane->update($validated);

            return redirect()
                ->route('admin.planes.show', $plane)
                ->with('success', 'Plan actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Soft delete the specified plan
     */
    public function destroy(PlanMaquina $plane): RedirectResponse
    {
        $plane->delete();

        return redirect()
            ->route('admin.planes.index')
            ->with('success', 'Plan eliminado');
    }

    /**
     * Restore a soft-deleted plan
     */
    public function restore(string $id): RedirectResponse
    {
        $plan = PlanMaquina::withTrashed()->findOrFail($id);
        $plan->restore();

        return redirect()
            ->route('admin.planes.index')
            ->with('success', 'Plan restaurado');
    }

    /**
     * Force delete a plan permanently
     */
    public function forceDelete(string $id): RedirectResponse
    {
        $plan = PlanMaquina::withTrashed()->findOrFail($id);
        $plan->forceDelete();

        return redirect()
            ->route('admin.planes.index')
            ->with('success', 'Plan eliminado permanentemente');
    }
}
