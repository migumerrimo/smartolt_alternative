@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Encabezado con Botones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>OLTs</h2>
        <div class="btn-group">
            <!-- Botón para Vista Previa del PDF -->
            <a href="{{ route('olts.report.preview') }}" 
               class="btn btn-outline-info btn-sm" 
               target="_blank">
                <i class="fas fa-eye me-1"></i>Vista Previa
            </a>
            
            <!-- Botón para Descargar PDF -->
            <a href="{{ route('olts.report.pdf') }}" 
               class="btn btn-outline-success btn-sm">
                <i class="fas fa-download me-1"></i>Descargar PDF
            </a>
            
            <!-- Botón Nueva OLT -->
            <a href="{{ route('olts.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Nueva OLT
            </a>
        </div>
    </div>

    @if($olts->count() > 0)
        <!-- Tarjetas de Resumen -->
        <div class="row mb-4">
            <div class="col-md-2 mb-2">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $olts->count() }}</h5>
                        <small class="card-text">Total OLTs</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <div class="card text-white bg-success">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $olts->where('status', 'active')->count() }}</h5>
                        <small class="card-text">OLTs Activas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $olts->where('status', 'maintenance')->count() }}</h5>
                        <small class="card-text">En Mantenimiento</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <div class="card text-white bg-info">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $olts->where('status', 'inactive')->count() }}</h5>
                        <small class="card-text">OLTs Inactivas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <div class="card text-white bg-success">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $olts->where('connection_status', true)->count() }}</h5>
                        <small class="card-text">Conectadas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <div class="card text-white bg-danger">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $olts->where('connection_status', false)->count() }}</h5>
                        <small class="card-text">Desconectadas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de OLTs -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Modelo</th>
                                <th>IP</th>
                                <th>Estado</th>
                                <th>Conexión SSH</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($olts as $olt)
                            <tr>
                                <td><strong>#{{ $olt->id }}</strong></td>
                                <td>{{ $olt->name }}</td>
                                <td>{{ $olt->model }}</td>
                                <td>
                                    <code>{{ $olt->management_ip }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $olt->status === 'active' ? 'success' : ($olt->status === 'maintenance' ? 'warning' : 'secondary') }}">
                                        {{ $olt->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($olt->connection_status)
                                        <span class="badge bg-success">
                                            <i class="fas fa-plug me-1"></i>Conectada
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-unplug me-1"></i>Desconectada
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <!-- Botón Ver -->
                                        <a href="{{ route('olts.show', $olt) }}" class="btn btn-info" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Botón Probar Conexión -->
                                        <a href="{{ route('olts.check-connection', $olt) }}" 
                                           class="btn btn-{{ $olt->connection_status ? 'success' : 'warning' }}"
                                           title="Probar Conexión SSH"
                                           onclick="testConnection(event, this, {{ $olt->id }})">
                                            <i class="fas fa-satellite-dish"></i>
                                        </a>
                                        
                                        <!-- Botón Editar -->
                                        <a href="{{ route('olts.edit', $olt) }}" class="btn btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- Botón Eliminar -->
                                        <form action="{{ route('olts.destroy', $olt) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta OLT?')" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-network-wired fa-2x mb-3"></i>
            <h4>No hay OLTs registradas aún</h4>
            <p>Comienza agregando la primera OLT al sistema.</p>
            <a href="{{ route('olts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Registrar Primera OLT
            </a>
        </div>
    @endif
</div>

<!-- Modal para prueba de conexión -->
<div class="modal fade" id="connectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Probando Conexión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="connectionResult">
                <!-- Aquí se mostrará el resultado -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
.btn-group .btn {
    margin: 0 2px;
}
.table th {
    font-weight: 600;
}
.badge {
    font-size: 0.75em;
    text-transform: capitalize;
}
.connection-testing {
    opacity: 0.6;
    pointer-events: none;
}
</style>

<script>
function testConnection(event, element, oltId) {
    event.preventDefault();
    
    const url = `/olts/${oltId}/check-connection`;
    const originalHtml = element.innerHTML;
    
    // Mostrar loading
    element.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    element.classList.add('connection-testing');
    
    // Hacer la petición AJAX
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Mostrar resultado en modal
            const modal = new bootstrap.Modal(document.getElementById('connectionModal'));
            const modalBody = document.getElementById('connectionResult');
            
            if (data.connected) {
                modalBody.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h5>✅ Conexión Exitosa</h5>
                        <p><strong>OLT:</strong> ${data.olt_name}</p>
                        <p><strong>IP:</strong> ${data.management_ip}</p>
                        <p>${data.message}</p>
                    </div>
                `;
                // Actualizar botón a verde
                element.classList.remove('btn-warning');
                element.classList.add('btn-success');
            } else {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                        <h5>❌ Conexión Fallida</h5>
                        <p><strong>OLT:</strong> ${data.olt_name}</p>
                        <p><strong>IP:</strong> ${data.management_ip}</p>
                        <p>${data.message}</p>
                        <small class="text-muted">Verifica que la OLT esté encendida y accesible desde la red.</small>
                    </div>
                `;
                // Actualizar botón a amarillo
                element.classList.remove('btn-success');
                element.classList.add('btn-warning');
            }
            
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            const modal = new bootstrap.Modal(document.getElementById('connectionModal'));
            document.getElementById('connectionResult').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h5>Error en la prueba</h5>
                    <p>Ocurrió un error al probar la conexión.</p>
                    <small class="text-muted">${error.message}</small>
                </div>
            `;
            modal.show();
        })
        .finally(() => {
            // Restaurar el ícono original
            element.innerHTML = '<i class="fas fa-satellite-dish"></i>';
            element.classList.remove('connection-testing');
            
            // Recargar la página después de 2 segundos para actualizar estados
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        });
}

// Función para ejecutar comandos (puedes expandir esto)
function executeCommand(oltId, command) {
    const url = `/olts/${oltId}/execute-command`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ command: command })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Comando ejecutado: ' + data.output);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error ejecutando comando');
    });
}
</script>
@endsection