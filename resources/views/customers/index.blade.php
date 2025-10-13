@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestión de Clientes</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
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
                            <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-primary" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
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
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Registrar Primer Cliente
                </a>
            </div>
        @endif
    </div>
</div>
@endsection