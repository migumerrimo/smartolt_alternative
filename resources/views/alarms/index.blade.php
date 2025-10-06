@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Centro de Alertas y Monitoreo</h2>
    <p class="text-muted">Detección automática de anomalías en la infraestructura de red</p>

    <!-- Tarjetas resumen de estado general -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-danger shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Críticas</h5>
                    <h3 class="fw-bold">{{ $critical_count ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Mayores</h5>
                    <h3 class="fw-bold">{{ $major_count ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Menores</h5>
                    <h3 class="fw-bold">{{ $minor_count ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Informativas</h5>
                    <h3 class="fw-bold">{{ $info_count ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de detección automática -->
    <div class="alert alert-secondary">
        <i class="bi bi-cpu"></i> 
        <strong>Sistema de detección automática:</strong> monitoreando OLTs, ONUs, potencia óptica y uso de tráfico...
    </div>

    <!-- Tabla de alertas -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="bi bi-exclamation-circle"></i> Alertas activas</h5>

            @if($alarms->count() > 0)
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Dispositivo</th>
                        <th>Tipo</th>
                        <th>Severidad</th>
                        <th>Mensaje</th>
                        <th>Detectada</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alarms as $alarm)
                        @php
                            $color = match($alarm->severity) {
                                'critical' => 'bg-danger text-white',
                                'major' => 'bg-warning',
                                'minor' => 'bg-info',
                                'warning' => 'bg-secondary text-dark',
                                default => 'bg-light'
                            };
                        @endphp
                        <tr class="{{ $color }}">
                            <td>{{ $alarm->id }}</td>
                            <td>
                                @if($alarm->onu_id)
                                    ONU #{{ $alarm->onu_id }}
                                @else
                                    OLT #{{ $alarm->olt_id }}
                                @endif
                            </td>
                            <td>{{ strtoupper($alarm->severity) }}</td>
                            <td>
                                @switch($alarm->severity)
                                    @case('critical') 🔥 @break
                                    @case('major') ⚠️ @break
                                    @case('minor') 🧩 @break
                                    @case('warning') 🚧 @break
                                    @default 💬
                                @endswitch
                                {{ ucfirst($alarm->severity) }}
                            </td>
                            <td>{{ $alarm->message }}</td>
                            <td>{{ $alarm->detected_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($alarm->active)
                                    <span class="badge bg-danger">Activa</span>
                                @else
                                    <span class="badge bg-success">Resuelta</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle-fill"></i> 
                    No se han detectado alertas activas en la red.
                </div>
            @endif
        </div>
    </div>

    <!-- Panel de diagnóstico predictivo -->
    <div class="card mt-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-bar-chart-line"></i> Diagnóstico Predictivo</h5>
            <p class="text-muted">El sistema analiza métricas de telemetría y rendimiento de OLTs/ONUs para anticipar fallos potenciales.</p>
            <ul>
                <li>Monitoreo de potencia óptica (RX/TX) de ONUs.</li>
                <li>Revisión periódica de estado de puertos PON.</li>
                <li>Análisis de congestión de tráfico en VLANs y GEM Ports.</li>
                <li>Supervisión del CPU y memoria en OLTs.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
