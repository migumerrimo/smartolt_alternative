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
                        <p class="mb-2"><strong>FreeOLT Alpha 0.1.0</strong></p>
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
</style>
@endsection