@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Documento de Mantenimiento</h1>
        <a href="{{ route('admin.documentos-mantenimiento.index') }}" class="btn btn-secondary rounded-pill px-4">Volver</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.documentos-mantenimiento.update', $documento) }}">
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
                        <label class="form-label fw-semibold">Rol (zona)</label>
                        <input type="text" name="rol" class="form-control @error('rol') is-invalid @enderror" value="{{ old('rol', $documento->rol) }}" required>
                        @error('rol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Tecnología reportada (seleccione una o varias)</label>
                        <div class="row">
                            @php $tecSeleccionadas = explode(',', $documento->tecnologia_reportada ?? ''); @endphp
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="barras" {{ in_array('barras', $tecSeleccionadas) ? 'checked' : '' }}> BARRAS</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="gps" {{ in_array('gps', $tecSeleccionadas) ? 'checked' : '' }}> GPS</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="varilla" {{ in_array('varilla', $tecSeleccionadas) ? 'checked' : '' }}> VARILLA</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="telpo" {{ in_array('telpo', $tecSeleccionadas) ? 'checked' : '' }}> TELPO</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="mdvr" {{ in_array('mdvr', $tecSeleccionadas) ? 'checked' : '' }}> MDVR</label></div>
                            <div class="col-md-2"><label><input type="checkbox" name="tecnologia[]" value="camaras" {{ in_array('camaras', $tecSeleccionadas) ? 'checked' : '' }}> CÁMARAS</label></div>
                        </div>
                        @error('tecnologia') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prueba barras de conteo Optocontrol</label>
                        <select name="prueba_barras" class="form-select">
                            <option value="">Seleccione</option>
                            <option value="SI" @selected(old('prueba_barras', $documento->prueba_barras) == 'SI')>SI</option>
                            <option value="NO" @selected(old('prueba_barras', $documento->prueba_barras) == 'NO')>NO</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Comentarios / Observaciones</label>
                        <textarea name="comentarios" class="form-control" rows="3">{{ old('comentarios', $documento->comentarios) }}</textarea>
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

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Número de adeudos</label>
                        <input type="number" name="veces_adeudo" class="form-control" value="{{ old('veces_adeudo', $documento->veces_adeudo) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Observaciones de adeudo</label>
                        <textarea name="observaciones_adeudo" class="form-control" rows="2">{{ old('observaciones_adeudo', $documento->observaciones_adeudo) }}</textarea>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="vigente" class="form-check-input" value="1" @checked(old('vigente', $documento->vigente))>
                            <label class="form-check-label">Documento vigente</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection