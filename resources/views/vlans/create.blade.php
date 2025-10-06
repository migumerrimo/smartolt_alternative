@extends('layouts.app')

@section('content')
<h2>Nueva VLAN</h2>

<form action="{{ route('vlans.store') }}" method="POST">
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
        <label>Número</label>
        <input type="number" name="number" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Tipo</label>
        <select name="type" class="form-select">
            <option value="standard">Standard</option>
            <option value="smart">Smart</option>
            <option value="mux">Mux</option>
            <option value="super">Super</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <input type="text" name="description" class="form-control">
    </div>
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
