@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Nuevo Agendamiento de Mantenimiento</h1>
        <a href="{{ route('admin.agendamientos.index') }}" class="btn btn-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.agendamientos.store') }}">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidad *</label>
                        <select name="unidad_id" class="form-select @error('unidad_id') is-invalid @enderror" required>
                            <option value="">Seleccione una unidad...</option>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id }}" 
                                    {{ old('unidad_id', $unidadSeleccionada?->id) == $unidad->id ? 'selected' : '' }}>
                                    {{ $unidad->numero_economico }} - {{ $unidad->nombre_unidad ?? 'Sin nombre' }}
                                </option>
                            @endforeach
                        </select>
                        @error('unidad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha Agendada *</label>
                        <input type="date" name="fecha_agendada" 
                               class="form-control @error('fecha_agendada') is-invalid @enderror" 
                               value="{{ old('fecha_agendada', date('Y-m-d', strtotime('+7 days'))) }}" required>
                        @error('fecha_agendada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted">Se recomienda agendar con al menos 7 días de anticipación</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Observaciones</label>
                        <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" 
                                  rows="3">{{ old('observaciones') }}</textarea>
                        @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-2"></i>Guardar Agendamiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection