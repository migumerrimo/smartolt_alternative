@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detalles del Cliente</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('customers.index') }}" class="btn btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <a href="{{ route('customers.assign-onu', $customer) }}" class="btn btn-success">
            <i class="bi bi-router"></i> Asignar ONU
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Información del Cliente -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge"></i> Información Personal
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Nombre:</th>
                        <td>{{ $customer->name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $customer->email }}</td>
                    </tr>
                    <tr>
                        <th>Teléfono:</th>
                        <td>{{ $customer->phone }}</td>
                    </tr>
                    <tr>
                        <th>Tipo:</th>
                        <td>
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
                        </td>
                    </tr>
                    <tr>
                        <th>Documento:</th>
                        <td>{{ $customer->document_number ?? 'No especificado' }}</td>
                    </tr>
                    <tr>
                        <th>Dirección:</th>
                        <td>{{ $customer->address }}</td>
                    </tr>
                    <tr>
                        <th>Fecha Registro:</th>
                        <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- ONUs Asignadas -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-router"></i> ONUs Asignadas</span>
                <span class="badge bg-primary">{{ $assignedOnus->count() }}</span>
            </div>
            <div class="card-body">
                @if($assignedOnus->count() > 0)
                    <div class="list-group">
                        @foreach($assignedOnus as $assignment)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $assignment->onu->serial_number }}</h6>
                                <small class="text-success">Activa</small>
                            </div>
                            <p class="mb-1">
                                <strong>OLT:</strong> {{ $assignment->onu->olt->name }}<br>
                                <strong>Modelo:</strong> {{ $assignment->onu->model }}<br>
                                <strong>Costo Mensual:</strong> ${{ number_format($assignment->monthly_cost, 2) }}
                            </p>
                            <small>Asignada el: {{ $assignment->assignment_date->format('d/m/Y') }}</small>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-router display-4 text-muted"></i>
                        <p class="text-muted mt-2">No hay ONUs asignadas</p>
                        <a href="{{ route('customers.assign-onu', $customer) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus"></i> Asignar Primera ONU
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection