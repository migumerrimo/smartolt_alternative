@extends('layouts.app')

@section('content')

<!-- Título principal de la vista -->
<h2>Configuraciones de Dispositivos</h2>

<!-- Botón para crear una nueva configuración -->
<a href="{{ route('device-configs.create') }}" class="btn btn-primary mb-3">Nueva Configuración</a>

<!-- Tabla que lista todas las configuraciones de dispositivos -->
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

    <!-- Cuerpo de la tabla con datos de configuraciones -->
    <tbody>
        @foreach($configs as $config)
        <tr>
            <td>{{ $config->id }}</td>
            <td>{{ $config->olt->name }}</td>
            <td>{{ $config->device_type }} - {{ $config->device_name }}</td>
            <td>{{ $config->version }}</td>
            <td>{{ optional($config->appliedBy)->name }}</td>
            <td>{{ $config->applied_at }}</td>

            <!-- Botones de acción: ver, editar y eliminar -->
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

