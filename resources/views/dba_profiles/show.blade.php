@extends('layouts.app')

@section('content')
<h2>Detalle Perfil DBA</h2>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>OLT:</strong> {{ $dbaProfile->olt->name }}</li>
    <li class="list-group-item"><strong>Nombre:</strong> {{ $dbaProfile->name }}</li>
    <li class="list-group-item"><strong>Tipo:</strong> {{ $dbaProfile->type }}</li>
    <li class="list-group-item"><strong>Max Bandwidth:</strong> {{ $dbaProfile->max_bandwidth }}</li>
</ul>

<a href="{{ route('dba-profiles.index') }}" class="btn btn-secondary">Volver</a>
@endsection
