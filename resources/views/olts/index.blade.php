@extends('layouts.app')

@section('content')
<h2>OLTs</h2>
<a href="{{ route('olts.create') }}" class="btn btn-primary mb-3">Nueva OLT</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Modelo</th>
            <th>IP</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($olts as $olt)
        <tr>
            <td>{{ $olt->id }}</td>
            <td>{{ $olt->name }}</td>
            <td>{{ $olt->model }}</td>
            <td>{{ $olt->management_ip }}</td>
            <td>{{ $olt->status }}</td>
            <td>
                <a href="{{ route('olts.show', $olt) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('olts.edit', $olt) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('olts.destroy', $olt) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
