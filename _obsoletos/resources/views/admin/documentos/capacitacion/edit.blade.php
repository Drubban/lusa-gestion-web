@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Conformidad de Capacitación</h1>
        <a href="{{ route('admin.documentos-capacitacion.index') }}" class="btn btn-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.documentos-capacitacion.update', $documento) }}">
                @csrf @method('PUT')

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Asignación (Operador + Unidad)</label>
                        <select name="asignacion_id" class="form-select @error('asignacion_id') is-invalid @enderror" required>
                            <option value="">Seleccione...</option>
                            @foreach($asignaciones as $asig)
                                <option value="{{ $asig->id }}" @selected(old('asignacion_id', $documento->asignacion_id) == $asig->id)>
                                    {{ $asig->operador->nombre_completo }} - {{ $asig->unidad->numero_economico }}
                                </option>
                            @endforeach
                        </select>
                        @error('asignacion_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha</label>
                        <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $documento->fecha->format('Y-m-d')) }}" required>
                        @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Hora</label>
                        <input type="time" name="hora" class="form-control @error('hora') is-invalid @enderror" value="{{ old('hora', $documento->hora) }}" required>
                        @error('hora') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="vigente" class="form-check-input" value="1" @checked(old('vigente', $documento->vigente))>
                            <label class="form-check-label">Documento vigente</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection