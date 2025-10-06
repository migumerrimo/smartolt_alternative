@extends('layouts.app')

@section('content')
<h2>ONUs</h2>
<a href="{{ route('onus.create') }}" class="btn btn-primary mb-3">Nueva ONU</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Serial</th>
            <th>Modelo</th>
            <th>PON Port</th>
            <th>Estado</th>
            <th>OLT</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($onus as $onu)
        <tr>
            <td>{{ $onu->serial_number }}</td>
            <td>{{ $onu->model }}</td>
            <td>{{ $onu->pon_port }}</td>
            <td>{{ $onu->status }}</td>
            <td>{{ $onu->olt->name }}</td>
            <td>
                <a href="{{ route('onus.show', $onu) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('onus.edit', $onu) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('onus.destroy', $onu) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
