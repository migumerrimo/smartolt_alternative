@extends('layouts.app')

@section('content')
<h2>Nueva Tabla de Tráfico</h2>

<form method="POST" action="{{ route('traffic-tables.store') }}">
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
        <label>Nombre</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>CIR</label>
        <input type="number" name="cir" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>PIR</label>
        <input type="number" name="pir" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Prioridad</label>
        <input type="number" name="priority" class="form-control" value="0">
    </div>
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
