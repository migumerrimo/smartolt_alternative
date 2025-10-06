@extends('layouts.app')

@section('content')
<h2>Nuevo Perfil de Línea</h2>

<form method="POST" action="{{ route('line-profiles.store') }}">
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
        <label>DBA Profile</label>
        <select name="dba_profile_id" class="form-select">
            <option value="">-- Ninguno --</option>
            @foreach($dbaProfiles as $db)
                <option value="{{ $db->id }}">{{ $db->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>T-CONT</label>
        <input type="number" name="tcont" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>GEM Ports</label>
        <input type="number" name="gem_ports" class="form-control" required>
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
