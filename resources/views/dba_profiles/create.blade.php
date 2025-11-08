@extends('layouts.app')

@section('content')

<!-- Sección principal: formulario para registrar un nuevo perfil DBA -->
<h2>Nuevo Perfil DBA</h2>

<!-- Formulario para crear un nuevo perfil DBA asociado a una OLT -->
<form method="POST" action="{{ route('dba-profiles.store') }}">
    @csrf

    <!-- Campo de selección de OLT (asociación del perfil a una OLT específica) -->
    <div class="mb-3">
        <label>OLT</label>
        <select name="olt_id" class="form-select" required>
            @foreach($olts as $olt)
                <option value="{{ $olt->id }}">{{ $olt->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Campo para definir el nombre del perfil DBA -->
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <!-- Campo para seleccionar el tipo de perfil DBA -->
    <div class="mb-3">
        <label>Tipo</label>
        <select name="type" class="form-select" required>
            <option value="type1">Type 1</option>
            <option value="type2">Type 2</option>
            <option value="type3">Type 3</option>
            <option value="type4">Type 4</option>
        </select>
    </div>

    <!-- Campo para establecer el ancho de banda máximo del perfil -->
    <div class="mb-3">
        <label>Max Bandwidth</label>
        <input type="number" name="max_bandwidth" class="form-control" required>
    </div>

    <!-- Botón para guardar el nuevo perfil DBA -->
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
