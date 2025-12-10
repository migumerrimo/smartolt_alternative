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

        <!-- 🔍 Sección de Búsqueda y Filtros -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="bi bi-funnel"></i> Filtros y Búsqueda</h5>
            
            <form method="GET" action="{{ route('alarms.index') }}" class="row g-3">
                <!-- Búsqueda general -->
                <div class="col-md-4">
                    <label for="search" class="form-label">🔍 Búsqueda general</label>
                    <input type="text" class="form-control" id="search" name="search" 
                        placeholder="Buscar en mensajes, OLT, ONU..." 
                        value="{{ $filters['search'] ?? '' }}">
                </div>

                <!-- Filtro por severidad -->
                <div class="col-md-2">
                    <label for="severity" class="form-label">🎯 Severidad</label>
                    <select class="form-select" id="severity" name="severity">
                        <option value="all" {{ ($filters['severity'] ?? '') == 'all' ? 'selected' : '' }}>Todas</option>
                        <option value="critical" {{ ($filters['severity'] ?? '') == 'critical' ? 'selected' : '' }}>Críticas</option>
                        <option value="major" {{ ($filters['severity'] ?? '') == 'major' ? 'selected' : '' }}>Mayores</option>
                        <option value="minor" {{ ($filters['severity'] ?? '') == 'minor' ? 'selected' : '' }}>Menores</option>
                        <option value="warning" {{ ($filters['severity'] ?? '') == 'warning' ? 'selected' : '' }}>Advertencias</option>
                        <option value="info" {{ ($filters['severity'] ?? '') == 'info' ? 'selected' : '' }}>Informativas</option>
                    </select>
                </div>

                <!-- Filtro por tipo de dispositivo -->
                <div class="col-md-2">
                    <label for="device_type" class="form-label">📡 Dispositivo</label>
                    <select class="form-select" id="device_type" name="device_type">
                        <option value="all" {{ ($filters['device_type'] ?? '') == 'all' ? 'selected' : '' }}>Todos</option>
                        <option value="olt" {{ ($filters['device_type'] ?? '') == 'olt' ? 'selected' : '' }}>Solo OLTs</option>
                        <option value="onu" {{ ($filters['device_type'] ?? '') == 'onu' ? 'selected' : '' }}>Solo ONUs</option>
                    </select>
                </div>

                <!-- Filtro por tiempo -->
                <div class="col-md-2">
                    <label for="time_filter" class="form-label">🕒 Período</label>
                    <select class="form-select" id="time_filter" name="time_filter">
                        <option value="all" {{ ($filters['time_filter'] ?? '') == 'all' ? 'selected' : '' }}>Todo el tiempo</option>
                        <option value="1h" {{ ($filters['time_filter'] ?? '') == '1h' ? 'selected' : '' }}>Última hora</option>
                        <option value="6h" {{ ($filters['time_filter'] ?? '') == '6h' ? 'selected' : '' }}>Últimas 6 horas</option>
                        <option value="24h" {{ ($filters['time_filter'] ?? '') == '24h' ? 'selected' : '' }}>Últimas 24 horas</option>
                        <option value="7d" {{ ($filters['time_filter'] ?? '') == '7d' ? 'selected' : '' }}>Últimos 7 días</option>
                    </select>
                </div>

                <!-- Botones de acción -->
                <div class="col-md-2 d-flex align-items-end">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-filter">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="{{ route('alarms.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>

            <!-- Mostrar filtros activos -->
            @if(isset($filters) && array_filter($filters))
                <div class="mt-3">
                    <small class="text-muted">Filtros activos:</small>
                    @foreach($filters as $key => $value)
                        @if($value && $value != 'all')
                            <span class="badge bg-light text-dark ms-2">
                                {{ $key }}: {{ $value }}
                            </span>
                        @endif
                    @endforeach
                    <a href="{{ route('alarms.index') }}" class="text-danger small ms-2">
                        <i class="bi bi-x-circle"></i> Limpiar todos
                    </a>
                </div>
            @endif
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
            <h5 class="card-title mb-3">
                <i class="bi bi-exclamation-circle"></i> 
                Alertas activas
                @if($alarms->count() > 0)
                    <span class="badge bg-primary">{{ $alarms->count() }}</span>
                @endif
            </h5>

            @if($alarms->count() > 0)
            <div class="table-responsive">
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
            </div>
            @else
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle-fill"></i> 
                    @if(isset($filters) && array_filter($filters))
                        No se encontraron alertas con los filtros aplicados.
                    @else
                        No se han detectado alertas activas en la red.
                    @endif
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

<style>
/* botón de filtro con tonalidad personalizada (más específico para sobreescribir Bootstrap) */
button.btn-filter {
    background-color: #2dd70bff !important; /* tono azul ligeramente distinto */
    border-color: #2dd70bff !important;
    color: #fff !important;
    box-shadow: none !important;
}
button.btn-filter:hover {
    background-color: #094bb5 !important;
    border-color: #094bb5 !important;
    color: #fff !important;
}
</style>
