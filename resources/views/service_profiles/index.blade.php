@extends('layouts.app')

@section('content')
<h2>Perfiles de Servicio</h2>
<a href="{{ route('service-profiles.create') }}" class="btn btn-primary mb-3">Nuevo Perfil de Servicio</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>OLT</th>
            <th>Nombre</th>
            <th>Servicio</th>
            <th>Puertos ETH</th>
            <th>VLAN</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($serviceProfiles as $profile)
        <tr>
            <td>{{ $profile->id }}</td>
            <td>{{ $profile->olt->name }}</td>
            <td>{{ $profile->name }}</td>
            <td>{{ ucfirst($profile->service) }}</td>
            <td>{{ $profile->eth_ports }}</td>
            <td>{{ optional($profile->vlan)->number }}</td>
            <td>
                <a href="{{ route('service-profiles.show',$profile) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('service-profiles.edit',$profile) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('service-profiles.destroy',$profile) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
