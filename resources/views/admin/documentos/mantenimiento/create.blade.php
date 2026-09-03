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
                    <!-- Seleccion de Unidad -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidad *</label>
                        <select name="unidad_id" id="unidad_id" class="form-select @error('unidad_id') is-invalid @enderror" required>
                            <option value="">Seleccione una unidad...</option>
                            @foreach($unidades as $unidad)
                            <option value="{{ $unidad->id }}"
                                data-zona="{{ $unidad->zona->nombre ?? '' }}"
                                {{ (old('unidad_id', $unidadSeleccionada?->id) == $unidad->id) ? 'selected' : '' }}>
                                {{ $unidad->numero_economico }} - {{ $unidad->nombre_unidad ?? 'Sin nombre' }}
                            </option>
                            @endforeach
                        </select>
                        @error('unidad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Zona (Radio Button) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Zona *</label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check">
                                <input type="radio" name="zona" id="zona_reyes" value="reyes" class="form-check-input"
                                    {{ old('zona', $unidadSeleccionada?->zona?->nombre ?? '') == 'reyes' ? 'checked' : '' }}>
                                <label class="form-check-label" for="zona_reyes">Reyes</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="zona" id="zona_apaxco" value="apaxco" class="form-check-input"
                                    {{ old('zona', $unidadSeleccionada?->zona?->nombre ?? '') == 'apaxco' ? 'checked' : '' }}>
                                <label class="form-check-label" for="zona_apaxco">Apaxco</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="zona" id="zona_citrus" value="citrus" class="form-check-input"
                                    {{ old('zona', $unidadSeleccionada?->zona?->nombre ?? '') == 'citrus' ? 'checked' : '' }}>
                                <label class="form-check-label" for="zona_citrus">Citrus</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="zona" id="zona_tranzumpango" value="tranzumpango" class="form-check-input"
                                    {{ old('zona', $unidadSeleccionada?->zona?->nombre ?? '') == 'tranzumpango' ? 'checked' : '' }}>
                                <label class="form-check-label" for="zona_tranzumpango">Tranzumpango</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="zona" id="zona_corredor_bc" value="corredor bc" class="form-check-input"
                                    {{ old('zona', $unidadSeleccionada?->zona?->nombre ?? '') == 'corredor bc' ? 'checked' : '' }}>
                                <label class="form-check-label" for="zona_corredor_bc">Corredor BC</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="zona" id="zona_odz" value="odz" class="form-check-input"
                                    {{ old('zona', $unidadSeleccionada?->zona?->nombre ?? '') == 'odz' ? 'checked' : '' }}>
                                <label class="form-check-label" for="zona_odz">ODZ</label>
                            </div>
                        </div>
                        @error('zona') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Tecnologia reportada -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Tecnologia reportada (seleccione una o varias)</label>
                        <div class="row">
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="barras"> BARRAS</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="gps"> GPS</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="varilla"> VARILLA</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="telpo"> TELPO</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="mdvr"> MDVR</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="camaras"> CAMARAS</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="tubo_corrugado"> TUBO CORRUGADO</label></div>
                            <div class="col-md-3"><label><input type="checkbox" name="tecnologia[]" value="limpieza_camaras"> LIMPIEZA DE CAMARAS</label></div>
                        </div>
                        @error('tecnologia') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Estado de Camaras -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Estado de Camaras</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="camara1" value="1" class="form-check-input" id="camara1">
                                    <label class="form-check-label" for="camara1">Camara 1 funcionando</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="camara2" value="1" class="form-check-input" id="camara2">
                                    <label class="form-check-label" for="camara2">Camara 2 funcionando</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="camara3" value="1" class="form-check-input" id="camara3">
                                    <label class="form-check-label" for="camara3">Camara 3 funcionando</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="camara4" value="1" class="form-check-input" id="camara4">
                                    <label class="form-check-label" for="camara4">Camara 4 funcionando</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prueba barras de conteo Optocontrol (Radio Button) -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Prueba barras de conteo Optocontrol</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input type="radio" name="prueba_barras" id="prueba_si" value="SI" class="form-check-input">
                                <label class="form-check-label" for="prueba_si">SI</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="prueba_barras" id="prueba_no" value="NO" class="form-check-input" checked>
                                <label class="form-check-label" for="prueba_no">NO</label>
                            </div>
                        </div>
                        @error('prueba_barras') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Comentarios / Observaciones -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Comentarios / Observaciones</label>
                        <textarea name="comentarios" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- Fecha y Hora -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha *</label>
                        <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                        @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Hora *</label>
                        <input type="time" name="hora" class="form-control @error('hora') is-invalid @enderror" value="{{ date('H:i') }}" required>
                        @error('hora') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Vigente -->
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unidadSelect = document.getElementById('unidad_id');
        const zonaRadios = document.querySelectorAll('input[name="zona"]');

        // Auto-seleccionar zona al cambiar de unidad
        unidadSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const zona = selectedOption.getAttribute('data-zona');

            if (zona) {
                const zonaLower = zona.toLowerCase();
                zonaRadios.forEach(radio => {
                    radio.checked = (radio.value === zonaLower);
                });
            }
        });

        // Si ya hay una unidad seleccionada al cargar, auto-seleccionar su zona
        if (unidadSelect.value) {
            const selectedOption = unidadSelect.options[unidadSelect.selectedIndex];
            const zona = selectedOption.getAttribute('data-zona');
            if (zona) {
                const zonaLower = zona.toLowerCase();
                zonaRadios.forEach(radio => {
                    radio.checked = (radio.value === zonaLower);
                });
            }
        }
    });
</script>
@endsection