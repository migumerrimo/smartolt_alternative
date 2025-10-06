@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- HEADER: Estado General + Métricas Clave -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-dark text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h1 class="card-title mb-1">🌐 Panel de Monitoreo de Red</h1>
                            <p class="card-text mb-0">Estado general en tiempo real de la infraestructura OLT/GPON</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="row">
                                <div class="col-4">
                                    <small class="d-block">Salud General</small>
                                    <h2 class="mb-0">{{ $health_score ?? 95 }}%</h2>
                                </div>
                                <div class="col-4">
                                    <small class="d-block">Uptime</small>
                                    <h2 class="mb-0">{{ $uptime ?? '99.8%' }}</h2>
                                </div>
                                <div class="col-4">
                                    <small class="d-block">Estado</small>
                                    <span class="badge bg-{{ $global_status === 'stable' ? 'success' : ($global_status === 'degraded' ? 'warning' : 'danger') }} fs-6">
                                        @if($global_status === 'stable') ✅ Estable
                                        @elseif($global_status === 'degraded') ⚠️ Degradado
                                        @else ❌ Crítico
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CARDS PRINCIPALES: OLTs, ONUs, Alarmas, Rendimiento -->
    <div class="row mb-4">
        <!-- OLTs -->
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">🔌 OLTs</h6>
                </div>
                <div class="card-body text-center">
                    <h1 class="display-4 text-primary">{{ $olts_active ?? 8 }}/{{ $olts_total ?? 10 }}</h1>
                    <p class="mb-1">Activas / Totales</p>
                    <div class="progress mb-2">
                        @php
                            $olts_percentage = (($olts_active ?? 8) / ($olts_total ?? 10)) * 100;
                        @endphp
                        <div class="progress-bar bg-primary" style="width: {{ $olts_percentage }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($olts_percentage, 1) }}% Disponibilidad</small>
                </div>
            </div>
        </div>

        <!-- ONUs -->
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">📡 ONUs</h6>
                </div>
                <div class="card-body text-center">
                    <h1 class="display-4 text-success">{{ $onus_online }}/{{ $onus_total }}</h1>
                    <p class="mb-1">Online / Totales</p>
                    <div class="progress mb-2">
                        @php
                            $onus_percentage = $onus_total > 0 ? ($onus_online / $onus_total) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $onus_percentage }}%"></div>
                    </div>
                    <small class="text-muted">
                        {{ number_format($onus_percentage, 1) }}% Conectadas<br>
                        <small class="text-muted">{{ $onus_registered }} reg. | {{ $onus_authenticated }} auth.</small>
                    </small>
                </div>
            </div>
        </div>

        <!-- Alarmas -->
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="card-title mb-0">🚨 Alarmas</h6>
                </div>
                <div class="card-body text-center">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-danger mb-0">{{ $alarms_critical ?? 0 }}</h4>
                            <small>Críticas</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning mb-0">{{ $alarms_major ?? 0 }}</h4>
                            <small>Mayores</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-info mb-0">{{ $alarms_minor ?? 0 }}</h4>
                            <small>Menores</small>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Total: {{ $alarms_active ?? 0 }} activas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rendimiento -->
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">📈 Rendimiento</h6>
                </div>
                <div class="card-body text-center">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-info mb-0">{{ $bandwidth_usage ?? 65 }}%</h4>
                            <small>BW Usado</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-0">{{ $latency ?? 15 }}ms</h4>
                            <small>Latencia</small>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Packet Loss: {{ $packet_loss ?? 0.1 }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN: OLTs DETALLADAS -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">🔍 OLTs - Estado Detallado</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nombre OLT</th>
                                    <th>Estado</th>
                                    <th>IP Management</th>
                                    <th>ONUs Online</th>
                                    <th>Ubicación</th>
                                    <th>Alarmas Activas</th>
                                    <th>Registrada</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($olts_detailed as $olt)
                                <tr>
                                    <td>
                                        <i class="fas fa-network-wired text-primary me-2"></i>
                                        {{ $olt->name }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $olt->status === 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($olt->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $olt->management_ip }}</small>
                                    </td>
                                    <td>
                                        <span class="text-success">{{ $olt->onus_online_count }}</span>/
                                        <span class="text-muted">{{ $olt->onus_total_count }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $olt->location ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $olt->active_alarms_count > 0 ? 'danger' : 'success' }}">
                                            {{ $olt->active_alarms_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $olt->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-chart-bar"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRÁFICOS Y MÉTRICAS -->
    <div class="row mb-4">
        <!-- Métricas de Calidad de Servicio -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">📊 Métricas de Calidad</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-3">
                                <h4 class="text-{{ $bandwidth_usage > 80 ? 'danger' : 'info' }}">{{ $bandwidth_usage }}%</h4>
                                <small>BW Usado</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <h4 class="text-{{ $latency > 50 ? 'warning' : 'success' }}">{{ $latency }}ms</h4>
                                <small>Latencia</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <h4 class="text-{{ $packet_loss > 1 ? 'danger' : 'secondary' }}">{{ $packet_loss }}%</h4>
                                <small>Packet Loss</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-sync-alt me-1"></i>
                            Métricas actualizadas en tiempo real
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución de ONUs por Estado -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">📡 Distribución de ONUs</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="mb-2">
                                <h5 class="text-success mb-1">{{ $onus_online }}</h5>
                                <small class="text-success">Online</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="mb-2">
                                <h5 class="text-primary mb-1">{{ $onus_registered }}</h5>
                                <small class="text-primary">Registradas</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="mb-2">
                                <h5 class="text-info mb-1">{{ $onus_authenticated }}</h5>
                                <small class="text-info">Autenticadas</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="mb-2">
                                <h5 class="text-danger mb-1">{{ $onus_offline }}</h5>
                                <small class="text-danger">Offline</small>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 10px;">
                        @php
                            $online_percent = $onus_total > 0 ? ($onus_online / $onus_total) * 100 : 0;
                            $registered_percent = $onus_total > 0 ? ($onus_registered / $onus_total) * 100 : 0;
                            $auth_percent = $onus_total > 0 ? ($onus_authenticated / $onus_total) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $online_percent }}%"></div>
                        <div class="progress-bar bg-primary" style="width: {{ $registered_percent }}%"></div>
                        <div class="progress-bar bg-info" style="width: {{ $auth_percent }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ALARMAS RECIENTES -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">🚨 Alarmas Recientes</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($recent_alarms as $alarm)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-{{ $this->getSeverityColor($alarm->severity) }} me-2">
                                    {{ ucfirst($alarm->severity) }}
                                </span>
                                <small>{{ Str::limit($alarm->message, 50) }}</small>
                                <br>
                                <small class="text-muted">
                                    {{ $alarm->olt->name }} 
                                    @if($alarm->onu)
                                    | ONU: {{ $alarm->onu->serial_number }}
                                    @endif
                                </small>
                            </div>
                            <small class="text-muted">{{ $alarm->detected_at->diffForHumans() }}</small>
                        </div>
                        @endforeach
                        
                        @if($recent_alarms->count() == 0)
                        <div class="list-group-item text-center text-muted">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            No hay alarmas activas
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MÉTRICAS DEL SISTEMA Y ACTIVIDAD -->
    <div class="row">
        <!-- Estado del Sistema -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">⚙️ Estado del Sistema</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-3">
                                <h5 class="text-{{ $cpu_usage > 80 ? 'danger' : 'success' }}">
                                    {{ $cpu_usage }}%
                                </h5>
                                <small>CPU</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <h5 class="text-{{ $memory_usage > 85 ? 'warning' : 'info' }}">
                                    {{ $memory_usage }}%
                                </h5>
                                <small>Memoria</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <h5 class="text-{{ $storage_usage > 90 ? 'danger' : 'secondary' }}">
                                    {{ $storage_usage }}%
                                </h5>
                                <small>Almacenamiento</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-server me-1"></i>
                            Sistema FreeOLT - Monitoreo activo
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">📋 Actividad del Sistema</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($recent_activity as $activity)
                        <div class="list-group-item d-flex align-items-center">
                            <i class="fas fa-{{ $this->getActivityIcon($activity->device_type) }} text-primary me-3"></i>
                            <div class="flex-grow-1">
                                <small>{{ $activity->description ?? 'Cambio en ' . $activity->device_type }}</small>
                                <br>
                                <small class="text-muted">
                                    {{ $activity->user->name ?? 'Sistema' }} • 
                                    {{ $activity->date->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($recent_activity->count() == 0)
                        <div class="list-group-item text-center text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay actividad reciente registrada
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.card {
    transition: transform 0.2s;
    margin-bottom: 1rem;
}
.card:hover {
    transform: translateY(-2px);
}
.bg-gradient-dark {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
}
.display-4 {
    font-size: 2.5rem;
    font-weight: bold;
}
.table th {
    border-top: none;
    font-weight: 600;
}
.list-group-item {
    border: none;
    padding: 0.75rem 0;
}
</style>

<!-- Scripts para gráficos futuros -->
@section('scripts')
<script>
// Aquí integraremos Chart.js posteriormente
console.log('Panel de monitoreo cargado - Listo para integración de gráficos');
</script>
@endsection

@endsection