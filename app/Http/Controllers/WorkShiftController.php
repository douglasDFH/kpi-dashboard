<?php

namespace App\Http\Controllers;

use App\Models\WorkShift;
use App\Models\Equipment;
use App\Models\ProductionPlan;
use App\Jobs\SimulateProduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $plans = ProductionPlan::whereIn('status', ['active', 'pending'])
            ->with('equipment')
            ->get();
        $operators = \App\Models\User::all();
        $activeShifts = WorkShift::where('status', 'active')->get();

        return view('work-shifts.create', compact('equipment', 'plans', 'operators', 'activeShifts'));
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
            'operator_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        // Verificar que no haya otra jornada activa para este equipo
        $activeShift = WorkShift::where('equipment_id', $validated['equipment_id'])
            ->where('status', 'active')
            ->whereNull('end_time')
            ->first();

        if ($activeShift) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe una jornada activa para este equipo. Debe finalizarla primero.');
        }

        $shift = WorkShift::startShift(
            $validated['equipment_id'],
            $validated['plan_id'] ?? null,
            $validated['shift_type'],
            $validated['operator_id'] ?? Auth::id()
        );

        if (isset($validated['notes'])) {
            $shift->update(['notes' => $validated['notes']]);
        }

        // Iniciar simulación de producción automática
        dispatch(new SimulateProduction($shift))->delay(now()->addSeconds(5));

        return redirect()->route('work-shifts.show', $shift)
            ->with('success', 'Jornada de trabajo iniciada exitosamente. La producción comenzará automáticamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkShift $workShift)
    {
        $workShift->load(['equipment', 'plan', 'operator']);
        $shift = $workShift; // Alias for view compatibility
        return view('work-shifts.show', compact('shift'));
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

        // Finalizar la jornada (crea ProductionData automáticamente en el modelo)
        $workShift->endShift();

        return redirect()->route('work-shifts.show', $workShift)
            ->with('success', 'Jornada finalizada y datos de producción registrados exitosamente.');
    }

    /**
     * Registrar producción en la jornada.
     */
    public function recordProduction(Request $request, WorkShift $workShift)
    {
        // LOG: Datos recibidos
        \Log::info('📥 recordProduction - Datos recibidos:', [
            'shift_id' => $workShift->id,
            'shift_status' => $workShift->status,
            'request_data' => $request->all(),
        ]);

        // Permitir tanto 'active' como 'pending_registration'
        if (!in_array($workShift->status, ['active', 'pending_registration'])) {
            \Log::error('❌ Status inválido:', ['status' => $workShift->status]);
            return response()->json([
                'success' => false,
                'message' => 'Esta jornada no está disponible para registrar producción.'
            ], 400);
        }

        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
                'good_units' => 'required|integer|min:0',
                'defective_units' => 'required|integer|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Error de validación:', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos: ' . json_encode($e->errors())
            ], 400);
        }

        // Validar que quantity = good_units + defective_units
        if ($validated['quantity'] != ($validated['good_units'] + $validated['defective_units'])) {
            \Log::error('❌ Suma incorrecta:', [
                'quantity' => $validated['quantity'],
                'good_units' => $validated['good_units'],
                'defective_units' => $validated['defective_units'],
                'suma' => $validated['good_units'] + $validated['defective_units']
            ]);
            return response()->json([
                'success' => false,
                'message' => 'La cantidad total debe ser igual a la suma de unidades buenas y defectuosas.'
            ], 400);
        }

        \Log::info('✅ Validación exitosa, registrando producción...');

        // Guardar el status anterior para decidir si finalizar
        $wasActive = $workShift->status === 'active';
        $wasPendingRegistration = $workShift->status === 'pending_registration';

        $workShift->recordProduction(
            $validated['quantity'],
            $validated['good_units'],
            $validated['defective_units']
        );

        \Log::info('✅ Producción registrada, estado actual:', [
            'status' => $workShift->status,
            'actual_production' => $workShift->actual_production,
            'wasActive' => $wasActive,
            'wasPendingRegistration' => $wasPendingRegistration
        ]);

        // SOLO finalizar si estaba en pending_registration
        // (El usuario está confirmando la producción final)
        if ($wasPendingRegistration) {
            \Log::info('🏁 Finalizando jornada manualmente por confirmación del usuario...');
            $workShift->endShift();
            \Log::info('✅ Jornada finalizada, status:', ['status' => $workShift->status]);
        }

        return response()->json([
            'success' => true,
            'message' => $workShift->status === 'completed' 
                ? 'Producción registrada y jornada finalizada exitosamente.' 
                : 'Producción registrada exitosamente.',
            'data' => [
                'actual_production' => $workShift->actual_production,
                'good_units' => $workShift->good_units,
                'defective_units' => $workShift->defective_units,
                'progress' => $workShift->progress,
                'quality_rate' => $workShift->quality_rate,
                'status' => $workShift->status,
            ]
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

