@extends('layouts.app')

@section('content')
<h2>Telemetría</h2>
<a href="{{ route('telemetry.create') }}" class="btn btn-primary mb-3">Nueva Métrica</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>OLT</th>
            <th>ONU</th>
            <th>Métrica</th>
            <th>Valor</th>
            <th>Unidad</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($telemetry as $t)
        <tr>
            <td>{{ $t->id }}</td>
            <td>{{ $t->olt->name }}</td>
            <td>{{ optional($t->onu)->serial_number }}</td>
            <td>{{ $t->metric }}</td>
            <td>{{ $t->value }}</td>
            <td>{{ $t->unit }}</td>
            <td>{{ $t->sampled_at }}</td>
            <td>
                <a href="{{ route('telemetry.show',$t) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('telemetry.edit',$t) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('telemetry.destroy',$t) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta métrica?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
