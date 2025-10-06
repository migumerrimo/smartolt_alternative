@extends('layouts.app')

@section('content')
<h2>Editar Perfil DBA</h2>

<form method="POST" action="{{ route('dba-profiles.update',$dbaProfile) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="name" value="{{ $dbaProfile->name }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Tipo</label>
        <select name="type" class="form-select" required>
            @foreach(['type1','type2','type3','type4'] as $type)
                <option value="{{ $type }}" @if($dbaProfile->type==$type) selected @endif>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Max Bandwidth</label>
        <input type="number" name="max_bandwidth" value="{{ $dbaProfile->max_bandwidth }}" class="form-control" required>
    </div>
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection
