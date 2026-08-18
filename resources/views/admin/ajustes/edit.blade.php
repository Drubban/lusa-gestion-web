@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Ajuste</h1>
        <a href="{{ route('admin.ajustes.index') }}" class="btn btn-secondary rounded-pill px-4">
            Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.ajustes.update', $ajuste->id) }}" id="ajusteForm">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Folio -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Folio *</label>
                        <input type="text" name="folio" 
                               class="form-control @error('folio') is-invalid @enderror" 
                               value="{{ old('folio', $ajuste->folio) }}" placeholder="Ej: AJ-2024-001" required>
                        @error('folio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fecha -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha *</label>
                        <input type="date" name="fecha" 
                               class="form-control @error('fecha') is-invalid @enderror" 
                               value="{{ old('fecha', $ajuste->fecha->format('Y-m-d')) }}" required>
                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hora -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Hora *</label>
                        <input type="time" name="hora" 
                               class="form-control @error('hora') is-invalid @enderror" 
                               value="{{ old('hora', $ajuste->hora) }}" required>
                        @error('hora')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Zona -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Zona</label>
                        <input type="text" name="zona" 
                               class="form-control @error('zona') is-invalid @enderror" 
                               value="{{ old('zona', $ajuste->zona) }}" placeholder="Ej: Zona Norte">
                        @error('zona')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Monto Total -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Monto Total *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="monto_total" 
                                   class="form-control @error('monto_total') is-invalid @enderror" 
                                   value="{{ old('monto_total', $ajuste->monto_total) }}" required>
                        </div>
                        @error('monto_total')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Firmado -->
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="firmado" 
                                   class="form-check-input" id="firmado" 
                                   value="1" {{ old('firmado', $ajuste->firmado) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="firmado">
                                <i class="fas fa-signature me-1"></i> Firmado
                            </label>
                        </div>
                    </div>

                    <!-- Separador -->
                    <div class="col-12">
                        <hr class="border-2 border-secondary">
                        <h5><i class="fas fa-user me-2"></i>Datos del Operador</h5>
                    </div>

                    <!-- Operador -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Operador *</label>
                        <select name="operador_id" id="operador_id" 
                                class="form-select @error('operador_id') is-invalid @enderror" required>
                            <option value="">Seleccione un operador</option>
                            @foreach($operadores as $operador)
                                <option value="{{ $operador->id }}" 
                                        data-clave="{{ $operador->clave_operador }}"
                                        {{ old('operador_id', $ajuste->operador_id) == $operador->id ? 'selected' : '' }}>
                                    {{ $operador->nombre_completo }} ({{ $operador->clave_operador }})
                                </option>
                            @endforeach
                        </select>
                        @error('operador_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Clave Operador -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Clave del Operador *</label>
                        <input type="text" name="clave_operador" id="clave_operador"
                               class="form-control @error('clave_operador') is-invalid @enderror" 
                               value="{{ old('clave_operador', $ajuste->clave_operador) }}" required>
                        @error('clave_operador')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Se autocompleta al seleccionar el operador</small>
                    </div>

                    <!-- Separador -->
                    <div class="col-12">
                        <hr class="border-2 border-secondary">
                        <h5><i class="fas fa-truck me-2"></i>Datos de la Unidad</h5>
                    </div>

                    <!-- Unidad -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidad *</label>
                        <select name="unidad_id" id="unidad_id" 
                                class="form-select @error('unidad_id') is-invalid @enderror" required>
                            <option value="">Seleccione una unidad</option>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id }}" 
                                    {{ old('unidad_id', $ajuste->unidad_id) == $unidad->id ? 'selected' : '' }}>
                                    {{ $unidad->numero_economico }} - {{ $unidad->nombre_unidad ?? 'Sin nombre' }}
                                </option>
                            @endforeach
                        </select>
                        @error('unidad_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <hr>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-save me-2"></i> Actualizar Ajuste
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SELECT2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#operador_id').select2({
            placeholder: 'Buscar operador por nombre o clave...',
            allowClear: true,
            theme: 'bootstrap-5',
            width: '100%'
        });

        $('#unidad_id').select2({
            placeholder: 'Buscar unidad...',
            allowClear: true,
            theme: 'bootstrap-5',
            width: '100%'
        });

        function actualizarClave() {
            var select = document.getElementById('operador_id');
            var selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                var clave = selectedOption.getAttribute('data-clave') || '';
                document.getElementById('clave_operador').value = clave;
                console.log('✅ Clave actualizada a: ' + clave);
            } else {
                document.getElementById('clave_operador').value = '';
                console.log('⚠️ No hay operador seleccionado');
            }
        }

        $('#operador_id').on('change', function() {
            actualizarClave();
        });

        $('#operador_id').on('select2:select', function(e) {
            var selectedElement = e.params.data.element;
            if (selectedElement) {
                var clave = selectedElement.getAttribute('data-clave') || '';
                document.getElementById('clave_operador').value = clave;
                console.log('✅ Clave actualizada a: ' + clave);
            }
        });

        $('#operador_id').on('select2:clear', function() {
            document.getElementById('clave_operador').value = '';
        });

        if ($('#operador_id').val()) {
            actualizarClave();
        }
    });
</script>

<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        border-radius: 0.375rem;
        border-color: #ced4da;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-top: 6px;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #ced4da;
    }
</style>
@endsection