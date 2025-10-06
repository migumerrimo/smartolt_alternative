@extends('layouts.app')

@section('title', 'Solicitudes de Registro - Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">📋 Gestión de Solicitudes de Registro</h1>
            <p class="text-muted">Revisa y gestiona las solicitudes de acceso al sistema</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                ← Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-1">Pendientes</h6>
                            <h3 class="text-primary mb-0">{{ $requests->where('status', 'pending')->count() }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="fs-2">⏳</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-1">Aprobadas</h6>
                            <h3 class="text-success mb-0">{{ $requests->where('status', 'approved')->count() }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="fs-2">✅</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-1">Rechazadas</h6>
                            <h3 class="text-danger mb-0">{{ $requests->where('status', 'rejected')->count() }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="fs-2">❌</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-1">Total</h6>
                            <h3 class="text-info mb-0">{{ $requests->count() }}</h3>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="fs-2">📊</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de solicitudes -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="card-title mb-0">Lista de Solicitudes</h5>
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Solicitante</th>
                                <th>Contacto</th>
                                <th>Rol Solicitado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        {{ $request->created_at->format('d/m/Y') }}<br>
                                        <span class="text-muted">{{ $request->created_at->format('H:i') }}</span>
                                    </small>
                                </td>
                                <td>
                                    <strong>{{ $request->name }}</strong>
                                    @if($request->notes)
                                        <br>
                                        <small class="text-muted" data-bs-toggle="tooltip" 
                                               title="{{ $request->notes }}">
                                            📝 {{ Str::limit($request->notes, 50) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $request->email }}</div>
                                    @if($request->phone)
                                        <small class="text-muted">{{ $request->phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $request->requested_role }}</span>
                                </td>
                                <td>
                                    @if($request->status === 'pending')
                                        <span class="badge bg-warning">⏳ Pendiente</span>
                                    @elseif($request->status === 'approved')
                                        <span class="badge bg-success">✅ Aprobada</span>
                                        @if($request->processed_at)
                                            <br>
                                            <small class="text-muted">
                                                Por: {{ $request->processor->name ?? 'Admin' }}<br>
                                                {{ $request->processed_at->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    @else
                                        <span class="badge bg-danger">❌ Rechazada</span>
                                        @if($request->processed_at)
                                            <br>
                                            <small class="text-muted">
                                                Por: {{ $request->processor->name ?? 'Admin' }}<br>
                                                {{ $request->processed_at->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($request->status === 'pending')
                                        <div class="btn-group btn-group-sm">
                                            <!-- Botón Aprobar -->
                                            <button type="button" class="btn btn-success" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#approveModal{{ $request->id }}">
                                                ✅ Aprobar
                                            </button>
                                            
                                            <!-- Botón Rechazar -->
                                            <button type="button" class="btn btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal{{ $request->id }}">
                                                ❌ Rechazar
                                            </button>
                                        </div>

                                        <!-- Modal para Aprobar -->
                                        <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">✅ Aprobar Solicitud</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('admin.register-requests.approve', $request->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>¿Estás seguro de aprobar la solicitud de <strong>{{ $request->name }}</strong>?</p>
                                                            <div class="mb-3">
                                                                <label for="admin_notes_approve{{ $request->id }}" class="form-label">
                                                                    Notas (opcional):
                                                                </label>
                                                                <textarea class="form-control" id="admin_notes_approve{{ $request->id }}" 
                                                                          name="admin_notes" rows="3" 
                                                                          placeholder="Notas internas para el registro..."></textarea>
                                                            </div>
                                                            <div class="alert alert-info">
                                                                <small>
                                                                    <strong>⚠️ IMPORTANTE:</strong> Se creará un usuario con rol 
                                                                    "<strong>{{ $request->requested_role }}</strong>" y se enviará 
                                                                    un email con instrucciones para establecer su contraseña.
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-success">✅ Sí, Aprobar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal para Rechazar -->
                                        <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">❌ Rechazar Solicitud</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('admin.register-requests.reject', $request->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>¿Estás seguro de rechazar la solicitud de <strong>{{ $request->name }}</strong>?</p>
                                                            <div class="mb-3">
                                                                <label for="admin_notes_reject{{ $request->id }}" class="form-label">
                                                                    Motivo del rechazo (opcional):
                                                                </label>
                                                                <textarea class="form-control" id="admin_notes_reject{{ $request->id }}" 
                                                                          name="admin_notes" rows="3" 
                                                                          placeholder="Explica el motivo del rechazo..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-danger">❌ Sí, Rechazar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Procesada</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="fs-1">📭</div>
                    <h4 class="text-muted">No hay solicitudes de registro</h4>
                    <p class="text-muted">Cuando los usuarios soliciten acceso, aparecerán aquí.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Información adicional -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">💡 Información</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Proceso de aprobación:</strong><br>
                        1. Revisa la información del solicitante<br>
                        2. Aprobar crea un usuario activo con rol solicitado<br>
                        3. Rechazar archiva la solicitud con motivo<br>
                        4. Todos los cambios quedan registrados en el sistema
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">📊 Estadísticas</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Resumen de actividades:</strong><br>
                        • Solicitudes pendientes: {{ $requests->where('status', 'pending')->count() }}<br>
                        • Usuarios creados: {{ $requests->where('status', 'approved')->count() }}<br>
                        • Solicitudes rechazadas: {{ $requests->where('status', 'rejected')->count() }}<br>
                        • Total procesado: {{ $requests->where('status', '!=', 'pending')->count() }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para tooltips -->
<script>
    // Inicializar tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}
.badge {
    font-size: 0.75em;
}
.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}
</style>
@endsection