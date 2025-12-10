@extends('layouts.app')

@section('content')
<h2>Telemetría</h2>
<a href="{{ route('telemetry.create') }}" class="btn btn-telemetry-new mb-3">Nueva Métrica</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>OLT</th>
            <th>ONU</th>
            <th>Métrica</th>
            <th>Valor</th>
            <th>Unidad</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($telemetry as $t)
        <tr>
            <td>{{ $t->id }}</td>
            <td>{{ $t->olt->name }}</td>
            <td>{{ optional($t->onu)->serial_number }}</td>
            <td>{{ $t->metric }}</td>
            <td>{{ $t->value }}</td>
            <td>{{ $t->unit }}</td>
            <td>{{ $t->sampled_at }}</td>
            <td>
                <a href="{{ route('telemetry.show',$t) }}" class="btn btn-sm btn-telemetry-view">Ver</a>
                <a href="{{ route('telemetry.edit',$t) }}" class="btn btn-sm btn-telemetry-edit">Editar</a>
                <form action="{{ route('telemetry.destroy',$t) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-telemetry-delete" onclick="return confirm('¿Eliminar esta métrica?')">Borrar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

<style>
/* Tonos personalizados y con mayor especificidad para sobreescribir Bootstrap */
.btn-telemetry-new {
    background-color: #00C853 !important; /* verde principal */
    border-color: #00C853 !important;
    color: #fff !important;
}
.btn-telemetry-new:hover {
    background-color: #009b3a !important;
    border-color: #009b3a !important;
}

/* Ver: tono teal (distinto) */
.btn-telemetry-view {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
    color: #fff !important;
}
.btn-telemetry-view:hover {
    background-color: #138f99 !important;
    border-color: #138f99 !important;
}

/* Editar: amarillo/amber */
.btn-telemetry-edit {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #212529 !important;
}
.btn-telemetry-edit:hover {
    background-color: #e0a800 !important;
    border-color: #e0a800 !important;
}

/* Borrar: rojo (mantener convenciones) */
.btn-telemetry-delete {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #fff !important;
}
.btn-telemetry-delete:hover {
    background-color: #b02a37 !important;
    border-color: #b02a37 !important;
}

/* Ajustes comunes */
.btn-telemetry-new, .btn-telemetry-view, .btn-telemetry-edit, .btn-telemetry-delete {
    box-shadow: none !important;
    text-decoration: none !important;
}
</style>
