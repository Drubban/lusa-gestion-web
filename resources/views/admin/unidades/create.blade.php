@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1>Nueva Unidad</h1>
    <a href="{{ route('admin.unidades.index') }}" class="btn btn-secondary">Volver</a>
</div>

<form method="POST" action="{{ route('admin.unidades.store') }}">
    @csrf
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Número económico *</label>
            <input type="text" name="numero_economico" class="form-control @error('numero_economico') is-invalid @enderror" required>
            @error('numero_economico') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Nombre de la unidad (opcional)</label>
            <input type="text" name="nombre_unidad" class="form-control">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Zona *</label>
            <select name="zona_id" class="form-select @error('zona_id') is-invalid @enderror" required>
                <option value="">Seleccione una zona</option>
                @foreach($zonas as $zona)
                <option value="{{ $zona->id }}" {{ old('zona_id') == $zona->id ? 'selected' : '' }}>
                    {{ ucfirst($zona->nombre) }}
                </option>
                @endforeach
            </select>
            @error('zona_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Asignar operador (opcional)</label>
            <select name="operador_id" class="form-select">
                <option value="">-- Sin operador --</option>
                @foreach($operadores as $operador)
                <option value="{{ $operador->id }}">{{ $operador->nombre_completo }} ({{ $operador->clave_operador }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <div class="form-check mt-4">
                <input type="checkbox"
                    name="activo"
                    class="form-check-input"
                    id="activo"
                    value="1"
                {{ old('activo', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">Activo</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Guardar unidad</button>
</form>
@endsection