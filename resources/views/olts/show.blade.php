@extends('layouts.app')

@section('content')
<h2>Detalle OLT</h2>

<ul class="list-group mb-3">
    <li class="list-group-item"><strong>Nombre:</strong> {{ $olt->name }}</li>
    <li class="list-group-item"><strong>Modelo:</strong> {{ $olt->model }}</li>
    <li class="list-group-item"><strong>Vendor:</strong> {{ $olt->vendor }}</li>
    <li class="list-group-item"><strong>IP:</strong> {{ $olt->management_ip }}</li>
    <li class="list-group-item"><strong>Estado:</strong> {{ $olt->status }}</li>
</ul>

<a href="{{ route('olts.index') }}" class="btn btn-secondary">Volver</a>
<a href="{{ route('olts.edit', $olt) }}" class="btn btn-warning">Editar</a>
<form action="{{ route('olts.destroy', $olt) }}" method="POST" class="d-inline">
    @csrf @method('DELETE')
    <button class="btn btn-danger" onclick="return confirm('¿Eliminar?')">Borrar</button>
</form>
@endsection
