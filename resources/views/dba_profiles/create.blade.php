@extends('layouts.app')

@section('content')
<h2>Nuevo Perfil DBA</h2>

<form method="POST" action="{{ route('dba-profiles.store') }}">
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
        <label>Tipo</label>
        <select name="type" class="form-select" required>
            <option value="type1">Type 1</option>
            <option value="type2">Type 2</option>
            <option value="type3">Type 3</option>
            <option value="type4">Type 4</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Max Bandwidth</label>
        <input type="number" name="max_bandwidth" class="form-control" required>
    </div>
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
