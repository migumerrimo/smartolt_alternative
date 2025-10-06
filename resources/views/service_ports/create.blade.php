@extends('layouts.app')

@section('content')
<h2>Nuevo Service Port</h2>

<form method="POST" action="{{ route('service-ports.store') }}">
    @csrf
    <div class="mb-3">
        <label>OLT</label>
        <select name="olt_id" class="form-select" required>
            @foreach($olts as $olt)
                <option value="{{ $olt->id }}">{{ $olt->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>ONU</label>
        <select name="onu_id" class="form-select" required>
            @foreach($onus as $onu)
                <option value="{{ $onu->id }}">{{ $onu->serial_number }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>VLAN</label>
        <select name="vlan_id" class="form-select" required>
            @foreach($vlans as $vlan)
                <option value="{{ $vlan->id }}">{{ $vlan->number }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Traffic Table</label>
        <select name="traffic_table_id" class="form-select">
            <option value="">-- Ninguna --</option>
            @foreach($trafficTables as $tt)
                <option value="{{ $tt->id }}">{{ $tt->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Gemport ID</label>
        <input type="number" name="gemport_id" class="form-control">
    </div>
    <div class="mb-3">
        <label>Tipo</label>
        <select name="type" class="form-select" required>
            <option value="gpon">GPON</option>
            <option value="eth">ETH</option>
            <option value="epon">EPON</option>
        </select>
    </div>
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
