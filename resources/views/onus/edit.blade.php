@extends('layouts.app')

@section('content')
<h2>Editar ONU</h2>

<form action="{{ route('onus.update', $onu) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Serial</label>
        <input type="text" name="serial_number" class="form-control" value="{{ $onu->serial_number }}" required>
    </div>
    <div class="mb-3">
        <label>Modelo</label>
        <input type="text" name="model" class="form-control" value="{{ $onu->model }}">
    </div>
    <div class="mb-3">
        <label>PON Port</label>
        <input type="text" name="pon_port" class="form-control" value="{{ $onu->pon_port }}">
    </div>
    <div class="mb-3">
        <label>Estado</label>
        <select name="status" class="form-select">
            @foreach(['registered','authenticated','online','down'] as $s)
                <option value="{{ $s }}" @if($onu->status==$s) selected @endif>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
