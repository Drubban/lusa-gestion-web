@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Unidad</h1>
        <a href="{{ route('admin.unidades.index') }}" class="btn btn-secondary rounded-pill px-4">
            Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.unidades.update', $unidad->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Numero Economico -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Numero Economico *</label>
                        <input type="text" name="numero_economico" 
                               class="form-control @error('numero_economico') is-invalid @enderror" 
                               value="{{ old('numero_economico', $unidad->numero_economico) }}" required>
                        @error('numero_economico')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nombre Unidad -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre de la Unidad</label>
                        <input type="text" name="nombre_unidad" 
                               class="form-control @error('nombre_unidad') is-invalid @enderror" 
                               value="{{ old('nombre_unidad', $unidad->nombre_unidad) }}">
                        @error('nombre_unidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Zona -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Zona *</label>
                        <select name="zona_id" class="form-select @error('zona_id') is-invalid @enderror" required>
                            <option value="">Seleccione una zona</option>
                            @foreach($zonas as $zona)
                                <option value="{{ $zona->id }}" 
                                    {{ old('zona_id', $unidad->zona_id) == $zona->id ? 'selected' : '' }}>
                                    {{ ucfirst($zona->nombre) }}
                                </option>
                            @endforeach
                        </select>
                        @error('zona_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Estado Activo -->
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" 
                                   class="form-check-input" id="activo" 
                                   value="1" {{ old('activo', $unidad->activo) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activo">
                                <i class="fas fa-check-circle me-1"></i> Unidad Activa
                            </label>
                        </div>
                    </div>

                    <!-- EQUIPOS ASIGNADOS - CHECKBOXES -->
                    <div class="col-12">
                        <hr class="border-2 border-primary">
                        <h5 class="text-primary"><i class="fas fa-microchip me-2"></i>Equipos Asignados</h5>
                        <p class="text-muted small">Marca los equipos que estan asignados a esta unidad</p>
                    </div>

                    <!-- E.T - Equipo Telpo -->
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="equipo_telpo" value="0">
                                    <input type="checkbox" name="equipo_telpo" 
                                           class="form-check-input" id="equipo_telpo" 
                                           value="1" {{ old('equipo_telpo', $unidad->equipo_telpo) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold ms-2" for="equipo_telpo">
                                        <i class="fas fa-mobile-alt text-primary me-2"></i>
                                        E.T (Equipo Telpo)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- E.G - Equipo GPS -->
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="equipo_gps" value="0">
                                    <input type="checkbox" name="equipo_gps" 
                                           class="form-check-input" id="equipo_gps" 
                                           value="1" {{ old('equipo_gps', $unidad->equipo_gps) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold ms-2" for="equipo_gps">
                                        <i class="fas fa-satellite text-success me-2"></i>
                                        E.G (Equipo GPS)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- E.B - Equipo Barras -->
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4">
                            <div class="card-body d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="equipo_barras" value="0">
                                    <input type="checkbox" name="equipo_barras" 
                                           class="form-check-input" id="equipo_barras" 
                                           value="1" {{ old('equipo_barras', $unidad->equipo_barras) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold ms-2" for="equipo_barras">
                                        <i class="fas fa-barcode text-warning me-2"></i>
                                        E.B (Equipo Barras)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Operador -->
                    <div class="col-12 mt-4">
                        <hr class="border-2 border-secondary">
                        <h5><i class="fas fa-user me-2"></i>Asignacion de Operador</h5>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Operador Actual</label>
                        <select name="operador_id" class="form-select">
                            <option value="">-- Sin operador --</option>
                            @foreach($operadores as $operador)
                                <option value="{{ $operador->id }}" 
                                    {{ old('operador_id', $operadorActual?->id) == $operador->id ? 'selected' : '' }}>
                                    {{ $operador->nombre_completo }} ({{ $operador->clave_operador }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Selecciona el operador que estara asignado a esta unidad</small>
                    </div>

                    <div class="col-12">
                        <hr>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-save me-2"></i> Actualizar Unidad
                        </button>
                        <a href="{{ route('admin.unidades.index') }}" class="btn btn-secondary rounded-pill px-4 ms-2">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection