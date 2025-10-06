@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Usuario</h2>

    <form action="{{ route('users.update',$user) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="role" class="form-select">
                <option value="admin" @selected($user->role=='admin')>Administrador</option>
                <option value="technician" @selected($user->role=='technician')>Técnico</option>
                <option value="support" @selected($user->role=='support')>Soporte</option>
                <option value="customer" @selected($user->role=='customer')>Cliente</option>
                <option value="read-only" @selected($user->role=='read-only')>Solo lectura</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="phone" value="{{ $user->phone }}" class="form-control">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="active" value="1" class="form-check-input" id="activeCheck" 
                   @checked($user->active)>
            <label for="activeCheck" class="form-check-label">Usuario activo</label>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
