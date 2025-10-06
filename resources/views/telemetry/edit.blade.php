@extends('layouts.app')

@section('content')
<h2>Editar Métrica de Telemetría</h2>

<form method="POST" action="{{ route('telemetry.update',$telemetry) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Métrica</label>
        <input type="text" name="metric" value="{{ $telemetry->metric }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Valor</label>
        <input type="number" step="0.001" name="value" value="{{ $telemetry->value }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Unidad</label>
        <input type="text" name="unit" value="{{ $telemetry->unit }}" class="form-control">
    </div>
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
