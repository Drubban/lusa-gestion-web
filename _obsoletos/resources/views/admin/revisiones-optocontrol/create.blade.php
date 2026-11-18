@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">
            <i class="fas fa-plus-circle me-2"></i>Nueva Revision Optocontrol
        </h1>
        <a href="{{ route('admin.revisiones-optocontrol.index') }}" class="btn btn-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.revisiones-optocontrol.store') }}">
                @csrf

                <div class="row g-4">
                    <!-- Dispositivo -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Dispositivo *</label>
                        <select name="disp" id="disp" class="form-select @error('disp') is-invalid @enderror" required>
                            <option value="">Seleccione...</option>
                            @foreach($disp_opciones as $opcion)
                                <option value="{{ $opcion }}" {{ old('disp') == $opcion ? 'selected' : '' }}>
                                    {{ ucfirst($opcion) }}
                                </option>
                            @endforeach
                        </select>
                        @error('disp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Unidad -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidad *</label>
                        <select name="unidad_id" id="unidad_id" class="form-select @error('unidad_id') is-invalid @enderror" required>
                            <option value="">Seleccione una unidad...</option>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id }}"
                                    data-nombre="{{ $unidad->nombre_unidad ?? '' }}"
                                    data-ruta="{{ $unidad->zona->nombre ?? '' }}"
                                    {{ old('unidad_id') == $unidad->id ? 'selected' : '' }}>
                                    {{ $unidad->numero_economico }} - {{ $unidad->nombre_unidad ?? 'Sin nombre' }}
                                </option>
                            @endforeach
                        </select>
                        @error('unidad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Nombre de unidad (solo lectura, autocompletado) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre de Unidad</label>
                        <input type="text" name="nombre_unidad" id="nombre_unidad" class="form-control bg-light"
                               value="{{ old('nombre_unidad') }}" readonly>
                        <small class="text-muted">Se completa automaticamente al seleccionar la unidad</small>
                    </div>

                    <!-- Ruta / Zona (solo lectura, autocompletado) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ruta (Zona)</label>
                        <input type="text" name="ruta" id="ruta" class="form-control bg-light"
                               value="{{ old('ruta') }}" readonly>
                        <small class="text-muted">Se completa automaticamente al seleccionar la unidad</small>
                    </div>

                    <!-- Fecha reporte -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fecha Reporte *</label>
                        <input type="date" name="fecha_reporte" class="form-control @error('fecha_reporte') is-invalid @enderror"
                               value="{{ old('fecha_reporte', date('Y-m-d')) }}" required>
                        @error('fecha_reporte') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Hora entrada -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Hora Entrada *</label>
                        <input type="time" name="hora_entrada" id="hora_entrada" class="form-control @error('hora_entrada') is-invalid @enderror"
                               value="{{ old('hora_entrada', date('H:i')) }}" required>
                        @error('hora_entrada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Hora salida -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Hora Salida</label>
                        <input type="time" name="hora_salida" id="hora_salida" class="form-control @error('hora_salida') is-invalid @enderror"
                               value="{{ old('hora_salida') }}">
                        @error('hora_salida') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Problema -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Problema *</label>
                        <textarea name="problema" class="form-control @error('problema') is-invalid @enderror"
                                  rows="3" required>{{ old('problema') }}</textarea>
                        @error('problema') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Solucion -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Solucion</label>
                        <textarea name="solucion" class="form-control @error('solucion') is-invalid @enderror"
                                  rows="3">{{ old('solucion') }}</textarea>
                        @error('solucion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Estado *</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach($status_opciones as $opcion)
                                <option value="{{ $opcion }}" {{ old('status', 'pendiente') == $opcion ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $opcion)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Tiempo estancia -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tiempo Estancia (Horas)</label>
                        <input type="number" name="tiempo_estancia" id="tiempo_estancia"
                               class="form-control @error('tiempo_estancia') is-invalid @enderror"
                               value="{{ old('tiempo_estancia') }}" step="0.01" min="0">
                        <small class="text-muted">Se calcula automaticamente con ambas horas</small>
                        @error('tiempo_estancia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Responsable -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Responsable</label>
                        <input type="text" name="responsable" class="form-control @error('responsable') is-invalid @enderror"
                               value="{{ old('responsable') }}">
                        @error('responsable') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.revisiones-optocontrol.index') }}" class="btn btn-secondary rounded-pill px-4">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-2"></i>Guardar revision
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unidadSelect = document.getElementById('unidad_id');
        const nombreInput = document.getElementById('nombre_unidad');
        const rutaInput = document.getElementById('ruta');
        const horaEntrada = document.getElementById('hora_entrada');
        const horaSalida = document.getElementById('hora_salida');
        const tiempoInput = document.getElementById('tiempo_estancia');

        function updateUnidadData() {
            const selectedOption = unidadSelect.options[unidadSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                // Cargar desde data attributes (mas rapido)
                const nombre = selectedOption.dataset.nombre || '';
                const ruta = selectedOption.dataset.ruta || '';
                nombreInput.value = nombre;
                rutaInput.value = ruta;

                // Respaldo: consultar por AJAX (por si falta info)
                fetch(`{{ url('admin/revisiones-optocontrol/unidad-data') }}/${selectedOption.value}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.nombre_unidad) nombreInput.value = data.nombre_unidad;
                        if (data.ruta) rutaInput.value = data.ruta;
                    })
                    .catch(err => console.error('Error cargando datos de unidad:', err));
            } else {
                nombreInput.value = '';
                rutaInput.value = '';
            }
        }

        function calcularTiempo() {
            if (horaEntrada.value && horaSalida.value) {
                const [h1, m1] = horaEntrada.value.split(':').map(Number);
                const [h2, m2] = horaSalida.value.split(':').map(Number);
                let minutos = (h2 * 60 + m2) - (h1 * 60 + m1);
                if (minutos < 0) minutos += 24 * 60; // cruza medianoche
                const horas = (minutos / 60).toFixed(2);
                tiempoInput.value = horas;
            }
        }

        unidadSelect.addEventListener('change', updateUnidadData);
        horaEntrada.addEventListener('change', calcularTiempo);
        horaSalida.addEventListener('change', calcularTiempo);

        // Trigger inicial si ya hay valor
        if (unidadSelect.value) updateUnidadData();
    });
</script>
@endpush
@endsection