@extends('layouts.app')

@section('content')

<!-- Título principal de la vista -->
<h2>Editar Configuración de Dispositivo</h2>

<!-- Formulario para editar una configuración de dispositivo existente -->
<form method="POST" action="{{ route('device-configs.update',$deviceConfig) }}">
    @csrf @method('PUT')

    <!-- Campo para editar el nombre del dispositivo -->
    <div class="mb-3">
        <label>Nombre del Dispositivo</label>
        <input type="text" name="device_name" value="{{ $deviceConfig->device_name }}" class="form-control">
    </div>

    <!-- Campo para modificar el texto de configuración -->
    <div class="mb-3">
        <label>Configuración</label>
        <textarea name="config_text" class="form-control" rows="6">{{ $deviceConfig->config_text }}</textarea>
    </div>

    <!-- Campo para actualizar la versión de la configuración -->
    <div class="mb-3">
        <label>Versión</label>
        <input type="text" name="version" value="{{ $deviceConfig->version }}" class="form-control">
    </div>

    <!-- Campo para seleccionar el usuario que aplicó la configuración -->
    <div class="mb-3">
        <label>Aplicado por</label>
        <select name="applied_by" class="form-select">
            <option value="">-- Ninguno --</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @if($deviceConfig->applied_by==$user->id) selected @endif>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Campo para editar la fecha y hora de aplicación -->
    <div class="mb-3">
        <label>Fecha de Aplicación</label>
        <input type="datetime-local" name="applied_at" value="{{ $deviceConfig->applied_at }}" class="form-control">
    </div>

    <!-- Botón para actualizar la configuración -->
    <button class="btn btn-primary">Actualizar</button>
</form>

@endsection

