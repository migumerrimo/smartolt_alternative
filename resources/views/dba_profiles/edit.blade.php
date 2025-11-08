@extends('layouts.app')

@section('content')
<h2 class="mb-4">Editar Perfil DBA</h2>

<form method="POST" action="{{ route('dba-profiles.update', $dbaProfile) }}">
    @csrf 
    @method('PUT')

    <!-- Campo: Nombre del perfil -->
    <div class="mb-3">
        <label for="name" class="form-label">Nombre</label>
        <input 
            type="text" 
            id="name"
            name="name" 
            value="{{ old('name', $dbaProfile->name) }}" 
            class="form-control @error('name') is-invalid @enderror" 
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Campo: Tipo de perfil -->
    <div class="mb-3">
        <label for="type" class="form-label">Tipo</label>
        <select 
            id="type"
            name="type" 
            class="form-select @error('type') is-invalid @enderror" 
            required
        >
            @foreach(['type1','type2','type3','type4'] as $type)
                <option value="{{ $type }}" {{ old('type', $dbaProfile->type) == $type ? 'selected' : '' }}>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Campo: Ancho de banda máximo -->
    <div class="mb-3">
        <label for="max_bandwidth" class="form-label">Ancho de Banda Máximo (Mbps)</label>
        <input 
            type="number" 
            id="max_bandwidth"
            name="max_bandwidth" 
            value="{{ old('max_bandwidth', $dbaProfile->max_bandwidth) }}" 
            class="form-control @error('max_bandwidth') is-invalid @enderror" 
            min="1" 
            required
        >
        @error('max_bandwidth')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Botones de acción -->
    <div class="d-flex justify-content-between">
        <a href="{{ route('dba-profiles.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Actualizar Perfil
        </button>
    </div>
</form>
@endsection
