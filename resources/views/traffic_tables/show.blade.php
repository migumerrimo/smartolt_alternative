@extends('layouts.app')

@section('content')
<h2>Detalle de Tabla de Tráfico</h2>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>OLT:</strong> {{ $trafficTable->olt->name }}</li>
    <li class="list-group-item"><strong>Nombre:</strong> {{ $trafficTable->name }}</li>
    <li class="list-group-item"><strong>CIR:</strong> {{ $trafficTable->cir }}</li>
    <li class="list-group-item"><strong>PIR:</strong> {{ $trafficTable->pir }}</li>
    <li class="list-group-item"><strong>Prioridad:</strong> {{ $trafficTable->priority }}</li>
</ul>

<a href="{{ route('traffic-tables.index') }}" class="btn btn-secondary">Volver</a>
@endsection
