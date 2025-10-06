@extends('layouts.app')

@section('content')
<h2>Nueva OLT</h2>

<form action="{{ route('olts.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Modelo</label>
        <input type="text" name="model" class="form-control">
    </div>
    <div class="mb-3">
        <label>Vendor</label>
        <select name="vendor" class="form-select">
            <option value="Huawei">Huawei</option>
            <option value="ZTE">ZTE</option>
            <option value="FiberHome">FiberHome</option>
            <option value="Other">Other</option>
        </select>
    </div>
    <div class="mb-3">
        <label>IP de Gestión</label>
        <input type="text" name="management_ip" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Ubicación</label>
        <input type="text" name="location" class="form-control">
    </div>
    <div class="mb-3">
        <label>Firmware</label>
        <input type="text" name="firmware" class="form-control">
    </div>
    <div class="mb-3">
        <label>Estado</label>
        <select name="status" class="form-select">
            <option value="active">Activo</option>
            <option value="inactive">Inactivo</option>
        </select>
    </div>
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
