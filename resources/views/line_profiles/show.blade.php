@extends('layouts.app')

@section('content')
<h2>Detalle Perfil de Línea</h2>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>OLT:</strong> {{ $lineProfile->olt->name }}</li>
    <li class="list-group-item"><strong>Nombre:</strong> {{ $lineProfile->name }}</li>
    <li class="list-group-item"><strong>DBA Profile:</strong> {{ optional($lineProfile->dbaProfile)->name }}</li>
    <li class="list-group-item"><strong>T-CONT:</strong> {{ $lineProfile->tcont }}</li>
    <li class="list-group-item"><strong>GEM Ports:</strong> {{ $lineProfile->gem_ports }}</li>
    <li class="list-group-item"><strong>VLAN:</strong> {{ optional($lineProfile->vlan)->number }}</li>
</ul>

<a href="{{ route('line-profiles.index') }}" class="btn btn-secondary">Volver</a>
@endsection
