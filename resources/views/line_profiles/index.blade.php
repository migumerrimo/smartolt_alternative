@extends('layouts.app')

@section('content')
<h2>Perfiles de Línea</h2>
<a href="{{ route('line-profiles.create') }}" class="btn btn-primary mb-3">Nuevo Perfil de Línea</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>OLT</th>
            <th>Nombre</th>
            <th>DBA Profile</th>
            <th>T-CONT</th>
            <th>GEM Ports</th>
            <th>VLAN</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lineProfiles as $profile)
        <tr>
            <td>{{ $profile->id }}</td>
            <td>{{ $profile->olt->name }}</td>
            <td>{{ $profile->name }}</td>
            <td>{{ optional($profile->dbaProfile)->name }}</td>
            <td>{{ $profile->tcont }}</td>
            <td>{{ $profile->gem_ports }}</td>
            <td>{{ optional($profile->vlan)->number }}</td>
            <td>
                <a href="{{ route('line-profiles.show',$profile) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('line-profiles.edit',$profile) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('line-profiles.destroy',$profile) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
