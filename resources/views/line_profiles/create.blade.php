@extends('layouts.app')

@section('content')
<h2>Nuevo Perfil de Línea</h2>

<form method="POST" action="{{ route('line-profiles.store') }}">
    @csrf

    <!-- Selección de OLT -->
    <div class="mb-3">
        <label for="olt_id" class="form-label">OLT *</label>
        <select name="olt_id" id="olt_id" class="form-select @error('olt_id') is-invalid @enderror" required>
            <option value="">-- Seleccione una OLT --</option>
            @foreach($olts as $olt)
                <option value="{{ $olt->id }}" {{ old('olt_id') == $olt->id ? 'selected' : '' }}>
                    {{ $olt->name }}
                </option>
            @endforeach
        </select>
        @error('olt_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Nombre del perfil -->
    <div class="mb-3">
        <label for="name" class="form-label">Nombre *</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Asociación con perfil DBA -->
    <div class="mb-3">
        <label for="dba_profile_id" class="form-label">Perfil DBA</label>
        <select name="dba_profile_id" id="dba_profile_id" class="form-select @error('dba_profile_id') is-invalid @enderror">
            <option value="">-- Ninguno --</option>
            @foreach($dbaProfiles as $db)
                <option value="{{ $db->id }}" {{ old('dba_profile_id') == $db->id ? 'selected' : '' }}>
                    {{ $db->name }}
                </option>
            @endforeach
        </select>
        @error('dba_profile_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Configuración técnica -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="tcont" class="form-label">T-CONT *</label>
            <input type="number" name="tcont" id="tcont" value="{{ old('tcont') }}"
                   class="form-control @error('tcont') is-invalid @enderror" required>
            @error('tcont')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="gem_ports" class="form-label">GEM Ports *</label>
            <input type="number" name="gem_ports" id="gem_ports" value="{{ old('gem_ports') }}"
                   class="form-control @error('gem_ports') is-invalid @enderror" required>
            @error('gem_ports')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- VLAN asociada -->
    <div class="mb-3">
        <label for="vlan_id" class="form-label">VLAN</label>
        <select name="vlan_id" id="vlan_id" class="form-select @error('vlan_id') is-invalid @enderror">
            <option value="">-- Ninguna --</option>
            @foreach($vlans as $vlan)
                <option value="{{ $vlan->id }}" {{ old('vlan_id') == $vlan->id ? 'selected' : '' }}>
                    VLAN {{ $vlan->number }}
                </option>
            @endforeach
        </select>
        @error('vlan_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Botones de acción -->
    <div class="d-flex justify-content-between">
        <a href="{{ route('line-profiles.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>
@endsection
