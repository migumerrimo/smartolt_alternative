@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Detalles del Usuario</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title">{{ $user->name }}</h4>
            <p><strong>Correo:</strong> {{ $user->email }}</p>
            <p><strong>Rol:</strong> {{ ucfirst($user->role) }}</p>
            <p><strong>Teléfono:</strong> {{ $user->phone ?? 'No registrado' }}</p>
            <p><strong>Estado:</strong> 
                @if($user->active)
                    <span class="badge bg-success">Activo</span>
                @else
                    <span class="badge bg-secondary">Inactivo</span>
                @endif
            </p>
            <p><strong>Registrado en:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('users.edit',$user) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
