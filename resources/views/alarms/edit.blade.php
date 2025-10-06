@extends('layouts.app')

@section('content')
<h2>Editar Alarma</h2>

<form method="POST" action="{{ route('alarms.update',$alarm) }}">
    @csrf @method('PUT')
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
    <div class="mb-3">
        <label>Mensaje</label>
        <textarea name="message" class="form-control" required>{{ $alarm->message }}</textarea>
    </div>
    <div class="mb-3">
        <label>Activa</label>
        <input type="checkbox" name="active" value="1" @if($alarm->active) checked @endif>
    </div>
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
