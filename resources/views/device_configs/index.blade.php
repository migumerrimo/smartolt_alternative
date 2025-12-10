@extends('layouts.app')

@section('content')

<!-- Título principal de la vista -->
<h2>Configuraciones de Dispositivos</h2>

<!-- Botón para crear una nueva configuración -->
<a href="{{ route('device-configs.create') }}" class="btn btn-config-new mb-3">Nueva Configuración</a>

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
                <a href="{{ route('device-configs.show',$config) }}" class="btn btn-sm btn-config-view">Ver</a>
                <a href="{{ route('device-configs.edit',$config) }}" class="btn btn-sm btn-config-edit">Editar</a>
                <form action="{{ route('device-configs.destroy',$config) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-config-delete" onclick="return confirm('¿Eliminar configuración?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

<style>
/* Tonos personalizados para distinguir botones (mayor especificidad) */
.btn-config-new {
    background-color: #00C853 !important; /* verde principal */
    border-color: #00C853 !important;
    color: #fff !important;
}
.btn-config-new:hover {
    background-color: #009b3a !important;
    border-color: #009b3a !important;
}

/* Ver: teal distinto */
.btn-config-view {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
    color: #fff !important;
}
.btn-config-view:hover {
    background-color: #138f99 !important;
    border-color: #138f99 !important;
}

/* Editar: amarillo/amber */
.btn-config-edit {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #212529 !important;
}
.btn-config-edit:hover {
    background-color: #e0a800 !important;
    border-color: #e0a800 !important;
}

/* Borrar: rojo */
.btn-config-delete {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #fff !important;
}
.btn-config-delete:hover {
    background-color: #b02a37 !important;
    border-color: #b02a37 !important;
}

/* Ajustes comunes */
.btn-config-new,
.btn-config-view,
.btn-config-edit,
.btn-config-delete {
    box-shadow: none !important;
    text-decoration: none !important;
}
</style>

