@extends('layouts.app')

@section('content')
<h2>Detalle Métrica de Telemetría</h2>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>OLT:</strong> {{ $telemetry->olt->name }}</li>
    <li class="list-group-item"><strong>ONU:</strong> {{ optional($telemetry->onu)->serial_number }}</li>
    <li class="list-group-item"><strong>Métrica:</strong> {{ $telemetry->metric }}</li>
    <li class="list-group-item"><strong>Valor:</strong> {{ $telemetry->value }}</li>
    <li class="list-group-item"><strong>Unidad:</strong> {{ $telemetry->unit }}</li>
    <li class="list-group-item"><strong>Fecha:</strong> {{ $telemetry->sampled_at }}</li>
</ul>

<a href="{{ route('telemetry.index') }}" class="btn btn-secondary">Volver</a>
@endsection
