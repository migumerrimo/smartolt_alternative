@extends('layouts.app')

@section('content')
<h2>Tablas de Tráfico</h2>
<a href="{{ route('traffic-tables.create') }}" class="btn btn-primary mb-3">Nueva Tabla de Tráfico</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>OLT</th>
            <th>Nombre</th>
            <th>CIR</th>
            <th>PIR</th>
            <th>Prioridad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($trafficTables as $table)
        <tr>
            <td>{{ $table->id }}</td>
            <td>{{ $table->olt->name }}</td>
            <td>{{ $table->name }}</td>
            <td>{{ $table->cir }}</td>
            <td>{{ $table->pir }}</td>
            <td>{{ $table->priority }}</td>
            <td>
                <a href="{{ route('traffic-tables.show',$table) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('traffic-tables.edit',$table) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('traffic-tables.destroy',$table) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
