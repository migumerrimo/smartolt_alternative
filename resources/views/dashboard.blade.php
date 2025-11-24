@extends('layouts.app')

@section('content')

<!-- Sección de Solicitudes Pendientes (Solo para Admin) -->
@if(auth()->user()->role === 'admin')
    @php
        $pendingRequests = \App\Models\RegisterRequest::where('status', 'pending')->count();
    @endphp
    
    @if($pendingRequests > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>⚠️ Tienes {{ $pendingRequests }} solicitud(es) de registro pendientes</strong>
                <p class="mb-0">Revisa y aprueba las solicitudes de nuevos usuarios.</p>
            </div>
            <div>
                <a href="{{ route('admin.register-requests.index') }}" class="btn btn-warning btn-sm">
                    📋 Revisar Solicitudes
                </a>
            </div>
        </div>
    </div>
    @endif
@endif

<!-- Estadísticas principales -->
<div class="row">
    <div class="col-md-3">
        <div class="card text-bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">OLTs Activas</h5>
                <p class="card-text display-6">{{ $olts_count }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">ONUs Online</h5>
                <p class="card-text display-6">{{ $onus_online }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title">Alarmas Activas</h5>
                <p class="card-text display-6">{{ $alarms_active }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Métricas de Telemetría</h5>
                <p class="card-text display-6">{{ $telemetry_count }}</p>
            </div>
        </div>
    </div>
</div>

<!-- ➤ WIDGET DE ESTADO SSH A LA OLT -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">🛠️ Estado de Conexión SSH con la OLT</h5>
                <span id="ssh-status-badge" class="badge bg-secondary">Verificando...</span>
            </div>

            <div class="card-body">
                <div id="ssh-status-content" class="text-center py-3">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2">Comprobando acceso a la OLT...</p>
                </div>
            </div>

            <div class="card-footer text-muted text-end">
                Última actualización: <span id="ssh-last-update">--/--/---- --:--:--</span>
            </div>
        </div>
    </div>
</div>

<script>
function updateSSHStatus() {
    fetch("/api/olt/ssh/status")
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById("ssh-status-badge");
            const content = document.getElementById("ssh-status-content");
            const lastUpdate = document.getElementById("ssh-last-update");

            const now = new Date().toLocaleString();
            lastUpdate.textContent = now;

            if (data.connected) {
                badge.className = "badge bg-success";
                badge.textContent = "Conectada";

                content.innerHTML = `
                    <h4 class="text-success">🟢 Conexión Establecida</h4>
                    <p class="text-muted mb-1">OLT Accesible vía SSH</p>
                    <p><strong>Modelo Detectado:</strong> ${data.model ?? "Huawei MA5680T"}</p>
                `;
            } else {
                badge.className = "badge bg-danger";
                badge.textContent = "Desconectada";

                content.innerHTML = `
                    <h4 class="text-danger">🔴 Sin Conexión SSH</h4>
                    <p class="text-muted">${data.message ?? "No se pudo acceder a la OLT"}</p>
                `;
            }
        })
        .catch(() => {
            const badge = document.getElementById("ssh-status-badge");
            const content = document.getElementById("ssh-status-content");

            badge.className = "badge bg-danger";
            badge.textContent = "Error";

            content.innerHTML = `
                <h4 class="text-danger">⚠️ Error de Comunicación</h4>
                <p class="text-muted">No se pudo contactar con el servidor Laravel.</p>
            `;
        });
}

updateSSHStatus();
setInterval(updateSSHStatus, 10000);
</script>

<!-- Acciones rápidas para Admin -->
@if(auth()->user()->role === 'admin')
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">🔧 Acciones Rápidas - Administrador</h5>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.register-requests.index') }}" class="btn btn-outline-primary w-100">
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="fs-4">📋</span>
                                <div class="ms-2 text-start">
                                    <small class="d-block">Solicitudes</small>
                                    <strong>{{ $pendingRequests }} pendientes</strong>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 mb-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-success w-100">
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="fs-4">👥</span>
                                <div class="ms-2 text-start">
                                    <small class="d-block">Usuarios</small>
                                    <strong>Gestionar</strong>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 mb-2">
                        <a href="{{ route('olts.index') }}" class="btn btn-outline-info w-100">
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="fs-4">🔌</span>
                                <div class="ms-2 text-start">
                                    <small class="d-block">OLTs</small>
                                    <strong>Configurar</strong>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 mb-2">
                        <a href="{{ route('alarms.index') }}" class="btn btn-outline-warning w-100">
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="fs-4">🚨</span>
                                <div class="ms-2 text-start">
                                    <small class="d-block">Alarmas</small>
                                    <strong>Verificar</strong>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Información del sistema -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">ℹ️ Información del Sistema</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-6">
                        <small class="text-muted">Usuario:</small>
                        <p class="mb-2"><strong>{{ auth()->user()->name }}</strong></p>
                    </div>

                    <div class="col-6">
                        <small class="text-muted">Rol:</small>
                        <p class="mb-2">
                            <span class="badge bg-{{ auth()->user()->role === 'admin' ? 'danger' : 'primary' }}">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </p>
                    </div>

                    <div class="col-6">
                        <small class="text-muted">Email:</small>
                        <p class="mb-2"><small>{{ auth()->user()->email }}</small></p>
                    </div>

                    <div class="col-6">
                        <small class="text-muted">Último acceso:</small>
                        <p class="mb-0"><small>{{ now()->format('d/m/Y H:i') }}</small></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">📊 Resumen de Actividad</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-6">
                        <small class="text-muted">Sesión activa:</small>
                        <p class="mb-2">
                            <span class="badge bg-success">Activa</span>
                        </p>
                    </div>

                    <div class="col-6">
                        <small class="text-muted">Versión:</small>
                        <p class="mb-2"><strong>CODENAME FreeOLT Alpha 0.1.9</strong></p>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">Sistema:</small>
                        <p class="mb-0"><small>CODENAME FreeOLT - SOLUCIONES TECNOLÓGICAS SUMMA DE MÉXICO</small></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- NUEVA SECCIÓN: Acceso a Vista de Rendimiento -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-gradient-info text-white">
                <h5 class="card-title mb-0">📈 Acceso a Métricas de Rendimiento</h5>
            </div>
            <div class="card-body text-center py-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">

                        <div class="mb-4">
                            <i class="fas fa-chart-line fa-4x text-info mb-3"></i>
                            <h3>Estado General del Sistema</h3>
                            <p class="text-muted">
                                Acceso a métricas, gráficos en tiempo real y un análisis completo 
                                del rendimiento de la red.
                            </p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="feature-item">
                                    <i class="fas fa-tachometer-alt text-primary fa-2x mb-2"></i>
                                    <h6>Métricas en Tiempo Real</h6>
                                    <small class="text-muted">Monitorización activa</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="feature-item">
                                    <i class="fas fa-chart-bar text-success fa-2x mb-2"></i>
                                    <h6>Gráficos Interactivos</h6>
                                    <small class="text-muted">Análisis visual</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="feature-item">
                                    <i class="fas fa-bell text-warning fa-2x mb-2"></i>
                                    <h6>Alertas Inteligentes</h6>
                                    <small class="text-muted">Detección proactiva</small>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('performance.metrics') }}" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-arrow-right me-2"></i>
                            Acceder al Panel de Rendimiento
                        </a>

                    </div>
                </div>
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
.btn {
    transition: all 0.2s;
}
.display-6 {
    font-size: 2rem;
    font-weight: bold;
}
.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #0d2ec5ff 100%);
}
.feature-item {
    padding: 1rem;
    border-radius: 10px;
    transition: all 0.3s ease;
}
.feature-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-5px);
}
.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}
</style>

@endsection
