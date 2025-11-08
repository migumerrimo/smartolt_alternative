@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detalle del Perfil DBA</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('dba-profiles.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-diagram-3"></i> Información del Perfil
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush mb-3">
            <li class="list-group-item"><strong>OLT:</strong> {{ $dbaProfile->olt->name }}</li>
            <li class="list-group-item"><strong>Nombre:</strong> {{ $dbaProfile->name }}</li>
            <li class="list-group-item"><strong>Tipo:</strong> {{ ucfirst($dbaProfile->type) }}</li>
            <li class="list-group-item"><strong>Ancho de Banda Máximo:</strong> {{ $dbaProfile->max_bandwidth }} Mbps</li>
        </ul>

        <div class="d-flex justify-content-end">
            <a href="{{ route('dba-profiles.edit', $dbaProfile) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('dba-profiles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>
@endsection
