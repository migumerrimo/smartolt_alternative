@extends('layouts.app')

@section('content')
<h2>Detalle Perfil de Servicio</h2>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>OLT:</strong> {{ $serviceProfile->olt->name }}</li>
    <li class="list-group-item"><strong>Nombre:</strong> {{ $serviceProfile->name }}</li>
    <li class="list-group-item"><strong>Servicio:</strong> {{ ucfirst($serviceProfile->service) }}</li>
    <li class="list-group-item"><strong>Puertos ETH:</strong> {{ $serviceProfile->eth_ports }}</li>
    <li class="list-group-item"><strong>VLAN:</strong> {{ optional($serviceProfile->vlan)->number }}</li>
</ul>

<a href="{{ route('service-profiles.index') }}" class="btn btn-secondary">Volver</a>
@endsection
