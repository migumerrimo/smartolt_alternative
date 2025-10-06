@extends('layouts.app')

@section('content')
<h2>Perfiles DBA</h2>
<a href="{{ route('dba-profiles.create') }}" class="btn btn-primary mb-3">Nuevo Perfil DBA</a>

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
        @foreach($dbaProfiles as $profile)
        <tr>
            <td>{{ $profile->id }}</td>
            <td>{{ $profile->olt->name }}</td>
            <td>{{ $profile->name }}</td>
            <td>{{ $profile->type }}</td>
            <td>{{ $profile->max_bandwidth }}</td>
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
