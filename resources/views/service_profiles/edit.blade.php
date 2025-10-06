@extends('layouts.app')

@section('content')
<h2>Editar Perfil de Servicio</h2>

<form method="POST" action="{{ route('service-profiles.update',$serviceProfile) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="name" value="{{ $serviceProfile->name }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Servicio</label>
        <select name="service" class="form-select" required>
            @foreach(['internet','voip','iptv','triple-play'] as $s)
                <option value="{{ $s }}" @if($serviceProfile->service==$s) selected @endif>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Puertos ETH</label>
        <input type="number" name="eth_ports" value="{{ $serviceProfile->eth_ports }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>VLAN</label>
        <select name="vlan_id" class="form-select">
            <option value="">-- Ninguna --</option>
            @foreach($vlans as $vlan)
                <option value="{{ $vlan->id }}" @if($serviceProfile->vlan_id==$vlan->id) selected @endif>
                    {{ $vlan->number }}
                </option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
