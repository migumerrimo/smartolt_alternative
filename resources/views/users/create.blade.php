@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Registrar Nuevo Usuario</h2>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="role" class="form-select">
                <option value="admin">Administrador</option>
                <option value="technician">Técnico</option>
                <option value="support">Soporte</option>
                <option value="customer">Cliente</option>
                <option value="read-only">Solo lectura</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="active" value="1" checked class="form-check-input" id="activeCheck">
            <label for="activeCheck" class="form-check-label">Usuario activo</label>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
