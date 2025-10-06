<?php

namespace App\Http\Controllers;

use App\Models\TrafficTable;
use App\Models\Olt;
use Illuminate\Http\Request;

class TrafficTableController extends Controller
{
    public function index(Request $request)
    {
        $trafficTables = TrafficTable::with('olt')->get();

        if ($request->wantsJson()) {
            return response()->json($trafficTables);
        }

        return view('traffic_tables.index', compact('trafficTables'));
    }

    public function create()
    {
        $olts = Olt::all();
        return view('traffic_tables.create', compact('olts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'olt_id'  => 'required|exists:olts,id',
            'name'    => 'required|string|max:50',
            'cir'     => 'required|integer|min:1',
            'pir'     => 'required|integer|min:1',
            'priority'=> 'nullable|integer|min:0',
        ]);

        $trafficTable = TrafficTable::create($validated);

        if ($request->wantsJson()) {
            return response()->json($trafficTable, 201);
        }

        return redirect()->route('traffic-table.index')
                         ->with('success', 'Traffic Table creada correctamente');
    }

    public function show(Request $request, TrafficTable $trafficTable)
    {
        if ($request->wantsJson()) {
            return response()->json($trafficTable->load('olt'));
        }

        return view('traffic_tables.show', compact('trafficTable'));
    }

    public function edit(TrafficTable $trafficTable)
    {
        $olts = Olt::all();
        return view('traffic_tables.edit', compact('trafficTable','olts'));
    }

    public function update(Request $request, TrafficTable $trafficTable)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:50',
            'cir'     => 'required|integer|min:1',
            'pir'     => 'required|integer|min:1',
            'priority'=> 'nullable|integer|min:0',
        ]);

        $trafficTable->update($validated);

        if ($request->wantsJson()) {
            return response()->json($trafficTable);
        }

        return redirect()->route('traffic-table.index')
                         ->with('success', 'Traffic Table actualizada correctamente');
    }

    public function destroy(Request $request, TrafficTable $trafficTable)
    {
        $trafficTable->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('traffic-table.index')
                         ->with('success', 'Traffic Table eliminada correctamente');
    }
}
