@extends('layouts.app')

@section('content')

<!-- Sección principal: vista detallada de una alarma específica -->
<h2>Detalle de Alarma</h2>

<!-- Lista con la información completa de la alarma -->
<ul class="list-group mb-3">
    <li class="list-group-item"><strong>OLT:</strong> {{ $alarm->olt->name }}</li>
    <li class="list-group-item"><strong>ONU:</strong> {{ optional($alarm->onu)->serial_number }}</li>
    <li class="list-group-item"><strong>Severidad:</strong> {{ ucfirst($alarm->severity) }}</li>
    <li class="list-group-item"><strong>Mensaje:</strong> {{ $alarm->message }}</li>
    <li class="list-group-item"><strong>Estado:</strong> {{ $alarm->active ? 'Activa' : 'Inactiva' }}</li>
    <li class="list-group-item"><strong>Detectada:</strong> {{ $alarm->detected_at }}</li>
</ul>

<!-- Botón para regresar al listado general de alarmas -->
<a href="{{ route('alarms.index') }}" class="btn btn-secondary">Volver</a>
@endsection
