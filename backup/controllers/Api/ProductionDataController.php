<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductionData;
use App\Events\ProductionDataUpdated;
use Illuminate\Http\Request;

class ProductionDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductionData::with('equipment');

        if ($request->equipment_id) {
            $query->where('equipment_id', $request->equipment_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('production_date', [$request->start_date, $request->end_date]);
        }

        $data = $query->latest('production_date')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'planned_production' => 'required|integer|min:0',
            'actual_production' => 'required|integer|min:0',
            'good_units' => 'required|integer|min:0',
            'defective_units' => 'required|integer|min:0',
            'cycle_time' => 'required|numeric|min:0',
            'production_date' => 'required|date',
        ]);

        $productionData = ProductionData::create($validated);

        // Broadcast el evento en tiempo real
        broadcast(new ProductionDataUpdated($productionData))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Production data created successfully',
            'data' => $productionData->load('equipment'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $productionData = ProductionData::with('equipment')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $productionData,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $productionData = ProductionData::findOrFail($id);

        $validated = $request->validate([
            'planned_production' => 'sometimes|integer|min:0',
            'actual_production' => 'sometimes|integer|min:0',
            'good_units' => 'sometimes|integer|min:0',
            'defective_units' => 'sometimes|integer|min:0',
            'cycle_time' => 'sometimes|numeric|min:0',
            'production_date' => 'sometimes|date',
        ]);

        $productionData->update($validated);

        // Broadcast el evento en tiempo real
        broadcast(new ProductionDataUpdated($productionData))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Production data updated successfully',
            'data' => $productionData->load('equipment'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $productionData = ProductionData::findOrFail($id);
        $productionData->delete();

        return response()->json([
            'success' => true,
            'message' => 'Production data deleted successfully',
        ]);
    }
}
