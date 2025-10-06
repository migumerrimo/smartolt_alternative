@extends('layouts.app')

@section('content')
<h2>Configuraciones de Dispositivos</h2>
<a href="{{ route('device-configs.create') }}" class="btn btn-primary mb-3">Nueva Configuración</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>OLT</th>
            <th>Dispositivo</th>
            <th>Versión</th>
            <th>Aplicado por</th>
            <th>Aplicado en</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($configs as $config)
        <tr>
            <td>{{ $config->id }}</td>
            <td>{{ $config->olt->name }}</td>
            <td>{{ $config->device_type }} - {{ $config->device_name }}</td>
            <td>{{ $config->version }}</td>
            <td>{{ optional($config->appliedBy)->name }}</td>
            <td>{{ $config->applied_at }}</td>
            <td>
                <a href="{{ route('device-configs.show',$config) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('device-configs.edit',$config) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('device-configs.destroy',$config) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar configuración?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
