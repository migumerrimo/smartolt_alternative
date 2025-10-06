<?php

namespace App\Http\Controllers;

use App\Models\LineProfile;
use App\Models\Olt;
use App\Models\DbaProfile;
use App\Models\Vlan;
use Illuminate\Http\Request;

class LineProfileController extends Controller
{
    public function index(Request $request)
    {
        $lineProfiles = LineProfile::with(['olt','dbaProfile','vlan'])->get();

        if ($request->wantsJson()) {
            return response()->json($lineProfiles);
        }

        return view('line_profiles.index', compact('lineProfiles'));
    }

    public function create()
    {
        $olts = Olt::all();
        $dbaProfiles = DbaProfile::all();
        $vlans = Vlan::all();

        return view('line_profiles.create', compact('olts','dbaProfiles','vlans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'olt_id'         => 'required|exists:olts,id',
            'name'           => 'required|string|max:50',
            'dba_profile_id' => 'nullable|exists:dba_profiles,id',
            'tcont'          => 'required|integer|min:1',
            'gem_ports'      => 'required|integer|min:1',
            'vlan_id'        => 'nullable|exists:vlans,id',
        ]);

        $lineProfile = LineProfile::create($validated);

        if ($request->wantsJson()) {
            return response()->json($lineProfile, 201);
        }

        return redirect()->route('line-profiles.index')
                         ->with('success', 'Perfil de Línea creado correctamente');
    }

    public function show(Request $request, LineProfile $lineProfile)
    {
        if ($request->wantsJson()) {
            return response()->json($lineProfile->load(['olt','dbaProfile','vlan']));
        }

        return view('line_profiles.show', compact('lineProfile'));
    }

    public function edit(LineProfile $lineProfile)
    {
        $olts = Olt::all();
        $dbaProfiles = DbaProfile::all();
        $vlans = Vlan::all();

        return view('line_profiles.edit', compact('lineProfile','olts','dbaProfiles','vlans'));
    }

    public function update(Request $request, LineProfile $lineProfile)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:50',
            'dba_profile_id' => 'nullable|exists:dba_profiles,id',
            'tcont'          => 'required|integer|min:1',
            'gem_ports'      => 'required|integer|min:1',
            'vlan_id'        => 'nullable|exists:vlans,id',
        ]);

        $lineProfile->update($validated);

        if ($request->wantsJson()) {
            return response()->json($lineProfile);
        }

        return redirect()->route('line-profiles.index')
                         ->with('success', 'Perfil de Línea actualizado correctamente');
    }

    public function destroy(Request $request, LineProfile $lineProfile)
    {
        $lineProfile->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('line-profiles.index')
                         ->with('success', 'Perfil de Línea eliminado');
    }
}
