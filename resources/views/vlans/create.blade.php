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

    <hr />
    <h4>Crear VLAN directamente en la OLT</h4>
    <div id="createResult"></div>
    <form id="createVlanForm" onsubmit="return crearVlan(event)">
        <div class="form-row">
            <div class="form-group col-md-2">
                <label>OLT ID</label>
                <input type="number" id="oltId" class="form-control" value="{{ old('olt_id', $olts->first()->id ?? 1) }}" />
            </div>
            <div class="form-group col-md-2">
                <label>VLAN #</label>
                <input type="number" id="vlanNumber" class="form-control" required />
            </div>
            <div class="form-group col-md-2">
                <label>Tipo</label>
                <input type="text" id="vlanType" class="form-control" value="smart" required />
            </div>
            <div class="form-group col-md-3">
                <label>Ports (ej. 0/7 0/8)</label>
                <input type="text" id="vlanPorts" class="form-control" />
            </div>
            <div class="form-group col-md-1">
                <label>Port mode</label>
                <input type="text" id="vlanPortMode" class="form-control" placeholder="2" />
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label>Native port (ej. 0/7)</label>
                <input type="text" id="nativePort" class="form-control" />
            </div>
            <div class="form-group col-md-2">
                <label>Native VLAN</label>
                <input type="number" id="nativeVlan" class="form-control" />
            </div>
            <div class="form-group col-md-3">
                <label>Vlanif IP</label>
                <input type="text" id="vlanifIp" class="form-control" />
            </div>
            <div class="form-group col-md-2">
                <label>Netmask</label>
                <input type="text" id="vlanifNetmask" class="form-control" value="255.255.255.0" />
            </div>
            <div class="form-group col-md-2 align-self-end">
                <button class="btn btn-success">Crear en OLT</button>
            </div>
        </div>
    </form>

    <script>
    function crearVlan(e) {
        e.preventDefault();
        document.getElementById('createResult').innerHTML = '';
        const oltId = document.getElementById('oltId').value || '1';
        const payload = {
            number: parseInt(document.getElementById('vlanNumber').value, 10),
            type: document.getElementById('vlanType').value,
            ports: document.getElementById('vlanPorts').value,
            port_mode: document.getElementById('vlanPortMode').value,
            native_port: document.getElementById('nativePort').value,
            native_vlan: document.getElementById('nativeVlan').value ? parseInt(document.getElementById('nativeVlan').value, 10) : null,
            vlanif_ip: document.getElementById('vlanifIp').value,
            vlanif_netmask: document.getElementById('vlanifNetmask').value,
            description: document.querySelector('input[name="description"]').value || null
        };

        fetch(`/api/olt/ssh/vlan/${oltId}/create`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(resp => {
            if (!resp) {
                document.getElementById('createResult').innerHTML = '<div class="alert alert-danger">No se obtuvo respuesta del servidor.</div>';
                return;
            }
            if (resp.success) {
                document.getElementById('createResult').innerHTML = `<div class="alert alert-success">${resp.message}</div><pre>${escapeHtml(resp.olt_output || '')}</pre>`;
            } else {
                document.getElementById('createResult').innerHTML = `<div class="alert alert-danger">${resp.message || 'Error'}<pre>${escapeHtml(resp.olt_output || '')}</pre></div>`;
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('createResult').innerHTML = '<div class="alert alert-danger">Error al conectar con la API.</div>';
        });
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
    </script>
@endsection
