@extends('layouts.app')

@section('content')
<h2>Editar Tabla de Tráfico</h2>

<form method="POST" action="{{ route('traffic-tables.update',$trafficTable) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="name" value="{{ $trafficTable->name }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>CIR</label>
        <input type="number" name="cir" value="{{ $trafficTable->cir }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>PIR</label>
        <input type="number" name="pir" value="{{ $trafficTable->pir }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Prioridad</label>
        <input type="number" name="priority" value="{{ $trafficTable->priority }}" class="form-control">
    </div>
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
