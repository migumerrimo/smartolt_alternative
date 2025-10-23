@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Encabezado con Botones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>ONUs</h2>
        <div class="btn-group">
            <!-- Botón para Vista Previa del PDF -->
            <a href="{{ route('onus.report.preview') }}" 
               class="btn btn-outline-info btn-sm" 
               target="_blank">
                <i class="fas fa-eye me-1"></i>Vista Previa
            </a>
            
            <!-- Botón para Descargar PDF -->
            <a href="{{ route('onus.report.pdf') }}" 
               class="btn btn-outline-success btn-sm">
                <i class="fas fa-download me-1"></i>Descargar PDF
            </a>
            
            <!-- Botón Nueva ONU -->
            <a href="{{ route('onus.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Nueva ONU
            </a>
        </div>
    </div>

    @if($onus->count() > 0)
        <!-- Tarjetas de Resumen -->
        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $onus->count() }}</h5>
                        <small class="card-text">Total ONUs</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-success">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $onus->where('status', 'online')->count() }}</h5>
                        <small class="card-text">ONUs Online</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $onus->where('status', 'registered')->count() }}</h5>
                        <small class="card-text">ONUs Registradas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card text-white bg-danger">
                    <div class="card-body text-center py-3">
                        <h5 class="card-title mb-0">{{ $onus->where('status', 'offline')->count() }}</h5>
                        <small class="card-text">ONUs Offline</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de ONUs -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Serial</th>
                                <th>Modelo</th>
                                <th>PON Port</th>
                                <th>Estado</th>
                                <th>OLT</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($onus as $onu)
                            <tr>
                                <td class="fw-bold">{{ $onu->serial_number }}</td>
                                <td>{{ $onu->model }}</td>
                                <td>{{ $onu->pon_port }}</td>
                                <td>
                                    <span class="badge bg-{{ $onu->status === 'online' ? 'success' : ($onu->status === 'registered' ? 'warning' : 'danger') }}">
                                        {{ $onu->status }}
                                    </span>
                                </td>
                                <td>{{ $onu->olt->name }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('onus.show', $onu) }}" class="btn btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('onus.edit', $onu) }}" class="btn btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('onus.destroy', $onu) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta ONU?')" title="Eliminar">
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
            <i class="fas fa-wifi fa-2x mb-3"></i>
            <h4>No hay ONUs registradas aún</h4>
            <p>Comienza agregando la primera ONU al sistema.</p>
            <a href="{{ route('onus.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Registrar Primera ONU
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