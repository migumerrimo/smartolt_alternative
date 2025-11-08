@extends('layouts.app')

@section('content')

<!-- Sección principal: formulario para crear una nueva alarma -->
<h2>Nueva Alarma</h2>

<!-- Formulario de creación de alarma -->
<form method="POST" action="{{ route('alarms.store') }}">
    @csrf

    <!-- Selección de OLT (obligatorio) -->
    <div class="mb-3">
        <label>OLT</label>
        <select name="olt_id" class="form-select" required>
            @foreach($olts as $olt)
                <option value="{{ $olt->id }}">{{ $olt->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Selección de ONU (opcional) -->
    <div class="mb-3">
        <label>ONU</label>
        <select name="onu_id" class="form-select">
            <option value="">-- Ninguna --</option>
            @foreach($onus as $onu)
                <option value="{{ $onu->id }}">{{ $onu->serial_number }}</option>
            @endforeach
        </select>
    </div>

    <!-- Selección de severidad de la alarma -->
    <div class="mb-3">
        <label>Severidad</label>
        <select name="severity" class="form-select" required>
            <option value="critical">Crítica</option>
            <option value="major">Mayor</option>
            <option value="minor">Menor</option>
            <option value="warning">Advertencia</option>
            <option value="info">Info</option>
        </select>
    </div>

    <!-- Campo para el mensaje descriptivo de la alarma -->
    <div class="mb-3">
        <label>Mensaje</label>
        <textarea name="message" class="form-control" required></textarea>
    </div>

    <!-- Checkbox para activar o desactivar la alarma -->
    <div class="mb-3">
        <label>Activa</label>
        <input type="checkbox" name="active" value="1" checked>
    </div>

    <!-- Botón para guardar la nueva alarma -->
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
