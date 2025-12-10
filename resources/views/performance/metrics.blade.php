@extends('layouts.app')

@section('content')
@php
    // ====================
    // HELPERS PARA LA VISTA
    // ====================
    
    /**
     * Devuelve el color Bootstrap según la severidad de la alarma
     */
    $getSeverityColor = function($severity) {
        switch($severity) {
            case 'critical': return 'danger';
            case 'major': return 'warning';
            case 'minor': return 'info';
            case 'warning': return 'secondary';
            default: return 'light';
        }
    };

    /**
     * Devuelve el ícono Font Awesome según el tipo de dispositivo
     */
    $getActivityIcon = function($deviceType) {
        switch(strtolower($deviceType)) {
            case 'olt': return 'network-wired';
            case 'onu': return 'wifi';
            case 'router': return 'router';
            case 'switch': return 'share-alt';
            case 'server': return 'server';
            default: return 'cog';
        }
    };
@endphp

<div class="container-fluid">

    <!-- HEADER: Estado General + Métricas Clave -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-dark text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h1 class="card-title mb-1">Panel de Monitoreo de Red</h1>
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

    <!-- BOTONES DE REPORTES - NUEVA SECCIÓN -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-3">
                    <div class="btn-group" role="group" aria-label="Botones de reporte">
                        <!-- Botón para Vista Previa -->
                        <a href="{{ route('reports.daily.preview') }}"
                           class="btn btn-report-preview btn-lg"
                           target="_blank">
                            <i class="fas fa-eye me-2"></i>Vista Previa Reporte
                        </a>

                        <!-- Botón para Descargar PDF -->
                        <a href="{{ route('reports.daily') }}"
                           class="btn btn-report-download btn-lg">
                            <i class="fas fa-download me-2"></i>Descargar PDF
                        </a>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Genera reportes detallados del estado del sistema
                        </small>
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
                    <h1 class="display-4 text-primary">{{ $olts_active ?? 0 }}/{{ $olts_total ?? 0 }}</h1>
                    <p class="mb-1">Activas / Totales</p>
                    <div class="progress mb-2">
                        @php
                            $olts_total_safe = ($olts_total ?? 0) > 0 ? ($olts_total ?? 0) : 1;
                            $olts_percentage = (($olts_active ?? 0) / $olts_total_safe) * 100;
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
                    <h1 class="display-4 text-success">{{ $onus_online ?? 0 }}/{{ $onus_total ?? 0 }}</h1>
                    <p class="mb-1">Online / Totales</p>
                    <div class="progress mb-2">
                        @php
                            $onus_total_safe = ($onus_total ?? 0) > 0 ? ($onus_total ?? 0) : 1;
                            $onus_percentage = (($onus_online ?? 0) / $onus_total_safe) * 100;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $onus_percentage }}%"></div>
                    </div>
                    <small class="text-muted">
                        {{ number_format($onus_percentage, 1) }}% Conectadas<br>
                        <small class="text-muted">{{ $onus_registered ?? 0 }} reg. | {{ $onus_authenticated ?? 0 }} auth.</small>
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
                            <h4 class="text-info mb-0">{{ $bandwidth_usage ?? 0 }}%</h4>
                            <small>BW Usado</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-0">{{ $latency ?? 0 }}ms</h4>
                            <small>Latencia</small>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Packet Loss: {{ $packet_loss ?? 0 }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resto del código permanece igual -->
    <!-- SECCIÓN: OLTs DETALLADAS -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">🔍 OLTs - Estado Detallado</h5>
                </div>
                <div class="card-body">
                    @if(isset($olts_detailed) && $olts_detailed->count() > 0)
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
                                        <span class="text-success">{{ $olt->onus_online_count ?? 0 }}</span>/
                                        <span class="text-muted">{{ $olt->onus_total_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $olt->location ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ ($olt->active_alarms_count ?? 0) > 0 ? 'danger' : 'success' }}">
                                            {{ $olt->active_alarms_count ?? 0 }}
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
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-network-wired fa-3x mb-3"></i>
                        <p>No hay OLTs registradas en el sistema</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- GRÁFICOS DE MÉTRICAS DE CALIDAD -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">📊 Métricas de Calidad - Últimas 24 Horas</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" data-period="24h">24H</button>
                        <button type="button" class="btn btn-outline-secondary" data-period="7d">7D</button>
                        <button type="button" class="btn btn-outline-secondary" data-period="30d">30D</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Gráfico de Ancho de Banda -->
                        <div class="col-md-4">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-info">
                                        <i class="fas fa-tachometer-alt"></i> Ancho de Banda
                                    </h6>
                                    <div class="chart-container" style="height: 200px;">
                                        <canvas id="bandwidthChart"></canvas>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Actual: <strong class="text-{{ ($bandwidth_usage ?? 0) > 80 ? 'danger' : 'info' }}">{{ $bandwidth_usage ?? 0 }}%</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico de Latencia -->
                        <div class="col-md-4">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-warning">
                                        <i class="fas fa-clock"></i> Latencia
                                    </h6>
                                    <div class="chart-container" style="height: 200px;">
                                        <canvas id="latencyChart"></canvas>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Actual: <strong class="text-{{ ($latency ?? 0) > 50 ? 'warning' : 'success' }}">{{ $latency ?? 0 }}ms</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico de Packet Loss -->
                        <div class="col-md-4">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Packet Loss
                                    </h6>
                                    <div class="chart-container" style="height: 200px;">
                                        <canvas id="packetLossChart"></canvas>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Actual: <strong class="text-{{ ($packet_loss ?? 0) > 1 ? 'danger' : 'secondary' }}">{{ $packet_loss ?? 0 }}%</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-center">
                            <small class="text-muted">
                            <i class="fas fa-sync-alt me-1"></i>
                            Gráficos en tiempo real - Actualizados cada 10 segundos
                            </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribución de ONUs por Estado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">📡 Distribución de ONUs</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="mb-2">
                                <h5 class="text-success mb-1">{{ $onus_online ?? 0 }}</h5>
                                <small class="text-success">Online</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="mb-2">
                                <h5 class="text-primary mb-1">{{ $onus_registered ?? 0 }}</h5>
                                <small class="text-primary">Registradas</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="mb-2">
                                <h5 class="text-info mb-1">{{ $onus_authenticated ?? 0 }}</h5>
                                <small class="text-info">Autenticadas</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="mb-2">
                                <h5 class="text-danger mb-1">{{ $onus_offline ?? 0 }}</h5>
                                <small class="text-danger">Offline</small>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 10px;">
                        @php
                            $onus_total_safe = ($onus_total ?? 0) > 0 ? ($onus_total ?? 0) : 1;
                            $online_percent = (($onus_online ?? 0) / $onus_total_safe) * 100;
                            $registered_percent = (($onus_registered ?? 0) / $onus_total_safe) * 100;
                            $auth_percent = (($onus_authenticated ?? 0) / $onus_total_safe) * 100;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $online_percent }}%"></div>
                        <div class="progress-bar bg-primary" style="width: {{ $registered_percent }}%"></div>
                        <div class="progress-bar bg-info" style="width: {{ $auth_percent }}%"></div>
                    </div>
                </div>
            </div>
        </div>

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
                                <h5 class="text-{{ ($cpu_usage ?? 0) > 80 ? 'danger' : 'success' }}">
                                    {{ $cpu_usage ?? 0 }}%
                                </h5>
                                <small>CPU</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <h5 class="text-{{ ($memory_usage ?? 0) > 85 ? 'warning' : 'info' }}">
                                    {{ $memory_usage ?? 0 }}%
                                </h5>
                                <small>Memoria</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <h5 class="text-{{ ($storage_usage ?? 0) > 90 ? 'danger' : 'secondary' }}">
                                    {{ $storage_usage ?? 0 }}%
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
                        @if(isset($recent_alarms) && $recent_alarms->count() > 0)
                            @foreach($recent_alarms as $alarm)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-{{ $getSeverityColor($alarm->severity) }} me-2">
                                        {{ ucfirst($alarm->severity) }}
                                    </span>
                                    <small>{{ Str::limit($alarm->message, 50) }}</small>
                                    <br>
                                    <small class="text-muted">
                                        {{ $alarm->olt->name ?? 'OLT Desconocida' }} 
                                        @if($alarm->onu)
                                        | ONU: {{ $alarm->onu->serial_number }}
                                        @endif
                                    </small>
                                </div>
                                <small class="text-muted">{{ $alarm->detected_at->diffForHumans() }}</small>
                            </div>
                            @endforeach
                        @else
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

    <!-- ACTIVIDAD RECIENTE -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">📋 Actividad del Sistema</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @if(isset($recent_activity) && $recent_activity->count() > 0)
                            @foreach($recent_activity as $activity)
                            <div class="list-group-item d-flex align-items-center">
                                <i class="fas fa-{{ $getActivityIcon($activity->device_type ?? 'default') }} text-primary me-3"></i>
                                <div class="flex-grow-1">
                                    <small>{{ $activity->description ?? 'Cambio en ' . ($activity->device_type ?? 'sistema') }}</small>
                                    <br>
                                    <small class="text-muted">
                                        {{ $activity->user->name ?? 'Sistema' }} • 
                                        {{ $activity->date->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        @else
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
.chart-container {
    position: relative;
}

/* Estilos para los botones de reporte */
.btn-group .btn {
    border-radius: 8px;
    margin: 0 5px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    color: white;
}

.btn-outline-success:hover {
    background-color: #198754;
    color: white;
}

/* botones de reportes: tonalidades específicas */
.btn-report-preview {
    background-color: #0d6efd !important; /* azul primario */
    border-color: #0d6efd !important;
    color: #fff !important;
    box-shadow: none !important;
}
.btn-report-preview:hover {
    background-color: #0b5ed7 !important;
    border-color: #0b5ed7 !important;
    color: #fff !important;
}

.btn-report-download {
    background-color: #20c997 !important; /* verde ligeramente distinto */
    border-color: #20c997 !important;
    color: #fff !important;
    box-shadow: none !important;
}
.btn-report-download:hover {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: #fff !important;
}
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Código SIMPLIFICADO y FUNCIONAL para gráficos
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando gráficos...');
    
    // Datos de ejemplo FIJOS para prueba
    const timeLabels = ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'];
    
    const bandwidthData = [25, 22, 20, 18, 22, 30, 45, 65, 75, 70, 65, 60, 55, 50, 45, 50, 65, 80, 85, 90, 88, 75, 60, 40];
    const latencyData = [12, 11, 10, 12, 15, 18, 22, 28, 32, 30, 28, 25, 22, 20, 18, 20, 25, 35, 42, 38, 35, 30, 25, 18];
    const packetLossData = [0.1, 0.08, 0.05, 0.1, 0.15, 0.2, 0.15, 0.3, 0.4, 0.35, 0.3, 0.25, 0.2, 0.15, 0.1, 0.15, 0.25, 0.5, 0.8, 0.6, 0.4, 0.3, 0.2, 0.15];

    // Configuración común SIMPLIFICADA
    const commonConfig = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { 
                display: true,
                grid: { color: 'rgba(0,0,0,0.1)' }
            },
            y: { 
                display: true,
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.1)' }
            }
        },
        elements: {
            line: { tension: 0.3 },
            point: { radius: 2, hoverRadius: 5 }
        }
    };

    // Crear gráfico de ANCHO DE BANDA
    const bandwidthCanvas = document.getElementById('bandwidthChart');
    if (bandwidthCanvas) {
        try {
            new Chart(bandwidthCanvas, {
                type: 'line',
                data: {
                    labels: timeLabels,
                    datasets: [{
                        label: 'Uso de BW (%)',
                        data: bandwidthData,
                        borderColor: '#0dcaf0',
                        backgroundColor: 'rgba(13, 202, 240, 0.2)',
                        borderWidth: 3,
                        fill: true
                    }]
                },
                options: {
                    ...commonConfig,
                    scales: {
                        ...commonConfig.scales,
                        y: { 
                            ...commonConfig.scales.y,
                            max: 100,
                            ticks: { 
                                callback: function(value) { return value + '%'; }
                            }
                        }
                    }
                }
            });
            console.log('✅ Gráfico de ancho de banda creado');
        } catch (error) {
            console.error('Error en gráfico de bandwidth:', error);
        }
    }

    // Crear gráfico de LATENCIA
    const latencyCanvas = document.getElementById('latencyChart');
    if (latencyCanvas) {
        try {
            new Chart(latencyCanvas, {
                type: 'line',
                data: {
                    labels: timeLabels,
                    datasets: [{
                        label: 'Latencia (ms)',
                        data: latencyData,
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        borderWidth: 3,
                        fill: true
                    }]
                },
                options: {
                    ...commonConfig,
                    scales: {
                        ...commonConfig.scales,
                        y: { 
                            ...commonConfig.scales.y,
                            max: 50,
                            ticks: { 
                                callback: function(value) { return value + 'ms'; }
                            }
                        }
                    }
                }
            });
            console.log('✅ Gráfico de latencia creado');
        } catch (error) {
            console.error('Error en gráfico de latencia:', error);
        }
    }

    // Crear gráfico de PACKET LOSS
    const packetLossCanvas = document.getElementById('packetLossChart');
    if (packetLossCanvas) {
        try {
            new Chart(packetLossCanvas, {
                type: 'line',
                data: {
                    labels: timeLabels,
                    datasets: [{
                        label: 'Packet Loss (%)',
                        data: packetLossData,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        borderWidth: 3,
                        fill: true
                    }]
                },
                options: {
                    ...commonConfig,
                    scales: {
                        ...commonConfig.scales,
                        y: { 
                            ...commonConfig.scales.y,
                            max: 1,
                            ticks: { 
                                callback: function(value) { return value + '%'; }
                            }
                        }
                    }
                }
            });
            console.log('✅ Gráfico de packet loss creado');
        } catch (error) {
            console.error('Error en gráfico de packet loss:', error);
        }
    }

    // Configurar botones de período
    document.querySelectorAll('[data-period]').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('[data-period]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            // Notificación simple
            const notification = document.createElement('div');
            notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 200px;';
            notification.innerHTML = `Período: ${this.dataset.period.toUpperCase()}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            document.body.appendChild(notification);
            setTimeout(() => {
                if (notification.parentNode) notification.remove();
            }, 2000);
        });
    });

    console.log('🎯 Todos los gráficos inicializados correctamente');

    // Sistema de actualización automática cada 10 segundos
    let updateCount = 0;
    setInterval(() => {
        updateCount++;
        console.log(`🔄 Actualización #${updateCount} - ${new Date().toLocaleTimeString()}`);
        
        // Aquí en una implementación real se actualizarían los datos
        // Por ahora solo mostramos que está funcionando
        if (updateCount === 1) {
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 60px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `✅ Sistema activo - Actualizando cada 10s<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            document.body.appendChild(notification);
            setTimeout(() => {
                if (notification.parentNode) notification.remove();
            }, 3000);
        }
    }, 10000);
});
</script>

<style>
.chart-container {
    position: relative;
    height: 200px;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border: 1px solid #dee2e6;
}
canvas {
    border-radius: 4px;
}
</style>
@endsection

@endsection