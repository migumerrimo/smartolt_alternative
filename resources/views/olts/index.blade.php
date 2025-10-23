@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Encabezado con Botones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>OLTs</h2>
        <div class="btn-group">
            <!-- Botón para Vista Previa del PDF -->
            <a href="{{ route('olts.report.preview') }}" 
               class="btn btn-outline-info btn-sm" 
               target="_blank">
                <i class="fas fa-eye me-1"></i>Vista Previa
            </a>
            
            <!-- Botón para Descargar PDF -->
            <a href="{{ route('olts.report.pdf') }}" 
               class="btn btn-outline-success btn-sm">
                <i class="fas fa-download me-1"></i>Descargar PDF
            </a>
            
            <!-- Botón Nueva OLT -->
            <a href="{{ route('olts.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Nueva OLT
            </a>
        </div>
    </div>

    @if($olts->count() > 0)
        <!-- Tarjetas de Resumen -->
        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $olts->count() }}</h5>
                        <small class="card-text">Total OLTs</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-success">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $olts->where('status', 'active')->count() }}</h5>
                        <small class="card-text">OLTs Activas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $olts->where('status', 'maintenance')->count() }}</h5>
                        <small class="card-text">En Mantenimiento</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-info">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $olts->where('status', 'inactive')->count() }}</h5>
                        <small class="card-text">OLTs Inactivas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de OLTs -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Modelo</th>
                                <th>IP</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($olts as $olt)
                            <tr>
                                <td><strong>#{{ $olt->id }}</strong></td>
                                <td>{{ $olt->name }}</td>
                                <td>{{ $olt->model }}</td>
                                <td>{{ $olt->management_ip }}</td>
                                <td>
                                    <span class="badge bg-{{ $olt->status === 'active' ? 'success' : ($olt->status === 'maintenance' ? 'warning' : 'secondary') }}">
                                        {{ $olt->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('olts.show', $olt) }}" class="btn btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('olts.edit', $olt) }}" class="btn btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('olts.destroy', $olt) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta OLT?')" title="Eliminar">
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
            <i class="fas fa-network-wired fa-2x mb-3"></i>
            <h4>No hay OLTs registradas aún</h4>
            <p>Comienza agregando la primera OLT al sistema.</p>
            <a href="{{ route('olts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Registrar Primera OLT
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
    text-transform: capitalize;
}
</style>
@endsection