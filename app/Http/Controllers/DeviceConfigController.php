<?php

namespace App\Http\Controllers;

use App\Models\DeviceConfig;
use App\Models\Olt;
use App\Models\User;
use Illuminate\Http\Request;

class DeviceConfigController extends Controller
{
    public function index(Request $request)
    {
        $configs = DeviceConfig::with(['olt','appliedBy'])->get();

        if ($request->wantsJson()) {
            return response()->json($configs);
        }

        return view('device_configs.index', compact('configs'));
    }

    public function create()
    {
        $olts = Olt::all();
        $users = User::all();

        return view('device_configs.create', compact('olts','users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'olt_id'     => 'required|exists:olts,id',
            'device_type'=> 'required|in:OLT,ROUTER,SWITCH',
            'device_name'=> 'nullable|string|max:100',
            'config_text'=> 'required|string',
            'version'    => 'nullable|string|max:50',
            'applied_by' => 'nullable|exists:users,id',
            'applied_at' => 'nullable|date',
        ]);

        $config = DeviceConfig::create($validated);

        if ($request->wantsJson()) {
            return response()->json($config, 201);
        }

        return redirect()->route('device-configs.index')
                         ->with('success', 'Configuración de dispositivo creada correctamente');
    }

    public function show(Request $request, DeviceConfig $deviceConfig)
    {
        if ($request->wantsJson()) {
            return response()->json($deviceConfig->load(['olt','appliedBy']));
        }

        return view('device_configs.show', compact('deviceConfig'));
    }

    public function edit(DeviceConfig $deviceConfig)
    {
        $olts = Olt::all();
        $users = User::all();

        return view('device_configs.edit', compact('deviceConfig','olts','users'));
    }

    public function update(Request $request, DeviceConfig $deviceConfig)
    {
        $validated = $request->validate([
            'device_name'=> 'nullable|string|max:100',
            'config_text'=> 'nullable|string',
            'version'    => 'nullable|string|max:50',
            'applied_by' => 'nullable|exists:users,id',
            'applied_at' => 'nullable|date',
        ]);

        $deviceConfig->update($validated);

        if ($request->wantsJson()) {
            return response()->json($deviceConfig);
        }

        return redirect()->route('device-configs.index')
                         ->with('success', 'Configuración de dispositivo actualizada correctamente');
    }

    public function destroy(Request $request, DeviceConfig $deviceConfig)
    {
        $deviceConfig->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('device-configs.index')
                         ->with('success', 'Configuración de dispositivo eliminada correctamente');
    }
}
