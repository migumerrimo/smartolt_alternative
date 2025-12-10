@extends('layouts.app')

@section('content')

<div class="container">
    <h2>Ingresa los datos solicitados en los campos</h2>

    <!-- Tarjeta para crear DBA Profile directamente en la OLT -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5>Crear DBA Profile en OLT (Comando directo)</h5>
        </div>
        <div class="card-body">
            <form id="createDbaProfileForm">
                <!-- Selección de OLT -->
                <div class="mb-3">
                    <label for="olt_id" class="form-label">Seleccionar OLT *</label>
                    <select name="olt_id" id="olt_id" class="form-select" required>
                        <option value="">-- Seleccionar OLT --</option>
                        @foreach($olts as $olt)
                            <option value="{{ $olt->id }}">{{ $olt->name }} ({{ $olt->management_ip }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Profile ID -->
                <div class="mb-3">
                    <label for="profile_id" class="form-label">Profile ID *</label>
                    <input type="number" name="profile_id" id="profile_id" class="form-control" 
                           placeholder="Ej: 10" min="0" required>
                    <small class="text-muted">Número identificador del perfil DBA</small>
                </div>

                <!-- Profile Name -->
                <div class="mb-3">
                    <label for="profile_name" class="form-label">Profile Name *</label>
                    <input type="text" name="profile_name" id="profile_name" class="form-control" 
                           placeholder="Ej: DBA 100 MEGAS" required>
                    <small class="text-muted">Nombre descriptivo del perfil</small>
                </div>

                <!-- Type -->
                <div class="mb-3">
                    <label for="type" class="form-label">Type *</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="1">Type 1</option>
                        <option value="2">Type 2</option>
                        <option value="3">Type 3</option>
                        <option value="4" selected>Type 4</option>
                    </select>
                    <small class="text-muted">Tipo de perfil DBA (1-4)</small>
                </div>

                <!-- Max Bandwidth -->
                <div class="mb-3">
                    <label for="max_kbps" class="form-label">Max Bandwidth (kbps) *</label>
                    <input type="number" name="max_kbps" id="max_kbps" class="form-control" 
                           placeholder="Ej: 100000" min="0" required>
                    <small class="text-muted">Ancho de banda máximo en kbps</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Crear DBA Profile en OLT
                </button>
                <a href="{{ route('dba-profiles.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>

            <!-- Resultado del comando -->
            <div id="result" class="mt-4" style="display:none;">
                <div class="alert" role="alert" id="resultAlert"></div>
                <div class="card">
                    <div class="card-header">
                        <strong>Salida del OLT:</strong>
                    </div>
                    <div class="card-body">
                        <pre id="oltOutput" style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 10px;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('createDbaProfileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const oltId = document.getElementById('olt_id').value;
    const profileId = document.getElementById('profile_id').value;
    const profileName = document.getElementById('profile_name').value;
    const type = document.getElementById('type').value;
    const maxKbps = document.getElementById('max_kbps').value;

    if (!oltId) {
        alert('Selecciona una OLT');
        return;
    }

    const payload = {
        profile_id: parseInt(profileId),
        profile_name: profileName,
        type: type,
        max_kbps: parseInt(maxKbps)
    };

    try {
        const response = await fetch(`/api/olt/ssh/dba-profile/${oltId}/create`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        });

        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('El servidor no devolvió JSON. Revisa los logs del servidor.');
        }

        const data = await response.json();

        // Mostrar resultado
        const resultDiv = document.getElementById('result');
        const alertDiv = document.getElementById('resultAlert');
        const outputPre = document.getElementById('oltOutput');

        resultDiv.style.display = 'block';

        if (data.success) {
            alertDiv.className = 'alert alert-success';
            alertDiv.innerHTML = '<strong>¡Éxito!</strong> ' + data.message;
        } else {
            alertDiv.className = 'alert alert-danger';
            alertDiv.innerHTML = '<strong>Error:</strong> ' + (data.message || data.error || 'Error desconocido');
        }

        outputPre.textContent = data.olt_output || 'Sin salida';

    } catch (error) {
        const resultDiv = document.getElementById('result');
        const alertDiv = document.getElementById('resultAlert');
        resultDiv.style.display = 'block';
        alertDiv.className = 'alert alert-danger';
        alertDiv.innerHTML = '<strong>Error de red:</strong> ' + error.message;
        console.error('Error completo:', error);
    }
});
</script>

@endsection