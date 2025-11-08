@extends('layouts.app')

@section('content')

<!-- Sección principal: vista detallada de un registro específico del historial de cambios -->
<h2>Detalle de Cambio</h2>

<!-- Lista con toda la información del registro de cambio -->
<ul class="list-group mb-3">
    <li class="list-group-item"><strong>Usuario:</strong> {{ $changeHistory->user->name }}</li>
    <li class="list-group-item"><strong>OLT:</strong> {{ $changeHistory->olt->name }}</li>
    <li class="list-group-item"><strong>Dispositivo:</strong> {{ $changeHistory->device_type }} - {{ $changeHistory->device_name }}</li>
    <li class="list-group-item"><strong>Comando:</strong> {{ $changeHistory->command }}</li>
    <li class="list-group-item"><strong>Resultado:</strong> {{ $changeHistory->result }}</li>
    <li class="list-group-item"><strong>Descripción:</strong> {{ $changeHistory->description }}</li>
    <li class="list-group-item"><strong>Fecha:</strong> {{ $changeHistory->date }}</li>
</ul>

<!-- Botón para regresar al listado de registros del historial de cambios -->
<a href="{{ route('change-history.index') }}" class="btn btn-secondary">Volver</a>
@endsection
