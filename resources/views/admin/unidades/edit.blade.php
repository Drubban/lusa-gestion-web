@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Unidad</h1>
        <a href="{{ route('admin.unidades.index') }}" class="btn btn-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.unidades.update', $unidad) }}">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Número económico *</label>
                        <input type="text" name="numero_economico" value="{{ old('numero_economico', $unidad->numero_economico) }}" class="form-control @error('numero_economico') is-invalid @enderror" required>
                        @error('numero_economico') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre de la unidad</label>
                        <input type="text" name="nombre_unidad" value="{{ old('nombre_unidad', $unidad->nombre_unidad) }}" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Zona *</label>
                        <select name="zona_id" class="form-select @error('zona_id') is-invalid @enderror" required>
                            <option value="">Seleccione una zona</option>
                            @foreach($zonas as $zona)
                                <option value="{{ $zona->id }}" {{ old('zona_id', $unidad->zona_id) == $zona->id ? 'selected' : '' }}>
                                    {{ ucfirst($zona->nombre) }}
                                </option>
                            @endforeach
                        </select>
                        @error('zona_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Asignar operador</label>
                        <select name="operador_id" class="form-select">
                            <option value="">-- Sin operador --</option>
                            @foreach($operadores as $operador)
                                <option value="{{ $operador->id }}" {{ old('operador_id', $operadorActual->id ?? '') == $operador->id ? 'selected' : '' }}>
                                    {{ $operador->nombre_completo }} ({{ $operador->clave_operador }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" {{ old('activo', $unidad->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar unidad</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection