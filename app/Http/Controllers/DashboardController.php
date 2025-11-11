<?php

namespace App\Http\Controllers;

use App\Models\JornadaProduccion;
use App\Models\Maquina;
use App\Models\RegistroProduccion;
use App\Services\Contracts\KpiServiceInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected KpiServiceInterface $kpiService;

    public function __construct(KpiServiceInterface $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    public function index(Request $request)
    {
        // Obtener máquina seleccionada o la primera por defecto
        $maquinaId = $request->get('maquina_id');
        $maquina = $maquinaId ? Maquina::find($maquinaId) : Maquina::first();

        if (! $maquina) {
            return view('dashboard', [
                'maquinas' => collect(),
                'kpis' => [],
                'metricas' => [],
                'maquinaSeleccionada' => null,
            ]);
        }

        // Obtener jornada activa para la máquina (status 'running')
        $jornadaActiva = JornadaProduccion::where('maquina_id', $maquina->id)
            ->where('status', 'running')
            ->first();

        // Calcular KPIs usando el servicio (sin parámetros de fecha, usa jornadas)
        $kpis = $this->kpiService->calculateOEE($maquina->id);

        // Obtener métricas adicionales
        $metricas = $this->kpiService->calculateAdditionalMetrics($maquina->id);

        // Obtener datos de producción recientes para gráficos
        $produccionReciente = RegistroProduccion::where('maquina_id', $maquina->id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        // Preparar datos para gráficos
        $chartData = $this->prepararDatosGraficos($kpis, $metricas);

        return view('dashboard', [
            'maquinas' => Maquina::all(),
            'maquinaSeleccionada' => $maquina,
            'kpis' => $kpis,
            'metricas' => $metricas,
            'chartData' => $chartData,
            'jornadaActiva' => $jornadaActiva,
        ]);
    }

    private function prepararDatosGraficos($kpis, $metricas)
    {
        return [
            'oee_components' => [
                'labels' => ['Disponibilidad', 'Rendimiento', 'Calidad'],
                'data' => [
                    $kpis['availability'] ?? 0,
                    $kpis['performance'] ?? 0,
                    $kpis['quality'] ?? 0,
                ],
                'backgroundColor' => ['#10b981', '#f59e0b', '#a855f7'],
            ],
            'production_summary' => [
                'labels' => ['Buenas', 'Defectuosas'],
                'data' => [
                    $metricas['total_bueno'] ?? 0,
                    $metricas['total_malo'] ?? 0,
                ],
                'backgroundColor' => ['#10b981', '#ef4444'],
            ],
        ];
    }
}
