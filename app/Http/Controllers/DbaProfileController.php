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
        $olts = Olt::all();

        // Si se envía 'olt_id' y 'fetch_from_olt', obtenemos los datos del OLT
        $oltDbaProfiles = null;
        $selectedOltId = null;
        if ($request->filled('olt_id') && $request->filled('fetch_from_olt')) {
            $selectedOltId = $request->olt_id;
            $olt = Olt::find($selectedOltId);
            if ($olt) {
                try {
                    $ssh = new \App\Services\OltSshService(
                        $olt->management_ip,
                        $olt->ssh_port,
                        $olt->ssh_username,
                        $olt->ssh_password
                    );
                    $resp = $ssh->getDbaProfiles();
                    $output = $resp['raw'] ?? '';

                    // Parsear la salida
                    $oltDbaProfiles = [];
                    $lines = preg_split('/\r?\n/', $output);
                    $headerIndex = null;
                    foreach ($lines as $i => $line) {
                        if (preg_match('/\bProfile-ID\b.*\btype\b/i', $line)) {
                            $headerIndex = $i;
                            break;
                        }
                    }

                    if ($headerIndex !== null) {
                        $dataStart = null;
                        for ($j = $headerIndex + 1; $j < count($lines); $j++) {
                            if (preg_match('/^-{3,}/', trim($lines[$j]))) {
                                $dataStart = $j + 1;
                                break;
                            }
                        }

                        if ($dataStart !== null) {
                            for ($k = $dataStart; $k < count($lines); $k++) {
                                $row = trim($lines[$k]);
                                if ($row === '' || preg_match('/^-{3,}/', $row)) {
                                    break;
                                }

                                if (preg_match('/^\s*(\d+)\s+(\d+)\s+(\S+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $row, $m)) {
                                    $oltDbaProfiles[] = [
                                        'profile_id' => intval($m[1]),
                                        'type' => intval($m[2]),
                                        'bandwidth_compensation' => $m[3],
                                        'fix_kbps' => intval($m[4]),
                                        'assure_kbps' => intval($m[5]),
                                        'max_kbps' => intval($m[6]),
                                        'bind_times' => intval($m[7])
                                    ];
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error fetching DBA Profiles from OLT', ['error' => $e->getMessage()]);
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'db_profiles' => $dbaProfiles,
                'olt_profiles' => $oltDbaProfiles
            ]);
        }

        return view('dba_profiles.index', compact('dbaProfiles', 'olts', 'oltDbaProfiles', 'selectedOltId'));
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
