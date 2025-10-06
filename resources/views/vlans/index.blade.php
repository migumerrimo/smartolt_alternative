@extends('layouts.app')

@section('content')
<h2>Configuración de VLANs</h2>
<a href="{{ route('vlans.create') }}" class="btn btn-primary mb-3">Nueva VLAN</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>OLT</th>
            <th>Número</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vlans as $vlan)
        <tr>
            <td>{{ $vlan->id }}</td>
            <td>{{ $vlan->olt->name }}</td>
            <td>{{ $vlan->number }}</td>
            <td>{{ $vlan->type }}</td>
            <td>{{ $vlan->description }}</td>
            <td>
                <a href="{{ route('vlans.show', $vlan) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('vlans.edit', $vlan) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('vlans.destroy', $vlan) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar VLAN?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
