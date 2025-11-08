@extends('layouts.app')

@section('content')

<!-- Sección principal: formulario para editar un registro existente del historial de cambios -->
<h2>Editar Registro de Cambio</h2>

<!-- Formulario de edición de registro de cambio -->
<form method="POST" action="{{ route('change-history.update', $changeHistory) }}">
    @csrf 
    @method('PUT')

    <!-- Campo para modificar el nombre del dispositivo afectado -->
    <div class="mb-3">
        <label>Nombre del Dispositivo</label>
        <input type="text" name="device_name" value="{{ $changeHistory->device_name }}" class="form-control">
    </div>

    <!-- Campo para modificar el comando ejecutado -->
    <div class="mb-3">
        <label>Comando</label>
        <textarea name="command" class="form-control">{{ $changeHistory->command }}</textarea>
    </div>

    <!-- Campo para modificar el resultado obtenido -->
    <div class="mb-3">
        <label>Resultado</label>
        <textarea name="result" class="form-control">{{ $changeHistory->result }}</textarea>
    </div>

    <!-- Campo para modificar la descripción o detalles del cambio -->
    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="description" class="form-control">{{ $changeHistory->description }}</textarea>
    </div>

    <!-- Botón para actualizar el registro de cambio -->
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection

