<?php

namespace App\Http\Controllers;

use App\Models\DowntimeData;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DowntimeDataController extends Controller
{
    /**
     * Display a listing of the downtime data.
     */
    public function index(Request $request)
    {
        $query = DowntimeData::with('equipment');

        // Filter by equipment
        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('start_time', '<=', $request->end_date);
        }

        $downtimeData = $query->orderBy('start_time', 'desc')->paginate(15);
        $equipment = Equipment::where('is_active', true)->get();

        return view('downtime.index', compact('downtimeData', 'equipment'));
    }

    /**
     * Show the form for creating new downtime data.
     */
    public function create()
    {
        $equipment = Equipment::where('is_active', true)->get();
        return view('downtime.create', compact('equipment'));
    }

    /**
     * Store a newly created downtime data in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'duration_minutes' => 'nullable|integer|min:1',
            'reason' => 'required|string|max:255',
            'category' => 'required|in:planificado,no planificado',
            'description' => 'nullable|string',
        ]);

        // Calculate duration if both start and end times are provided
        if ($validated['end_time'] && $validated['start_time']) {
            $start = Carbon::parse($validated['start_time']);
            $end = Carbon::parse($validated['end_time']);
            $validated['duration_minutes'] = $start->diffInMinutes($end);
        }

        DowntimeData::create($validated);

        return redirect()->route('downtime.index')
            ->with('success', 'Tiempo muerto registrado exitosamente.');
    }

    /**
     * Show the form for editing the specified downtime data.
     */
    public function edit(DowntimeData $downtime)
    {
        $equipment = Equipment::where('is_active', true)->get();
        return view('downtime.edit', compact('downtime', 'equipment'));
    }

    /**
     * Update the specified downtime data in storage.
     */
    public function update(Request $request, DowntimeData $downtime)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'duration_minutes' => 'nullable|integer|min:1',
            'reason' => 'required|string|max:255',
            'category' => 'required|in:planificado,no planificado',
            'description' => 'nullable|string',
        ]);

        // Calculate duration if both start and end times are provided
        if ($validated['end_time'] && $validated['start_time']) {
            $start = Carbon::parse($validated['start_time']);
            $end = Carbon::parse($validated['end_time']);
            $validated['duration_minutes'] = $start->diffInMinutes($end);
        }

        $downtime->update($validated);

        return redirect()->route('downtime.index')
            ->with('success', 'Tiempo muerto actualizado exitosamente.');
    }

    /**
     * Remove the specified downtime data from storage.
     */
    public function destroy(DowntimeData $downtime)
    {
        $downtime->delete();

        return redirect()->route('downtime.index')
            ->with('success', 'Registro de tiempo muerto eliminado exitosamente.');
    }
}
