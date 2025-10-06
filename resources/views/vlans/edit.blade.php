@extends('layouts.app')

@section('content')
<h2>Editar VLAN</h2>

<form action="{{ route('vlans.update', $vlan) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Número</label>
        <input type="number" name="number" class="form-control" value="{{ $vlan->number }}" required>
    </div>
    <div class="mb-3">
        <label>Tipo</label>
        <select name="type" class="form-select">
            @foreach(['standard','smart','mux','super'] as $t)
                <option value="{{ $t }}" @if($vlan->type==$t) selected @endif>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Descripción</label>
        <input type="text" name="description" class="form-control" value="{{ $vlan->description }}">
    </div>
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
