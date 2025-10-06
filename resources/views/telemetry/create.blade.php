@extends('layouts.app')

@section('content')
<h2>Nueva Métrica de Telemetría</h2>

<form method="POST" action="{{ route('telemetry.store') }}">
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
        <label>ONU</label>
        <select name="onu_id" class="form-select">
            <option value="">-- Ninguna --</option>
            @foreach($onus as $onu)
                <option value="{{ $onu->id }}">{{ $onu->serial_number }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Métrica</label>
        <input type="text" name="metric" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Valor</label>
        <input type="number" step="0.001" name="value" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Unidad</label>
        <input type="text" name="unit" class="form-control">
    </div>
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
