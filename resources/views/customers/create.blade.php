@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Registrar Nuevo Cliente</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-person-plus"></i> Información del Cliente
    </div>
    <div class="card-body">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nombre Completo *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Teléfono *</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="customer_type" class="form-label">Tipo de Cliente *</label>
                    <select class="form-select @error('customer_type') is-invalid @enderror" 
                            id="customer_type" name="customer_type" required>
                        <option value="">Seleccionar tipo...</option>
                        <option value="residential" {{ old('customer_type') == 'residential' ? 'selected' : '' }}>Residencial</option>
                        <option value="business" {{ old('customer_type') == 'business' ? 'selected' : '' }}>Empresarial</option>
                        <option value="corporate" {{ old('customer_type') == 'corporate' ? 'selected' : '' }}>Corporativo</option>
                    </select>
                    @error('customer_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Dirección *</label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="document_number" class="form-label">Número de Documento</label>
                <input type="text" class="form-control @error('document_number') is-invalid @enderror" 
                       id="document_number" name="document_number" value="{{ old('document_number') }}">
                @error('document_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Guardar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection