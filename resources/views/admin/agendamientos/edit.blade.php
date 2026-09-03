@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Agendamiento</h1>
        <a href="{{ route('admin.agendamientos.index') }}" class="btn btn-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.agendamientos.update', $agendamiento) }}">
                @csrf @method('PUT')

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidad</label>
                        <select name="unidad_id" class="form-select" disabled>
                            <option value="{{ $agendamiento->unidad_id }}">
                                {{ $agendamiento->unidad->numero_economico }} - {{ $agendamiento->unidad->nombre_unidad ?? 'Sin nombre' }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha Agendada *</label>
                        <input type="date" name="fecha_agendada" 
                               class="form-control @error('fecha_agendada') is-invalid @enderror" 
                               value="{{ old('fecha_agendada', $agendamiento->fecha_agendada->format('Y-m-d')) }}" required>
                        @error('fecha_agendada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Estado *</label>
                        <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                            <option value="pendiente" {{ old('estado', $agendamiento->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="cumplido" {{ old('estado', $agendamiento->estado) == 'cumplido' ? 'selected' : '' }}>Cumplido</option>
                            <option value="no_cumplido" {{ old('estado', $agendamiento->estado) == 'no_cumplido' ? 'selected' : '' }}>No cumplido</option>
                            <option value="reagendado" {{ old('estado', $agendamiento->estado) == 'reagendado' ? 'selected' : '' }}>Reagendado</option>
                        </select>
                        @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha de Cumplimiento</label>
                        <input type="date" name="fecha_cumplimiento" 
                               class="form-control @error('fecha_cumplimiento') is-invalid @enderror" 
                               value="{{ old('fecha_cumplimiento', $agendamiento->fecha_cumplimiento?->format('Y-m-d')) }}" readonly>
                        @error('fecha_cumplimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted">Se llena automáticamente al marcar como cumplido</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Observaciones</label>
                        <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" 
                                  rows="3">{{ old('observaciones', $agendamiento->observaciones) }}</textarea>
                        @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-2"></i>Actualizar Agendamiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection