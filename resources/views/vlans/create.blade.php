@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Nueva VLAN</h2>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5>Crear VLAN directamente en la OLT</h5>
        </div>
        <div class="card-body">
            <div id="createResult" class="mb-3"></div>
            
            <form id="createVlanForm" onsubmit="return crearVlan(event)">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Seleccionar OLT *</label>
                        <select id="oltId" class="form-control" required>
                            @foreach($olts as $olt)
                                <option value="{{ $olt->id }}">{{ $olt->name }} ({{ $olt->management_ip }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">VLAN # *</label>
                        <input type="number" id="vlanNumber" class="form-control" placeholder="200" min="1" max="4094" required />
                        <small class="text-muted">Número de VLAN (1-4094)</small>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tipo *</label>
                        <select id="vlanType" class="form-control" required>
                            <option value="smart" selected>Smart</option>
                            <option value="standard">Standard</option>
                            <option value="mux">Mux</option>
                            <option value="super">Super</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Slot/Port *</label>
                        <input type="text" id="vlanPorts" class="form-control" placeholder="0/7" required />
                        <small class="text-muted">Ej: 0/7 (frame/slot)</small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Port List *</label>
                        <input type="text" id="vlanPortMode" class="form-control" placeholder="1" value="1" required />
                        <small class="text-muted">Ej: 1 o 0,1,4-5</small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Native VLAN Tag</label>
                        <input type="number" id="nativeVlan" class="form-control" placeholder="1" value="1" />
                        <small class="text-muted">Tag para native-vlan</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">VLAN Interface IP</label>
                        <input type="text" id="vlanifIp" class="form-control" placeholder="10.10.40.1" />
                        <small class="text-muted">IP de la interfaz vlanif (opcional)</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Netmask</label>
                        <input type="text" id="vlanifNetmask" class="form-control" value="255.255.255.0" />
                        <small class="text-muted">Máscara de subred</small>
                    </div>
                </div>

                <div class="alert alert-info">
                    <strong>Ejemplo de comando generado:</strong><br>
                    <code>
                        vlan 200 smart<br>
                        port vlan 200 0/7 1<br>
                        interface scu 0/7<br>
                        native-vlan 1 vlan 200<br>
                        quit<br>
                        interface vlanif 200<br>
                        ip address 10.10.40.1 255.255.255.0<br>
                        quit
                    </code>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Crear VLAN en OLT
                </button>
                <a href="{{ route('vlans.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<script>
function crearVlan(e) {
    e.preventDefault();
    document.getElementById('createResult').innerHTML = '<div class="alert alert-info">Ejecutando comandos en la OLT...</div>';
    
    const oltId = document.getElementById('oltId').value;
    const vlanNumber = parseInt(document.getElementById('vlanNumber').value, 10);
    const vlanType = document.getElementById('vlanType').value;
    const slotPort = document.getElementById('vlanPorts').value.trim();
    const portList = document.getElementById('vlanPortMode').value.trim();
    const nativeVlan = document.getElementById('nativeVlan').value;
    const vlanifIp = document.getElementById('vlanifIp').value.trim();
    const vlanifNetmask = document.getElementById('vlanifNetmask').value.trim();

    const payload = {
        number: vlanNumber,
        type: vlanType,
        ports: slotPort,
        port_mode: portList,
        native_port: slotPort,  // Usamos el mismo slot/port
        native_vlan: nativeVlan ? parseInt(nativeVlan, 10) : null,
        vlanif_ip: vlanifIp || null,
        vlanif_netmask: vlanifNetmask || '255.255.255.0'
    };

    fetch(`/api/olt/ssh/vlan/${oltId}/create`, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(r => {
        if (!r.ok) {
            return r.json().then(data => {
                throw new Error(data.message || 'Error del servidor');
            });
        }
        return r.json();
    })
    .then(resp => {
        if (resp.success) {
            document.getElementById('createResult').innerHTML = `
                <div class="alert alert-success">
                    <strong>¡Éxito!</strong> ${resp.message}
                </div>
                <div class="card">
                    <div class="card-header">Salida del OLT:</div>
                    <div class="card-body">
                        <pre style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 10px;">${escapeHtml(resp.olt_output || '')}</pre>
                    </div>
                </div>
            `;
            // Resetear formulario
            document.getElementById('createVlanForm').reset();
        } else {
            document.getElementById('createResult').innerHTML = `
                <div class="alert alert-danger">
                    <strong>Error:</strong> ${resp.message || 'Error desconocido'}
                </div>
                <div class="card">
                    <div class="card-header">Salida del OLT:</div>
                    <div class="card-body">
                        <pre style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 10px;">${escapeHtml(resp.olt_output || '')}</pre>
                    </div>
                </div>
            `;
        }
    })
    .catch(err => {
        console.error(err);
        document.getElementById('createResult').innerHTML = `
            <div class="alert alert-danger">
                <strong>Error de conexión:</strong> ${err.message}
            </div>
        `;
    });
    
    return false;
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}
</script>
@endsection