@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Nuevo Documento de Mantenimiento</h1>
        <a href="{{ route('admin.documentos-mantenimiento.index') }}" class="btn btn-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.documentos-mantenimiento.store') }}">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Asignación (Operador + Unidad)</label>
                        <select name="asignacion_id" class="form-select @error('asignacion_id') is-invalid @enderror" required>
                            <option value="">Seleccione...</option>
                            @foreach($asignaciones as $asig)
                                <option value="{{ $asig->id }}">{{ $asig->operador->nombre_completo }} - {{ $asig->unidad->numero_economico }}</option>
                            @endforeach
                        </select>
                        @error('asignacion_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Rol (zona)</label>
                        <input type="text" name="rol" class="form-control @error('rol') is-invalid @enderror" placeholder="Ej: reyes, apaxco, citrus" required>
                        @error('rol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Tecnología reportada (seleccione una o varias)</label>
                        <div class="row">
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="barras"> BARRAS</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="gps"> GPS</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="varilla"> VARILLA</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="telpo"> TELPO</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="mdvr"> MDVR</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="camaras"> CÁMARAS</label></div>
                        </div>
                        @error('tecnologia') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prueba barras de conteo Optocontrol</label>
                        <select name="prueba_barras" class="form-select">
                            <option value="">Seleccione</option>
                            <option value="SI">SI</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Comentarios / Observaciones</label>
                        <textarea name="comentarios" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha</label>
                        <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                        @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Hora</label>
                        <input type="time" name="hora" class="form-control @error('hora') is-invalid @enderror" value="{{ date('H:i') }}" required>
                        @error('hora') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Número de adeudos</label>
                        <input type="number" name="veces_adeudo" class="form-control" value="0">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Observaciones de adeudo</label>
                        <textarea name="observaciones_adeudo" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="vigente" class="form-check-input" checked>
                            <label class="form-check-label">Documento vigente</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection