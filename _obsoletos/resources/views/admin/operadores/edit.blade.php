@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Operador</h1>
        <a href="{{ route('admin.operadores.index') }}" class="btn btn-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.operadores.update', $operador) }}">
                @csrf @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Clave operador (4 dígitos)</label>
                        <input type="text" name="clave_operador" value="{{ old('clave_operador', $operador->clave_operador) }}" class="form-control @error('clave_operador') is-invalid @enderror" required>
                        @error('clave_operador') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre completo</label>
                        <input type="text" name="nombre_completo" value="{{ old('nombre_completo', $operador->nombre_completo) }}" class="form-control @error('nombre_completo') is-invalid @enderror" required>
                        @error('nombre_completo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Asignar unidad</label>
                        <select name="unidad_id" class="form-select">
                            <option value="">-- Sin unidad --</option>
                            @foreach($unidades as $unidad)
                            <option value="{{ $unidad->id }}" @selected($unidadActual && $unidadActual->id == $unidad->id)>{{ $unidad->numero_economico }} - {{ $unidad->nombre_unidad }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="activo" class="form-check-input" @checked(old('activo', $operador->activo))>
                            <label class="form-check-label">Activo</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar operador</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection