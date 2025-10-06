@extends('layouts.app')

@section('content')
<h2>Detalle de Configuración de Dispositivo</h2>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>OLT:</strong> {{ $deviceConfig->olt->name }}</li>
    <li class="list-group-item"><strong>Tipo de Dispositivo:</strong> {{ $deviceConfig->device_type }}</li>
    <li class="list-group-item"><strong>Nombre:</strong> {{ $deviceConfig->device_name }}</li>
    <li class="list-group-item"><strong>Configuración:</strong><br><pre>{{ $deviceConfig->config_text }}</pre></li>
    <li class="list-group-item"><strong>Versión:</strong> {{ $deviceConfig->version }}</li>
    <li class="list-group-item"><strong>Aplicado por:</strong> {{ optional($deviceConfig->appliedBy)->name }}</li>
    <li class="list-group-item"><strong>Aplicado en:</strong> {{ $deviceConfig->applied_at }}</li>
</ul>

<a href="{{ route('device-configs.index') }}" class="btn btn-secondary">Volver</a>
@endsection
