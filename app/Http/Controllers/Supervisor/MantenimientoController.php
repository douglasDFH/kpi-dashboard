<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\RegistrarMantenimientoRequest;
use App\Models\Maquina;
use App\Models\RegistroMantenimiento;
use App\Services\Contracts\MantenimientoServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MantenimientoController extends Controller
{
    public function __construct(
        private MantenimientoServiceInterface $mantenimientoService
    ) {}

    /**
     * Display a listing of maintenance records
     */
    public function index(): View
    {
        $registros = RegistroMantenimiento::with('maquina')
            ->orderBy('fecha', 'desc')
            ->paginate(20);

        return view('supervisor.mantenimiento.index', [
            'registros' => $registros,
        ]);
    }

    /**
     * Show the form for creating a new maintenance record
     */
    public function create(): View
    {
        $maquinas = Maquina::active()->get();

        return view('supervisor.mantenimiento.create', [
            'maquinas' => $maquinas,
        ]);
    }

    /**
     * Store a newly created maintenance record
     */
    public function store(RegistrarMantenimientoRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $registro = $this->mantenimientoService->registrarMantenimiento(
                $validated['maquina_id'],
                Auth::id(),
                $validated['tipo'],
                $validated['descripcion']
            );

            return redirect()
                ->route('supervisor.mantenimiento.show', $registro)
                ->with('success', 'Registro de mantenimiento creado exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified maintenance record
     */
    public function show(RegistroMantenimiento $registro): View
    {
        $registro->load('maquina', 'supervisor');

        return view('supervisor.mantenimiento.show', [
            'registro' => $registro,
        ]);
    }

    /**
     * Show the form for editing maintenance record
     */
    public function edit(RegistroMantenimiento $registro): View
    {
        return view('supervisor.mantenimiento.edit', [
            'registro' => $registro,
        ]);
    }

    /**
     * Update the specified maintenance record
     */
    public function update(RegistrarMantenimientoRequest $request, RegistroMantenimiento $registro): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $registro->update([
                'tipo' => $validated['tipo'],
                'descripcion' => $validated['descripcion'],
                'duracion_minutos' => $validated['duracion_minutos'] ?? null,
                'piezas_reemplazadas' => $validated['piezas_reemplazadas'] ?? null,
            ]);

            return redirect()
                ->route('supervisor.mantenimiento.show', $registro)
                ->with('success', 'Registro actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete the specified maintenance record
     */
    public function destroy(RegistroMantenimiento $registro): RedirectResponse
    {
        $registro->delete();

        return redirect()
            ->route('supervisor.mantenimiento.index')
            ->with('success', 'Registro de mantenimiento eliminado');
    }

    /**
     * Get maintenance history for a specific machine
     */
    public function historialMaquina(Maquina $maquina): View
    {
        $registros = $this->mantenimientoService->obtenerHistorial($maquina->id, 50);

        return view('supervisor.mantenimiento.historial', [
            'maquina' => $maquina,
            'registros' => $registros,
        ]);
    }
}
