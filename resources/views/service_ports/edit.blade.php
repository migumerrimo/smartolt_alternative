@extends('layouts.app')

@section('content')
<h2>Editar Service Port</h2>

<form method="POST" action="{{ route('service-ports.update',$servicePort) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>ONU</label>
        <select name="onu_id" class="form-select" required>
            @foreach($onus as $onu)
                <option value="{{ $onu->id }}" @if($servicePort->onu_id==$onu->id) selected @endif>
                    {{ $onu->serial_number }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>VLAN</label>
        <select name="vlan_id" class="form-select" required>
            @foreach($vlans as $vlan)
                <option value="{{ $vlan->id }}" @if($servicePort->vlan_id==$vlan->id) selected @endif>
                    {{ $vlan->number }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Traffic Table</label>
        <select name="traffic_table_id" class="form-select">
            <option value="">-- Ninguna --</option>
            @foreach($trafficTables as $tt)
                <option value="{{ $tt->id }}" @if($servicePort->traffic_table_id==$tt->id) selected @endif>
                    {{ $tt->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Gemport ID</label>
        <input type="number" name="gemport_id" value="{{ $servicePort->gemport_id }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>Tipo</label>
        <select name="type" class="form-select" required>
            @foreach(['gpon','eth','epon'] as $t)
                <option value="{{ $t }}" @if($servicePort->type==$t) selected @endif>{{ strtoupper($t) }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
