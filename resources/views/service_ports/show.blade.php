@extends('layouts.app')

@section('content')
<h2>Detalle Service Port</h2>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>OLT:</strong> {{ $servicePort->olt->name }}</li>
    <li class="list-group-item"><strong>ONU:</strong> {{ $servicePort->onu->serial_number }}</li>
    <li class="list-group-item"><strong>VLAN:</strong> {{ $servicePort->vlan->number }}</li>
    <li class="list-group-item"><strong>Traffic Table:</strong> {{ optional($servicePort->trafficTable)->name }}</li>
    <li class="list-group-item"><strong>Gemport ID:</strong> {{ $servicePort->gemport_id }}</li>
    <li class="list-group-item"><strong>Tipo:</strong> {{ strtoupper($servicePort->type) }}</li>
</ul>

<a href="{{ route('service-ports.index') }}" class="btn btn-secondary">Volver</a>
@endsection
