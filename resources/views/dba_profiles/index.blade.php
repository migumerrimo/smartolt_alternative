@extends('layouts.app')

@section('content')

<div class="container">
    <h2>Perfiles DBA</h2>

    <!-- Formulario para obtener perfiles del OLT -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Obtener Perfiles DBA del OLT</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dba-profiles.index') }}" class="form-inline">
                <div class="form-group mr-3">
                    <label for="olt_id" class="mr-2">Seleccionar OLT:</label>
                    <select name="olt_id" id="olt_id" class="form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($olts as $olt)
                            <option value="{{ $olt->id }}" {{ $selectedOltId == $olt->id ? 'selected' : '' }}>
                                {{ $olt->name }} ({{ $olt->management_ip }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" name="fetch_from_olt" value="1" class="btn btn-primary">
                    Obtener del OLT
                </button>
            </form>
        </div>
    </div>

    <!-- Tabla de DBA Profiles del OLT (si se obtuvo) -->
    @if($oltDbaProfiles)
        <div class="card mb-4">
            <div class="card-header">
                <h5>Perfiles DBA del OLT</h5>
            </div>
            <div class="card-body">
                @if(count($oltDbaProfiles) > 0)
                    <table class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Profile-ID</th>
                                <th>Type</th>
                                <th>Bandwidth Compensation</th>
                                <th>Fix (kbps)</th>
                                <th>Assure (kbps)</th>
                                <th>Max (kbps)</th>
                                <th>Bind Times</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($oltDbaProfiles as $profile)
                                <tr>
                                    <td>{{ $profile['profile_id'] }}</td>
                                    <td>{{ $profile['type'] }}</td>
                                    <td>{{ $profile['bandwidth_compensation'] }}</td>
                                    <td>{{ $profile['fix_kbps'] }}</td>
                                    <td>{{ $profile['assure_kbps'] }}</td>
                                    <td>{{ $profile['max_kbps'] }}</td>
                                    <td>{{ $profile['bind_times'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No hay perfiles DBA en el OLT.</p>
                @endif
            </div>
        </div>
    @endif

    <!-- Tabla de DBA Profiles en la Base de Datos -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Perfiles DBA en Base de Datos</h5>
            <a href="{{ route('dba-profiles.create') }}" class="btn btn-primary btn-sm">Nuevo Perfil DBA</a>
        </div>
        <div class="card-body">
            @if($dbaProfiles->count() > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>OLT</th>
                            <th>Profile-ID</th>
                            <th>Type</th>
                            <th>Bandwidth Comp.</th>
                            <th>Fix (kbps)</th>
                            <th>Assure (kbps)</th>
                            <th>Max (kbps)</th>
                            <th>Bind Times</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dbaProfiles as $profile)
                            <tr>
                                <td>{{ $profile->id }}</td>
                                <td>{{ $profile->olt->name ?? 'N/A' }}</td>
                                <td>{{ $profile->profile_id ?? '-' }}</td>
                                <td>{{ $profile->type ?? '-' }}</td>
                                <td>{{ $profile->bandwidth_compensation ?? '-' }}</td>
                                <td>{{ $profile->fix_kbps ?? '-' }}</td>
                                <td>{{ $profile->assure_kbps ?? '-' }}</td>
                                <td>{{ $profile->max_kbps ?? '-' }}</td>
                                <td>{{ $profile->bind_times ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('dba-profiles.show',$profile) }}" class="btn btn-sm btn-info">Ver</a>
                                    <a href="{{ route('dba-profiles.edit',$profile) }}" class="btn btn-sm btn-warning">Editar</a>
                                    <form action="{{ route('dba-profiles.destroy',$profile) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">No hay perfiles DBA en la base de datos.</p>
            @endif
        </div>
    </div>
</div>

@endsection
