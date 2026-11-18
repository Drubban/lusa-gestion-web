@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Usuario de App</h1>
        <a href="{{ route('admin.usuarios-app.index') }}" class="btn btn-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.usuarios-app.update', $usuario) }}">
                @csrf @method('PUT')

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre de usuario *</label>
                        <input type="text" name="nombre_usuario" class="form-control @error('nombre_usuario') is-invalid @enderror" value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}" required>
                        @error('nombre_usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contraseña (dejar vacío para no cambiar)</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Departamento *</label>
                        <select name="departamento_id" class="form-select" required>
                            @foreach($departamentos as $depto)
                            <option value="{{ $depto->id }}" @selected(old('departamento_id', $usuario->departamento_id)==$depto->id)>{{ ucfirst($depto->nombre) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="puede_generar_documentos" class="form-check-input" value="1" @checked(old('puede_generar_documentos', $usuario->puede_generar_documentos))>
                            <label class="form-check-label">Puede generar documentos</label>
                        </div>
                        <div class="form-check mt-2">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" @checked(old('activo', $usuario->activo))>
                            <label class="form-check-label">Activo</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection