<?php
// app/Http/Controllers/OltController.php
namespace App\Http\Controllers;

use App\Models\Olt;
use Illuminate\Http\Request;
use PDF;

class OltController extends Controller
{
    public function index(Request $request)
    {
        $olts = Olt::all();
        if ($request->wantsJson()) return response()->json($olts);
        return view('olts.index', compact('olts'));
    }

    public function create() { return view('olts.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=>'required|string|max:100',
            'model'=>'nullable|string|max:50',
            'vendor'=>'required|in:Huawei,ZTE,FiberHome,Other',
            'management_ip' => 'required|ip|unique:olts,management_ip',
            'location'=>'nullable|string|max:255',
            'firmware'=>'nullable|string|max:50',
            'status'=>'required|in:active,inactive',
        ]);
        $olt = Olt::create($validated);
        if ($request->wantsJson()) return response()->json($olt,201);
        return redirect()->route('olts.index')->with('success','OLT creada');
    }

    public function show(Request $request, Olt $olt)
    {
        if ($request->wantsJson()) return response()->json($olt);
        return view('olts.show', compact('olt'));
    }

    public function edit(Olt $olt) { return view('olts.edit', compact('olt')); }

    public function update(Request $request, Olt $olt)
    {
        $validated = $request->validate([
            'name'=>'required|string|max:100',
            'model'=>'nullable|string|max:50',
            'vendor'=>'required|in:Huawei,ZTE,FiberHome,Other',
            'management_ip' => 'required|ip|unique:olts,management_ip,' . $olt->id,
            'location'=>'nullable|string|max:255',
            'firmware'=>'nullable|string|max:50',
            'status'=>'required|in:active,inactive',
        ]);
        $olt->update($validated);
        if ($request->wantsJson()) return response()->json($olt);
        return redirect()->route('olts.index')->with('success','OLT actualizada');
    }

    public function destroy(Request $request, Olt $olt)
    {
        $olt->delete();
        if ($request->wantsJson()) return response()->json(null,204);
        return redirect()->route('olts.index')->with('success','OLT eliminada');
    }

     /**
     * Generar PDF con listado de OLTs
     */
    public function generatePDF()
    {
        $olts = Olt::withCount(['onus as online_onus_count' => function($query) {
                    $query->where('status', 'online');
                }])
                ->withCount('onus as total_onus_count')
                ->get();

        $data = [
            'olts' => $olts,
            'title' => 'Reporte de OLTs',
            'date' => now()->format('d/m/Y H:i:s'),
            'totalOlts' => $olts->count(),
            'activeOlts' => $olts->where('status', 'active')->count(),
            'totalOnus' => $olts->sum('total_onus_count'),
            'onlineOnus' => $olts->sum('online_onus_count'),
        ];

        $pdf = PDF::loadView('olts.pdf', $data);
        
        return $pdf->download('reporte-olts-' . now()->format('Y-m-d') . '.pdf');
    }

    public function previewPDF()
    {
        $olts = Olt::withCount(['onus as online_onus_count' => function($query) {
                    $query->where('status', 'online');
                }])
                ->withCount('onus as total_onus_count')
                ->get();

        $data = [
            'olts' => $olts,
            'title' => 'Reporte de OLTs',
            'date' => now()->format('d/m/Y H:i:s'),
            'totalOlts' => $olts->count(),
            'activeOlts' => $olts->where('status', 'active')->count(),
            'totalOnus' => $olts->sum('total_onus_count'),
            'onlineOnus' => $olts->sum('online_onus_count'),
        ];

        return view('olts.pdf', $data);
    }

}
