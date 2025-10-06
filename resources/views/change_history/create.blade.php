@extends('layouts.app')

@section('content')
<h2>Nuevo Registro de Cambio</h2>

<form method="POST" action="{{ route('change-history.store') }}">
    @csrf
    <div class="mb-3">
        <label>Usuario</label>
        <select name="user_id" class="form-select" required>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
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
            <option value="ONU">ONU</option>
            <option value="ROUTER">Router</option>
            <option value="SWITCH">Switch</option>
            <option value="SERVER">Servidor</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Nombre del Dispositivo</label>
        <input type="text" name="device_name" class="form-control">
    </div>
    <div class="mb-3">
        <label>Comando</label>
        <textarea name="command" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label>Resultado</label>
        <textarea name="result" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="description" class="form-control"></textarea>
    </div>
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
