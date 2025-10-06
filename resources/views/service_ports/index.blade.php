@extends('layouts.app')

@section('content')
<h2>Service Ports</h2>
<a href="{{ route('service-ports.create') }}" class="btn btn-primary mb-3">Nuevo Service Port</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>OLT</th>
            <th>ONU</th>
            <th>VLAN</th>
            <th>Traffic Table</th>
            <th>Gemport</th>
            <th>Tipo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($servicePorts as $sp)
        <tr>
            <td>{{ $sp->id }}</td>
            <td>{{ $sp->olt->name }}</td>
            <td>{{ $sp->onu->serial_number }}</td>
            <td>{{ $sp->vlan->number }}</td>
            <td>{{ optional($sp->trafficTable)->name }}</td>
            <td>{{ $sp->gemport_id }}</td>
            <td>{{ strtoupper($sp->type) }}</td>
            <td>
                <a href="{{ route('service-ports.show',$sp) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('service-ports.edit',$sp) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('service-ports.destroy',$sp) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este Service Port?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
