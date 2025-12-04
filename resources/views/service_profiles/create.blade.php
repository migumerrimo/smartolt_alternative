@extends('layouts.app')

@section('content')
<h2>Nuevo Perfil de Servicio</h2>

<form method="POST" action="{{ route('service-profiles.store') }}">
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
        <label>Profile ID (ID en la OLT)</label>
        <input type="number" name="profile_id" id="profile_id" class="form-control" placeholder="ej: 4" required>
    </div>
    <div class="mb-3">
        <label>Comando opcional ont-port</label>
        <input type="text" name="ont_port_command" id="ont_port_command" class="form-control" placeholder="ej: ont-port eth adaptive">
        <div class="form-text">Campo opcional para añadir configuración de puertos dentro del perfil (ej. <code>ont-port eth adaptive</code>).</div>
    </div>
    <div class="mb-3">
        <label>Servicio</label>
        <select name="service" class="form-select" required>
            <option value="internet">Internet</option>
            <option value="voip">VoIP</option>
            <option value="iptv">IPTV</option>
            <option value="triple-play">Triple Play</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Puertos ETH</label>
        <input type="number" name="eth_ports" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>VLAN</label>
        <select name="vlan_id" class="form-select">
            <option value="">-- Ninguna --</option>
            @foreach($vlans as $vlan)
                <option value="{{ $vlan->id }}">{{ $vlan->number }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-success">Guardar</button>
    <button type="button" class="btn btn-primary ms-2" id="createOnOltBtn">Crear en OLT</button>
</form>

<hr />
<div id="oltCreateResult"></div>

<script>
document.getElementById('createOnOltBtn').addEventListener('click', function(){
    const oltSelect = document.querySelector('select[name="olt_id"]');
    const oltId = oltSelect ? oltSelect.value : null;
    const profileId = document.getElementById('profile_id').value;
    const name = document.querySelector('input[name="name"]').value;
    const ontPort = document.getElementById('ont_port_command').value;

    if (!oltId) {
        alert('Selecciona una OLT.');
        return;
    }
    if (!profileId || !name) {
        alert('Profile ID y Nombre son obligatorios para crear en la OLT.');
        return;
    }

    const payload = {
        profile_id: parseInt(profileId, 10),
        name: name,
        ont_port_command: ontPort || null
    };

    document.getElementById('oltCreateResult').innerHTML = '<div class="alert alert-info">Enviando comandos a la OLT...</div>';

    fetch(`/api/olt/ssh/service-profile/${oltId}/create`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json().then(j => ({ status: r.status, body: j })))
    .then(resp => {
        const status = resp.status;
        const body = resp.body;
        let html = '';
        if (status >= 200 && status < 300) {
            html += '<div class="alert alert-success">Perfil creado en la OLT (revisa `olt_output` para detalles).</div>';
        } else {
            html += '<div class="alert alert-danger">Error al crear en la OLT (revisa `olt_output`).</div>';
        }
        html += '<h5>Comandos enviados</h5><pre>' + (body.commands ? body.commands.join('\n') : '') + '</pre>';
        html += '<h5>Salida cruda OLT</h5><pre>' + (body.olt_output ? body.olt_output.replace(/</g,'&lt;') : '') + '</pre>';
        document.getElementById('oltCreateResult').innerHTML = html;
    })
    .catch(err => {
        console.error(err);
        document.getElementById('oltCreateResult').innerHTML = '<div class="alert alert-danger">Error al conectar con la API.</div>';
    });
});
</script>
@endsection
