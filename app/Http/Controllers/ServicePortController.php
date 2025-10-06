<?php

namespace App\Http\Controllers;

use App\Models\ServicePort;
use App\Models\Olt;
use App\Models\Onu;
use App\Models\Vlan;
use App\Models\TrafficTable;
use Illuminate\Http\Request;

class ServicePortController extends Controller
{
    public function index(Request $request)
    {
        $servicePorts = ServicePort::with(['olt','onu','vlan','trafficTable'])->get();

        if ($request->wantsJson()) {
            return response()->json($servicePorts);
        }

        return view('service_ports.index', compact('servicePorts'));
    }

    public function create()
    {
        $olts = Olt::all();
        $onus = Onu::all();
        $vlans = Vlan::all();
        $trafficTables = TrafficTable::all();

        return view('service_ports.create', compact('olts','onus','vlans','trafficTables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'olt_id'          => 'required|exists:olts,id',
            'onu_id'          => 'required|exists:onus,id',
            'vlan_id'         => 'required|exists:vlans,id',
            'traffic_table_id'=> 'nullable|exists:traffic_table,id',
            'gemport_id'      => 'nullable|integer',
            'type'            => 'required|in:gpon,eth,epon',
        ]);

        $servicePort = ServicePort::create($validated);

        if ($request->wantsJson()) {
            return response()->json($servicePort, 201);
        }

        return redirect()->route('service-ports.index')
                         ->with('success', 'Service Port creado correctamente');
    }

    public function show(Request $request, ServicePort $servicePort)
    {
        if ($request->wantsJson()) {
            return response()->json($servicePort->load(['olt','onu','vlan','trafficTable']));
        }

        return view('service_ports.show', compact('servicePort'));
    }

    public function edit(ServicePort $servicePort)
    {
        $olts = Olt::all();
        $onus = Onu::all();
        $vlans = Vlan::all();
        $trafficTables = TrafficTable::all();

        return view('service_ports.edit', compact('servicePort','olts','onus','vlans','trafficTables'));
    }

    public function update(Request $request, ServicePort $servicePort)
    {
        $validated = $request->validate([
            'onu_id'          => 'required|exists:onus,id',
            'vlan_id'         => 'required|exists:vlans,id',
            'traffic_table_id'=> 'nullable|exists:traffic_table,id',
            'gemport_id'      => 'nullable|integer',
            'type'            => 'required|in:gpon,eth,epon',
        ]);

        $servicePort->update($validated);

        if ($request->wantsJson()) {
            return response()->json($servicePort);
        }

        return redirect()->route('service-ports.index')
                         ->with('success', 'Service Port actualizado correctamente');
    }

    public function destroy(Request $request, ServicePort $servicePort)
    {
        $servicePort->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('service-ports.index')
                         ->with('success', 'Service Port eliminado correctamente');
    }
}
