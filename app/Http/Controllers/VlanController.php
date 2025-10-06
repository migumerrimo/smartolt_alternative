<?php
// app/Http/Controllers/VlanController.php
namespace App\Http\Controllers;

use App\Models\Vlan;
use App\Models\Olt;
use Illuminate\Http\Request;

class VlanController extends Controller
{
    public function index(Request $request)
    {
        $vlans = Vlan::with('olt')->get();
        if ($request->wantsJson()) return response()->json($vlans);
        return view('vlans.index', compact('vlans'));
    }

    public function create()
    {
        $olts = Olt::all();
        return view('vlans.create', compact('olts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'olt_id'=>'required|exists:olts,id',
            'number'=>'required|integer',
            'type'=>'required|in:standard,smart,mux,super',
            'description'=>'nullable|string|max:255',
            'uplink_port'=>'nullable|string|max:20',
        ]);
        $vlan = Vlan::create($validated);
        if ($request->wantsJson()) return response()->json($vlan,201);
        return redirect()->route('vlans.index')->with('success','VLAN creada');
    }

    public function show(Request $request, Vlan $vlan)
    {
        if ($request->wantsJson()) return response()->json($vlan->load('olt'));
        return view('vlans.show', compact('vlan'));
    }

    public function edit(Vlan $vlan)
    {
        $olts = Olt::all();
        return view('vlans.edit', compact('vlan','olts'));
    }

    public function update(Request $request, Vlan $vlan)
    {
        $validated = $request->validate([
            'number'=>'required|integer',
            'type'=>'required|in:standard,smart,mux,super',
            'description'=>'nullable|string|max:255',
            'uplink_port'=>'nullable|string|max:20',
        ]);
        $vlan->update($validated);
        if ($request->wantsJson()) return response()->json($vlan);
        return redirect()->route('vlans.index')->with('success','VLAN actualizada');
    }

    public function destroy(Request $request, Vlan $vlan)
    {
        $vlan->delete();
        if ($request->wantsJson()) return response()->json(null,204);
        return redirect()->route('vlans.index')->with('success','VLAN eliminada');
    }
}
