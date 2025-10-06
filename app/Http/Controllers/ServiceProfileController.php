<?php

namespace App\Http\Controllers;

use App\Models\ServiceProfile;
use App\Models\Olt;
use App\Models\Vlan;
use Illuminate\Http\Request;

class ServiceProfileController extends Controller
{
    public function index(Request $request)
    {
        $serviceProfiles = ServiceProfile::with(['olt','vlan'])->get();

        if ($request->wantsJson()) {
            return response()->json($serviceProfiles);
        }

        return view('service_profiles.index', compact('serviceProfiles'));
    }

    public function create()
    {
        $olts = Olt::all();
        $vlans = Vlan::all();
        return view('service_profiles.create', compact('olts','vlans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'olt_id'   => 'required|exists:olts,id',
            'name'     => 'required|string|max:50',
            'service'  => 'required|in:internet,voip,iptv,triple-play',
            'eth_ports'=> 'required|integer|min:1',
            'vlan_id'  => 'nullable|exists:vlans,id',
        ]);

        $serviceProfile = ServiceProfile::create($validated);

        if ($request->wantsJson()) {
            return response()->json($serviceProfile, 201);
        }

        return redirect()->route('service-profiles.index')
                         ->with('success', 'Perfil de Servicio creado correctamente');
    }

    public function show(Request $request, ServiceProfile $serviceProfile)
    {
        if ($request->wantsJson()) {
            return response()->json($serviceProfile->load(['olt','vlan']));
        }

        return view('service_profiles.show', compact('serviceProfile'));
    }

    public function edit(ServiceProfile $serviceProfile)
    {
        $olts = Olt::all();
        $vlans = Vlan::all();
        return view('service_profiles.edit', compact('serviceProfile','olts','vlans'));
    }

    public function update(Request $request, ServiceProfile $serviceProfile)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:50',
            'service'  => 'required|in:internet,voip,iptv,triple-play',
            'eth_ports'=> 'required|integer|min:1',
            'vlan_id'  => 'nullable|exists:vlans,id',
        ]);

        $serviceProfile->update($validated);

        if ($request->wantsJson()) {
            return response()->json($serviceProfile);
        }

        return redirect()->route('service-profiles.index')
                         ->with('success', 'Perfil de Servicio actualizado correctamente');
    }

    public function destroy(Request $request, ServiceProfile $serviceProfile)
    {
        $serviceProfile->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('service-profiles.index')
                         ->with('success', 'Perfil de Servicio eliminado');
    }
}
