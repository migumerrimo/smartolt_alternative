@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Historial de Cambios del Sistema</h2>
    <p class="text-muted">Registros detallados de todos los movimientos realizados en el sistema: usuarios, OLTs, ONUs, alertas, switches, etc.</p>

    <!-- Filtros de búsqueda -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('change-history.index') }}" class="row g-3">
                <!-- Búsqueda general -->
                <div class="col-md-6">
                    <label for="search" class="form-label">Buscar por palabra clave:</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Ejemplo: admin, ONU-01, error, actualización..." 
                           value="{{ request('search') }}">
                </div>

                <!-- Filtro por tipo de dispositivo -->
                <div class="col-md-3">
                    <label for="device_type" class="form-label">Filtrar por tipo:</label>
                    <select name="device_type" id="device_type" class="form-select">
                        <option value="">-- Todos --</option>
                        <option value="OLT" {{ request('device_type') == 'OLT' ? 'selected' : '' }}>OLT</option>
                        <option value="ONU" {{ request('device_type') == 'ONU' ? 'selected' : '' }}>ONU</option>
                        <option value="ROUTER" {{ request('device_type') == 'ROUTER' ? 'selected' : '' }}>Router</option>
                        <option value="SWITCH" {{ request('device_type') == 'SWITCH' ? 'selected' : '' }}>Switch</option>
                        <option value="SERVER" {{ request('device_type') == 'SERVER' ? 'selected' : '' }}>Servidor</option>
                    </select>
                </div>

                <!-- Filtro por usuario -->
                <div class="col-md-3">
                    <label for="user_id" class="form-label">Filtrar por usuario:</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">-- Todos --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botones -->
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <a href="{{ route('change-history.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de resultados -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="bi bi-clock-history"></i> Registro Global de Cambios
            </h5>

            @if($changes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Usuario</th>
                            <th>Dispositivo</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Comando Ejecutado</th>
                            <th>Resultado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($changes as $change)
                        @php
                            $color = match($change->device_type) {
                                'OLT' => 'table-primary',
                                'ONU' => 'table-success',
                                'ROUTER' => 'table-info',
                                'SWITCH' => 'table-warning',
                                'SERVER' => 'table-secondary',
                                default => ''
                            };
                        @endphp
                        <tr class="{{ $color }}">
                            <td>{{ $change->id }}</td>
                            <td>{{ $change->user->name ?? 'Sistema' }}</td>
                            <td>{{ $change->device_name ?? 'N/A' }}</td>
                            <td>{{ $change->device_type }}</td>
                            <td>{{ Str::limit($change->description, 60) }}</td>
                            <td>
                                @if($change->command)
                                    <code>{{ Str::limit($change->command, 50) }}</code>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($change->result)
                                    <span class="text-success"><i class="bi bi-check-circle"></i> {{ Str::limit($change->result, 40) }}</span>
                                @else
                                    <span class="text-muted">Sin resultado</span>
                                @endif
                            </td>
                            <td>{{ $change->date->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No se encontraron registros con los filtros aplicados.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
