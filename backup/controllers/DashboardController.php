<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Services\KpiService;

class DashboardController extends Controller
{
    protected $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    public function index()
    {
        $equipment = Equipment::where('is_active', true)->get();
        $kpis = $this->kpiService->getAllEquipmentKPIs();

        return view('dashboard', compact('equipment', 'kpis'));
    }
}
