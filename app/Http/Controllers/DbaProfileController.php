<?php

namespace App\Http\Controllers;

use App\Models\DbaProfile;
use App\Models\Olt;
use Illuminate\Http\Request;

class DbaProfileController extends Controller
{
    public function index(Request $request)
    {
        $dbaProfiles = DbaProfile::with('olt')->get();

        if ($request->wantsJson()) {
            return response()->json($dbaProfiles);
        }

        return view('dba_profiles.index', compact('dbaProfiles'));
    }

    public function create()
    {
        $olts = Olt::all();
        return view('dba_profiles.create', compact('olts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'olt_id'       => 'required|exists:olts,id',
            'name'         => 'required|string|max:50',
            'type'         => 'required|in:type1,type2,type3,type4',
            'max_bandwidth'=> 'required|integer|min:1',
        ]);

        $dbaProfile = DbaProfile::create($validated);

        if ($request->wantsJson()) {
            return response()->json($dbaProfile, 201);
        }

        return redirect()->route('dba-profiles.index')
                         ->with('success', 'Perfil DBA creado correctamente');
    }

    public function show(Request $request, DbaProfile $dbaProfile)
    {
        if ($request->wantsJson()) {
            return response()->json($dbaProfile->load('olt'));
        }

        return view('dba_profiles.show', compact('dbaProfile'));
    }

    public function edit(DbaProfile $dbaProfile)
    {
        $olts = Olt::all();
        return view('dba_profiles.edit', compact('dbaProfile','olts'));
    }

    public function update(Request $request, DbaProfile $dbaProfile)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:50',
            'type'         => 'required|in:type1,type2,type3,type4',
            'max_bandwidth'=> 'required|integer|min:1',
        ]);

        $dbaProfile->update($validated);

        if ($request->wantsJson()) {
            return response()->json($dbaProfile);
        }

        return redirect()->route('dba-profiles.index')
                         ->with('success', 'Perfil DBA actualizado correctamente');
    }

    public function destroy(Request $request, DbaProfile $dbaProfile)
    {
        $dbaProfile->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('dba-profiles.index')
                         ->with('success', 'Perfil DBA eliminado');
    }
}
