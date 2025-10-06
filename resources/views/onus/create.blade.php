@extends('layouts.app')

@section('content')
<h2>Nueva ONU</h2>

<form action="{{ route('onus.store') }}" method="POST">
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
        <label>Serial</label>
        <input type="text" name="serial_number" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Modelo</label>
        <input type="text" name="model" class="form-control">
    </div>
    <div class="mb-3">
        <label>PON Port</label>
        <input type="text" name="pon_port" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Perfil de Línea</label>
        <select name="line_profile_id" class="form-select">
            <option value="">-- Ninguno --</option>
            @foreach($lineProfiles as $lp)
                <option value="{{ $lp->id }}">{{ $lp->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Perfil de Servicio</label>
        <select name="service_profile_id" class="form-select">
            <option value="">-- Ninguno --</option>
            @foreach($serviceProfiles as $sp)
                <option value="{{ $sp->id }}">{{ $sp->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Estado</label>
        <select name="status" class="form-select">
            <option value="registered">Registered</option>
            <option value="authenticated">Authenticated</option>
            <option value="online">Online</option>
            <option value="down">Down</option>
        </select>
    </div>
    <button class="btn btn-success">Guardar</button>
</form>
@endsection
