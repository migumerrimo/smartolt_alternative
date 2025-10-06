@extends('layouts.app')

@section('content')
<h2>Editar OLT</h2>

<form action="{{ route('olts.update', $olt) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="name" class="form-control" value="{{ $olt->name }}" required>
    </div>
    <div class="mb-3">
        <label>Modelo</label>
        <input type="text" name="model" class="form-control" value="{{ $olt->model }}">
    </div>
    <div class="mb-3">
        <label>Vendor</label>
        <select name="vendor" class="form-select">
            @foreach(['Huawei','ZTE','FiberHome','Other'] as $v)
                <option value="{{ $v }}" @if($olt->vendor==$v) selected @endif>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>IP de Gestión</label>
        <input type="text" name="management_ip" class="form-control" value="{{ $olt->management_ip }}" required>
    </div>
    <div class="mb-3">
        <label>Ubicación</label>
        <input type="text" name="location" class="form-control" value="{{ $olt->location }}">
    </div>
    <div class="mb-3">
        <label>Firmware</label>
        <input type="text" name="firmware" class="form-control" value="{{ $olt->firmware }}">
    </div>
    <div class="mb-3">
        <label>Estado</label>
        <select name="status" class="form-select">
            <option value="active" @if($olt->status=='active') selected @endif>Activo</option>
            <option value="inactive" @if($olt->status=='inactive') selected @endif>Inactivo</option>
        </select>
    </div>
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
