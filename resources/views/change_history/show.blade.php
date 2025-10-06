@extends('layouts.app')

@section('content')
<h2>Detalle de Cambio</h2>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>Usuario:</strong> {{ $changeHistory->user->name }}</li>
    <li class="list-group-item"><strong>OLT:</strong> {{ $changeHistory->olt->name }}</li>
    <li class="list-group-item"><strong>Dispositivo:</strong> {{ $changeHistory->device_type }} - {{ $changeHistory->device_name }}</li>
    <li class="list-group-item"><strong>Comando:</strong> {{ $changeHistory->command }}</li>
    <li class="list-group-item"><strong>Resultado:</strong> {{ $changeHistory->result }}</li>
    <li class="list-group-item"><strong>Descripción:</strong> {{ $changeHistory->description }}</li>
    <li class="list-group-item"><strong>Fecha:</strong> {{ $changeHistory->date }}</li>
</ul>

<a href="{{ route('change-history.index') }}" class="btn btn-secondary">Volver</a>
@endsection
