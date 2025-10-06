@extends('layouts.app')

@section('content')
<h2>Nueva Configuración de Dispositivo</h2>

<form method="POST" action="{{ route('device-configs.store') }}">
    @csrf
    <div class="mb-3">
        <label>OLT</label>
        <select name="olt_id" class="form-select" required>
            @foreach($olts as $olt)
                <option value="{{ $olt->id }}">{{ $olt->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Tipo de Dispositivo</label>
        <select name="device_type" class="form-select" required>
            <option value="OLT">OLT</option>
            <option value="ROUTER">Router</option>
            <option value="SWITCH">Switch</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Nombre del Dispositivo</label>
        <input type="text" name="device_name" class="form-control">
    </div>
    <div class="mb-3">
        <label>Configuración</label>
        <textarea name="config_text" class="form-control" rows="6" required></textarea>
    </div>
    <div class="mb-3">
        <label>Versión</label>
        <input type="text" name="version" class="form-control">
    </div>
    <div class="mb-3">
        <label>Aplicado por</label>
        <select name="applied_by" class="form-select">
            <option value="">-- Ninguno --</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Fecha de Aplicación</label>
        <input type="datetime-local" name="applied_at" class="form-control">
    </div>
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
