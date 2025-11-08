@extends('layouts.app')

@section('content')

<!-- Sección principal: vista para asignar una ONU disponible a un cliente específico -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Asignar ONU a Cliente</h1>

    <!-- Botón de retorno al perfil del cliente -->
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('customers.show', $customer) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Cliente
        </a>
    </div>
</div>

<div class="row">
    <!-- Columna izquierda: formulario para asignar una ONU -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-router"></i> Seleccionar ONU para Asignar
            </div>
            <div class="card-body">
                @if($availableOnus->count() > 0)

                    <!-- Formulario de asignación de ONU al cliente -->
                    <form action="{{ route('customers.assign-onu.store', $customer) }}" method="POST">
                        @csrf
                        
                        <!-- Selección de ONU disponible -->
                        <div class="mb-3">
                            <label for="onu_id" class="form-label">Seleccionar ONU *</label>
                            <select class="form-select @error('onu_id') is-invalid @enderror" 
                                    id="onu_id" name="onu_id" required>
                                <option value="">Selecciona una ONU...</option>
                                @foreach($availableOnus as $onu)
                                <option value="{{ $onu->id }}" {{ old('onu_id') == $onu->id ? 'selected' : '' }}>
                                    {{ $onu->serial_number }} - {{ $onu->model }} (OLT: {{ $onu->olt->name }})
                                </option>
                                @endforeach
                            </select>
                            @error('onu_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campo para definir costo mensual -->
                        <div class="mb-3">
                            <label for="monthly_cost" class="form-label">Costo Mensual ($) *</label>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('monthly_cost') is-invalid @enderror" 
                                   id="monthly_cost" name="monthly_cost" 
                                   value="{{ old('monthly_cost', 0) }}" required>
                            @error('monthly_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campo para notas adicionales sobre la asignación -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas (Opcional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones de acción: cancelar o asignar -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Asignar ONU
                            </button>
                        </div>
                    </form>

                <!-- Mensaje cuando no hay ONUs disponibles -->
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-router display-4 text-muted"></i>
                        <h4 class="text-muted">No hay ONUs disponibles</h4>
                        <p class="text-muted">Todas las ONUs están asignadas a otros clientes.</p>
                        <a href="{{ route('onus.index') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Gestionar ONUs
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Columna derecha: información del cliente y estadísticas -->
    <div class="col-md-4">

        <!-- Tarjeta con información del cliente -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person"></i> Información del Cliente
            </div>
            <div class="card-body">
                <h6>{{ $customer->user->name }}</h6>
                <p class="mb-1"><strong>Email:</strong> {{ $customer->user->email }}</p>
                <p class="mb-1"><strong>Teléfono:</strong> {{ $customer->user->phone }}</p>
                <p class="mb-1"><strong>Dirección:</strong> {{ $customer->address }}</p>
                <p class="mb-0">
                    <strong>Tipo:</strong> 
                    @switch($customer->customer_type)
                        @case('residential')
                            <span class="badge bg-primary">Residencial</span>
                            @break
                        @case('business')
                            <span class="badge bg-success">Empresarial</span>
                            @break
                        @case('corporate')
                            <span class="badge bg-warning">Corporativo</span>
                            @break
                    @endswitch
                </p>
            </div>
        </div>

        <!-- Tarjeta con el conteo de ONUs disponibles -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> ONUs Disponibles
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h3 class="text-primary">{{ $availableOnus->count() }}</h3>
                    <p class="text-muted">ONUs listas para asignar</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
