@extends('layouts.app')

@section('content')
<h2>Editar Perfil de Línea</h2>

<form method="POST" action="{{ route('line-profiles.update',$lineProfile) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="name" class="form-control" value="{{ $lineProfile->name }}" required>
    </div>
    <div class="mb-3">
        <label>DBA Profile</label>
        <select name="dba_profile_id" class="form-select">
            <option value="">-- Ninguno --</option>
            @foreach($dbaProfiles as $db)
                <option value="{{ $db->id }}" @if($lineProfile->dba_profile_id==$db->id) selected @endif>
                    {{ $db->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>T-CONT</label>
        <input type="number" name="tcont" class="form-control" value="{{ $lineProfile->tcont }}" required>
    </div>
    <div class="mb-3">
        <label>GEM Ports</label>
        <input type="number" name="gem_ports" class="form-control" value="{{ $lineProfile->gem_ports }}" required>
    </div>
    <div class="mb-3">
        <label>VLAN</label>
        <select name="vlan_id" class="form-select">
            <option value="">-- Ninguna --</option>
            @foreach($vlans as $vlan)
                <option value="{{ $vlan->id }}" @if($lineProfile->vlan_id==$vlan->id) selected @endif>
                    {{ $vlan->number }}
                </option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
