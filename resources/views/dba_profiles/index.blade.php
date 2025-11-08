@extends('layouts.app')

@section('content')

<!-- Encabezado de la página y botón para crear un nuevo perfil DBA -->
<h2>Perfiles DBA</h2>
<a href="{{ route('dba-profiles.create') }}" class="btn btn-primary mb-3">Nuevo Perfil DBA</a>

<!-- Tabla de listado de perfiles DBA -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>OLT</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Max Bandwidth</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <!-- Iteración sobre la colección de perfiles DBA -->
        @foreach($dbaProfiles as $profile)
        <tr>
            <!-- Identificador del perfil -->
            <td>{{ $profile->id }}</td>

            <!-- Nombre de la OLT asociada -->
            <td>{{ $profile->olt->name }}</td>

            <!-- Nombre del perfil DBA -->
            <td>{{ $profile->name }}</td>

            <!-- Tipo de perfil DBA -->
            <td>{{ $profile->type }}</td>

            <!-- Ancho de banda máximo permitido -->
            <td>{{ $profile->max_bandwidth }}</td>

            <!-- Botones de acción: ver, editar y eliminar -->
            <td>
                <a href="{{ route('dba-profiles.show',$profile) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('dba-profiles.edit',$profile) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('dba-profiles.destroy',$profile) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

