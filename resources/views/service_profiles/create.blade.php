@extends('layouts.app')

@section('content')
<h2>Nuevo Perfil de Servicio</h2>

<form method="POST" action="{{ route('service-profiles.store') }}">
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
        <label>Nombre</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Servicio</label>
        <select name="service" class="form-select" required>
            <option value="internet">Internet</option>
            <option value="voip">VoIP</option>
            <option value="iptv">IPTV</option>
            <option value="triple-play">Triple Play</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Puertos ETH</label>
        <input type="number" name="eth_ports" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>VLAN</label>
        <select name="vlan_id" class="form-select">
            <option value="">-- Ninguna --</option>
            @foreach($vlans as $vlan)
                <option value="{{ $vlan->id }}">{{ $vlan->number }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
