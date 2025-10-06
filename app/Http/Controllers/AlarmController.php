<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Olt;
use App\Models\Onu;
use Illuminate\Http\Request;

class AlarmController extends Controller
{
    public function index(Request $request)
    {
        $alarms = Alarm::with(['olt','onu'])->get();

        if ($request->wantsJson()) {
            return response()->json($alarms);
        }

        return view('alarms.index', compact('alarms'));
    }

    public function create()
    {
        $olts = Olt::all();
        $onus = Onu::all();

        return view('alarms.create', compact('olts','onus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'olt_id'  => 'required|exists:olts,id',
            'onu_id'  => 'nullable|exists:onus,id',
            'severity'=> 'required|in:critical,major,minor,warning,info',
            'message' => 'required|string',
            'active'  => 'boolean',
        ]);

        $alarm = Alarm::create($validated);

        if ($request->wantsJson()) {
            return response()->json($alarm, 201);
        }

        return redirect()->route('alarms.index')
                         ->with('success', 'Alarma creada correctamente');
    }

    public function show(Request $request, Alarm $alarm)
    {
        if ($request->wantsJson()) {
            return response()->json($alarm->load(['olt','onu']));
        }

        return view('alarms.show', compact('alarm'));
    }

    public function edit(Alarm $alarm)
    {
        $olts = Olt::all();
        $onus = Onu::all();

        return view('alarms.edit', compact('alarm','olts','onus'));
    }

    public function update(Request $request, Alarm $alarm)
    {
        $validated = $request->validate([
            'severity'=> 'required|in:critical,major,minor,warning,info',
            'message' => 'required|string',
            'active'  => 'boolean',
        ]);

        $alarm->update($validated);

        if ($request->wantsJson()) {
            return response()->json($alarm);
        }

        return redirect()->route('alarms.index')
                         ->with('success', 'Alarma actualizada correctamente');
    }

    public function destroy(Request $request, Alarm $alarm)
    {
        $alarm->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('alarms.index')
                         ->with('success', 'Alarma eliminada correctamente');
    }
}
