@extends('layouts.app')

@section('content')

<!-- Sección principal: formulario para editar una alarma existente -->
<h2>Editar Alarma</h2>

<!-- Formulario de edición de alarma -->
<form method="POST" action="{{ route('alarms.update', $alarm) }}">
    @csrf 
    @method('PUT')

    <!-- Selección de severidad de la alarma -->
    <div class="mb-3">
        <label>Severidad</label>
        <select name="severity" class="form-select" required>
            @foreach(['critical','major','minor','warning','info'] as $sev)
                <option value="{{ $sev }}" @if($alarm->severity==$sev) selected @endif>
                    {{ ucfirst($sev) }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Campo para el mensaje descriptivo de la alarma -->
    <div class="mb-3">
        <label>Mensaje</label>
        <textarea name="message" class="form-control" required>{{ $alarm->message }}</textarea>
    </div>

    <!-- Checkbox para indicar si la alarma está activa -->
    <div class="mb-3">
        <label>Activa</label>
        <input type="checkbox" name="active" value="1" @if($alarm->active) checked @endif>
    </div>

    <!-- Botón para actualizar los datos de la alarma -->
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
