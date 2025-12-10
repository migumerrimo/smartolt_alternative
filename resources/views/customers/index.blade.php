@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestión de Clientes</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <!-- Botón para Vista Previa del PDF -->
            <a href="{{ route('customers.report.preview') }}" 
               class="btn btn-outline-info btn-sm" 
               target="_blank">
                <i class="bi bi-eye"></i> Vista Previa
            </a>
            
            <!-- Botón para Descargar PDF -->
            <a href="{{ route('customers.report.pdf') }}" 
               class="btn btn-outline-success btn-sm">
                <i class="bi bi-download"></i> Descargar PDF
            </a>
        </div>
        <a href="{{ route('customers.create') }}" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Nuevo Cliente
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Tarjetas de Resumen -->
<div class="row mb-4">
    <div class="col-md-2 mb-2">
        <div class="card text-white bg-primary">
            <div class="card-body text-center py-3">
                <h5 class="card-title mb-0">{{ $customers->count() }}</h5>
                <small class="card-text">Total</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-2">
        <div class="card text-white bg-info">
            <div class="card-body text-center py-3">
                <h5 class="card-title mb-0">{{ $customers->where('customer_type', 'residential')->count() }}</h5>
                <small class="card-text">Residencial</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-2">
        <div class="card text-white bg-success">
            <div class="card-body text-center py-3">
                <h5 class="card-title mb-0">{{ $customers->where('customer_type', 'business')->count() }}</h5>
                <small class="card-text">Empresarial</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-2">
        <div class="card text-white bg-warning">
            <div class="card-body text-center py-3">
                <h5 class="card-title mb-0">{{ $customers->where('customer_type', 'corporate')->count() }}</h5>
                <small class="card-text">Corporativo</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-2">
        <div class="card text-white bg-dark">
            <div class="card-body text-center py-3">
                <h5 class="card-title mb-0">
                    @php
                        // CORRECCIÓN: Usar assignedOnus en lugar de customerAssignments
                        $customersWithOnus = $customers->filter(function($customer) {
                            return $customer->assignedOnus->count() > 0;
                        })->count();
                    @endphp
                    {{ $customersWithOnus }}
                </h5>
                <small class="card-text">Con ONUs</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-people-fill"></i> Lista de Clientes
    </div>
    <div class="card-body">
        @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Tipo</th>
                            <th>Dirección</th>
                            <th>ONUs</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer->user->name }}</td>
                            <td>{{ $customer->user->email }}</td>
                            <td>{{ $customer->user->phone }}</td>
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
                            <td>{{ Str::limit($customer->address, 30) }}</td>
                            <td>
                                {{-- CORRECCIÓN: Usar assignedOnus en lugar de customerAssignments --}}
                                @if($customer->assignedOnus->count() > 0)
                                    <span class="badge bg-success">{{ $customer->assignedOnus->count() }}</span>
                                @else
                                    <span class="badge bg-secondary">0</span>
                                @endif
                            </td>
                            <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-primary" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Eliminar" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $customer->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Modal de Confirmación de Eliminación -->
                                <div class="modal fade" id="deleteModal{{ $customer->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Estás seguro de que deseas eliminar al cliente <strong>{{ $customer->user->name }}</strong>?</p>
                                                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('customers.destroy', $customer) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Eliminar Cliente</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-people display-1 text-muted"></i>
                <h4 class="text-muted">No hay clientes registrados</h4>
                <p class="text-muted">Comienza agregando tu primer cliente al sistema.</p>
                <a href="{{ route('customers.create') }}" class="btn btn-custom-green">
                    <i class="bi bi-person-plus"></i> Registrar Primer Cliente
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
.btn-group .btn {
    margin: 0 2px;
}
.btn-custom-green {
    background-color: #00C853;
    border-color: #00C853;
    color: #fff;
}
.btn-custom-green:hover {
    background-color: #009b3a;
    border-color: #009b3a;
    color: #fff;
}
</style>
@endsection