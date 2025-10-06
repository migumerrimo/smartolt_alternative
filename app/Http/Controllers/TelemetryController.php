<?php

namespace App\Http\Controllers;

use App\Models\Telemetry;
use App\Models\Olt;
use App\Models\Onu;
use Illuminate\Http\Request;

class TelemetryController extends Controller
{
    public function index(Request $request)
    {
        $telemetry = Telemetry::with(['olt','onu'])->get();

        if ($request->wantsJson()) {
            return response()->json($telemetry);
        }

        return view('telemetry.index', compact('telemetry'));
    }

    public function create()
    {
        $olts = Olt::all();
        $onus = Onu::all();

        return view('telemetry.create', compact('olts','onus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'olt_id' => 'required|exists:olts,id',
            'onu_id' => 'nullable|exists:onus,id',
            'metric' => 'required|string|max:50',
            'value'  => 'required|numeric',
            'unit'   => 'nullable|string|max:20',
        ]);

        $telemetry = Telemetry::create($validated);

        if ($request->wantsJson()) {
            return response()->json($telemetry, 201);
        }

        return redirect()->route('telemetry.index')
                         ->with('success', 'Métrica de telemetría creada correctamente');
    }

    public function show(Request $request, Telemetry $telemetry)
    {
        if ($request->wantsJson()) {
            return response()->json($telemetry->load(['olt','onu']));
        }

        return view('telemetry.show', compact('telemetry'));
    }

    public function edit(Telemetry $telemetry)
    {
        $olts = Olt::all();
        $onus = Onu::all();

        return view('telemetry.edit', compact('telemetry','olts','onus'));
    }

    public function update(Request $request, Telemetry $telemetry)
    {
        $validated = $request->validate([
            'metric' => 'required|string|max:50',
            'value'  => 'required|numeric',
            'unit'   => 'nullable|string|max:20',
        ]);

        $telemetry->update($validated);

        if ($request->wantsJson()) {
            return response()->json($telemetry);
        }

        return redirect()->route('telemetry.index')
                         ->with('success', 'Métrica de telemetría actualizada correctamente');
    }

    public function destroy(Request $request, Telemetry $telemetry)
    {
        $telemetry->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('telemetry.index')
                         ->with('success', 'Métrica de telemetría eliminada correctamente');
    }
}
