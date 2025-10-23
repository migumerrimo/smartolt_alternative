@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Encabezado con Botones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Usuarios</h2>
        <div class="btn-group">
            <!-- Botón para Vista Previa del PDF -->
            <a href="{{ route('users.report.preview') }}" 
               class="btn btn-outline-info btn-sm" 
               target="_blank">
                <i class="fas fa-eye me-1"></i>Vista Previa
            </a>
            
            <!-- Botón para Descargar PDF -->
            <a href="{{ route('users.report.pdf') }}" 
               class="btn btn-outline-success btn-sm">
                <i class="fas fa-download me-1"></i>Descargar PDF
            </a>
            
            <!-- Botón Nuevo Usuario -->
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Nuevo Usuario
            </a>
        </div>
    </div>

    @if($users->count() > 0)
        <!-- Tarjetas de Resumen -->
        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $users->count() }}</h5>
                        <small class="card-text">Total Usuarios</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-success">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $users->where('active', true)->count() }}</h5>
                        <small class="card-text">Activos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $users->where('role', 'admin')->count() }}</h5>
                        <small class="card-text">Administradores</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-info">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $users->where('role', 'technician')->count() }}</h5>
                        <small class="card-text">Técnicos</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Activo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td><strong>#{{ $user->id }}</strong></td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'technician' ? 'warning' : 'success') }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->active ? 'success' : 'secondary' }}">
                                        {{ $user->active ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?')" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-users fa-2x mb-3"></i>
            <h4>No hay usuarios registrados aún</h4>
            <p>Comienza agregando el primer usuario al sistema.</p>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Crear Primer Usuario
            </a>
        </div>
    @endif
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
.table th {
    font-weight: 600;
}
.badge {
    font-size: 0.75em;
}
</style>
@endsection