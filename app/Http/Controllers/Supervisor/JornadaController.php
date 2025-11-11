<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supervisor\IniciarJornadaRequest;
use App\Http\Requests\Supervisor\FinalizarJornadaRequest;
use App\Http\Requests\Supervisor\PausarJornadaRequest;
use App\Http\Requests\Supervisor\ReanudarJornadaRequest;
use App\Models\JornadaProduccion;
use App\Models\Maquina;
use App\Services\Contracts\JornadaServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JornadaController extends Controller
{
    public function __construct(
        private JornadaServiceInterface $jornadaService
    ) {}

    /**
     * Display a listing of jornadas
     */
    public function index(): View
    {
        $jornadas = JornadaProduccion::with('maquina')
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        return view('supervisor.jornadas.index', [
            'jornadas' => $jornadas,
        ]);
    }

    /**
     * Show the form for creating a new jornada
     */
    public function create(): View
    {
        $maquinas = Maquina::active()
            ->with('planMaquina')
            ->get();

        return view('supervisor.jornadas.create', [
            'maquinas' => $maquinas,
        ]);
    }

    /**
     * Store a newly created jornada
     */
    public function store(IniciarJornadaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $jornada = $this->jornadaService->iniciarJornada(
                $validated['maquina_id'],
                Auth::id()
            );

            return redirect()
                ->route('supervisor.jornadas.show', $jornada)
                ->with('success', 'Jornada iniciada exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified jornada
     */
    public function show(JornadaProduccion $jornada): View
    {
        $jornada->load(['maquina', 'registrosProduccion', 'eventosParada']);

        return view('supervisor.jornadas.show', [
            'jornada' => $jornada,
        ]);
    }

    /**
     * Show the form for editing the jornada
     */
    public function edit(JornadaProduccion $jornada): View
    {
        return view('supervisor.jornadas.edit', [
            'jornada' => $jornada,
        ]);
    }

    /**
     * Update the specified jornada
     */
    public function update(JornadaProduccion $jornada): RedirectResponse
    {
        // Jornadas no se pueden editar una vez iniciadas
        return redirect()
            ->route('supervisor.jornadas.show', $jornada)
            ->with('info', 'Las jornadas no se pueden editar después de iniciadas');
    }

    /**
     * Delete the specified jornada
     */
    public function destroy(JornadaProduccion $jornada): RedirectResponse
    {
        // Solo se pueden eliminar jornadas en estado 'draft'
        if ($jornada->status !== 'draft') {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Solo se pueden eliminar jornadas no iniciadas']);
        }

        $jornada->delete();

        return redirect()
            ->route('supervisor.jornadas.index')
            ->with('success', 'Jornada eliminada');
    }

    /**
     * Pausar una jornada en ejecución
     */
    public function pausar(JornadaProduccion $jornada, PausarJornadaRequest $request): RedirectResponse
    {
        if ($jornada->status !== 'running') {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Solo se pueden pausar jornadas en ejecución']);
        }

        try {
            $validated = $request->validated();

            $this->jornadaService->pausarJornada(
                $jornada->id,
                $validated['motivo']
            );

            return redirect()
                ->route('supervisor.jornadas.show', $jornada)
                ->with('success', 'Jornada pausada exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Reanudar una jornada pausada
     */
    public function reanudar(JornadaProduccion $jornada, ReanudarJornadaRequest $request): RedirectResponse
    {
        if ($jornada->status !== 'paused') {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Solo se pueden reanudar jornadas pausadas']);
        }

        try {
            $this->jornadaService->reanudarJornada($jornada->id);

            return redirect()
                ->route('supervisor.jornadas.show', $jornada)
                ->with('success', 'Jornada reanudada exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Finalizar una jornada
     */
    public function finalizar(JornadaProduccion $jornada, FinalizarJornadaRequest $request): RedirectResponse
    {
        if (!in_array($jornada->status, ['running', 'paused'])) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Solo se pueden finalizar jornadas en ejecución o pausadas']);
        }

        try {
            $this->jornadaService->finalizarJornada($jornada->id);

            return redirect()
                ->route('supervisor.jornadas.show', $jornada)
                ->with('success', 'Jornada finalizada exitosamente');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
