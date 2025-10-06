@extends('layouts.app')

@section('content')
<h2>Editar Registro de Cambio</h2>

<form method="POST" action="{{ route('change-history.update',$changeHistory) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nombre del Dispositivo</label>
        <input type="text" name="device_name" value="{{ $changeHistory->device_name }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>Comando</label>
        <textarea name="command" class="form-control">{{ $changeHistory->command }}</textarea>
    </div>
    <div class="mb-3">
        <label>Resultado</label>
        <textarea name="result" class="form-control">{{ $changeHistory->result }}</textarea>
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="description" class="form-control">{{ $changeHistory->description }}</textarea>
    </div>
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
