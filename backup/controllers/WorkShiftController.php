<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\ProductionPlan;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WorkShift::with(['equipment', 'plan', 'operator']);

        // Filtrar por equipo
        if ($request->has('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar por turno
        if ($request->has('shift_type')) {
            $query->where('shift_type', $request->shift_type);
        }

        $shifts = $query->latest('start_time')->paginate(15);
        $equipment = Equipment::where('is_active', true)->get();

        return view('work-shifts.index', compact('shifts', 'equipment'));
    }

    /**
     * Show the form for creating a new resource (iniciar jornada).
     */
    public function create()
    {
        $equipment = Equipment::where('is_active', true)->get();
        $activePlans = ProductionPlan::where('status', 'active')
            ->orWhere('status', 'pending')
            ->with('equipment')
            ->get();

        return view('work-shifts.create', compact('equipment', 'activePlans'));
    }

    /**
     * Store a newly created resource in storage (iniciar jornada).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'plan_id' => 'nullable|exists:production_plans,id',
            'shift_type' => 'required|in:morning,afternoon,night',
            'notes' => 'nullable|string',
        ]);

        // Verificar que no haya otra jornada activa para este equipo
        $activeShift = WorkShift::where('equipment_id', $validated['equipment_id'])
            ->where('status', 'active')
            ->whereNull('end_time')
            ->first();

        if ($activeShift) {
            return redirect()->back()
                ->with('error', 'Ya existe una jornada activa para este equipo. Debe finalizarla primero.');
        }

        $shift = WorkShift::startShift(
            $validated['equipment_id'],
            $validated['plan_id'] ?? null,
            $validated['shift_type'],
            Auth::id()
        );

        if (isset($validated['notes'])) {
            $shift->update(['notes' => $validated['notes']]);
        }

        return redirect()->route('work-shifts.show', $shift)
            ->with('success', 'Jornada de trabajo iniciada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkShift $workShift)
    {
        $workShift->load(['equipment', 'plan', 'operator']);

        return view('work-shifts.show', compact('workShift'));
    }

    /**
     * Finalizar jornada.
     */
    public function end(WorkShift $workShift)
    {
        if ($workShift->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Esta jornada ya ha sido finalizada.');
        }

        $workShift->endShift();

        return redirect()->route('work-shifts.show', $workShift)
            ->with('success', 'Jornada de trabajo finalizada exitosamente.');
    }

    /**
     * Registrar producción en la jornada.
     */
    public function recordProduction(Request $request, WorkShift $workShift)
    {
        if ($workShift->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Esta jornada no está activa.',
            ], 400);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'good_units' => 'required|integer|min:0',
            'defective_units' => 'required|integer|min:0',
        ]);

        // Validar que quantity = good_units + defective_units
        if ($validated['quantity'] != ($validated['good_units'] + $validated['defective_units'])) {
            return response()->json([
                'success' => false,
                'message' => 'La cantidad total debe ser igual a la suma de unidades buenas y defectuosas.',
            ], 400);
        }

        $workShift->recordProduction(
            $validated['quantity'],
            $validated['good_units'],
            $validated['defective_units']
        );

        return response()->json([
            'success' => true,
            'message' => 'Producción registrada exitosamente.',
            'data' => [
                'actual_production' => $workShift->actual_production,
                'good_units' => $workShift->good_units,
                'defective_units' => $workShift->defective_units,
                'progress' => $workShift->progress,
                'quality_rate' => $workShift->quality_rate,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkShift $workShift)
    {
        // Solo se pueden eliminar jornadas activas sin producción
        if ($workShift->status !== 'active' || $workShift->actual_production > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar esta jornada.');
        }

        $workShift->delete();

        return redirect()->route('work-shifts.index')
            ->with('success', 'Jornada eliminada exitosamente.');
    }
}
