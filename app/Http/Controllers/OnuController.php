<?php

namespace App\Http\Controllers;

use App\Models\Onu;
use App\Models\Olt;
use App\Models\LineProfile;
use App\Models\ServiceProfile;
use Illuminate\Http\Request;
use PDF;

class OnuController extends Controller
{
    public function index(Request $request)
    {
        $onus = Onu::with(['olt','lineProfile','serviceProfile'])->get();

        if ($request->wantsJson()) {
            return response()->json($onus);
        }

        return view('onus.index', compact('onus'));
    }

    public function create()
    {
        $olts = Olt::all();
        $lineProfiles = LineProfile::all();
        $serviceProfiles = ServiceProfile::all();

        return view('onus.create', compact('olts','lineProfiles','serviceProfiles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'olt_id'            => 'required|exists:olts,id',
            'serial_number'     => 'required|string|max:50|unique:onus,serial_number',
            'model'             => 'nullable|string|max:50',
            'pon_port'          => 'required|string|max:20',
            'line_profile_id'   => 'nullable|exists:line_profiles,id',
            'service_profile_id'=> 'nullable|exists:service_profiles,id',
            'status'            => 'required|in:registered,authenticated,online,down',
        ]);

        $onu = Onu::create($validated);

        if ($request->wantsJson()) {
            return response()->json($onu, 201);
        }

        return redirect()->route('onus.index')
                         ->with('success', 'ONU creada correctamente');
    }

    public function show(Request $request, Onu $onu)
    {
        if ($request->wantsJson()) {
            return response()->json($onu->load(['olt','lineProfile','serviceProfile']));
        }

        return view('onus.show', compact('onu'));
    }

    public function edit(Onu $onu)
    {
        $olts = Olt::all();
        $lineProfiles = LineProfile::all();
        $serviceProfiles = ServiceProfile::all();

        return view('onus.edit', compact('onu','olts','lineProfiles','serviceProfiles'));
    }

    public function update(Request $request, Onu $onu)
    {
        $validated = $request->validate([
            'serial_number'     => 'required|string|max:50|unique:onus,serial_number,'.$onu->id,
            'model'             => 'nullable|string|max:50',
            'pon_port'          => 'required|string|max:20',
            'line_profile_id'   => 'nullable|exists:line_profiles,id',
            'service_profile_id'=> 'nullable|exists:service_profiles,id',
            'status'            => 'required|in:registered,authenticated,online,down',
        ]);

        $onu->update($validated);

        if ($request->wantsJson()) {
            return response()->json($onu);
        }

        return redirect()->route('onus.index')
                         ->with('success', 'ONU actualizada correctamente');
    }

    public function destroy(Request $request, Onu $onu)
    {
        $onu->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('onus.index')
                         ->with('success', 'ONU eliminada correctamente');
    }

     /**
     * Generar PDF con listado de ONUs
     */
    public function generatePDF()
{
    $onus = Onu::with(['olt', 'customerAssignments.customer'])->get();

    $data = [
        'onus' => $onus,
        'title' => 'Reporte de ONUs',
        'date' => now()->format('d/m/Y H:i:s'),
        'totalOnus' => $onus->count(),
        'onlineOnus' => $onus->where('status', 'online')->count(),
        'offlineOnus' => $onus->where('status', 'offline')->count(),
        'registeredOnus' => $onus->where('status', 'registered')->count(),
        'assignedOnus' => $onus->where('has_customer', true)->count(), // Usando el helper
    ];

    $pdf = PDF::loadView('onus.pdf', $data);
    
    return $pdf->download('reporte-onus-' . now()->format('Y-m-d') . '.pdf');
}

    /**
     * Vista previa del PDF (HTML)
     */
    public function previewPDF()
    {
        $onus = Onu::with(['olt', 'customerAssignments.customer'])->get();

        $data = [
            'onus' => $onus,
            'title' => 'Reporte de ONUs',
            'date' => now()->format('d/m/Y H:i:s'),
            'totalOnus' => $onus->count(),
            'onlineOnus' => $onus->where('status', 'online')->count(),
            'offlineOnus' => $onus->where('status', 'offline')->count(),
            'registeredOnus' => $onus->where('status', 'registered')->count(),
            'assignedOnus' => $onus->filter(function($onu) {
                return $onu->customerAssignments->count() > 0;
            })->count(),
        ];

        return view('onus.pdf', $data);
    }

}
