@extends('admin.layouts.app')

@section('title', 'Editar Revisión Optocontrol')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3">Editar Revisión Optocontrol #{{ $revision->id }}</h1>
                <a href="{{ route('admin.revisiones-optocontrol.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.revisiones-optocontrol.update', $revision->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="disp" class="form-label">Dispositivo <span class="text-danger">*</span></label>
                                <select name="disp" id="disp" class="form-select @error('disp') is-invalid @enderror" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($disp_opciones as $opcion)
                                        <option value="{{ $opcion }}" {{ old('disp', $revision->disp) == $opcion ? 'selected' : '' }}>
                                            {{ ucfirst($opcion) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('disp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="unidad_id" class="form-label">Unidad <span class="text-danger">*</span></label>
                                <select name="unidad_id" id="unidad_id" class="form-select @error('unidad_id') is-invalid @enderror" required>
                                    <option value="">Seleccione una unidad...</option>
                                    @foreach($unidades as $unidad)
                                        <option value="{{ $unidad->id }}" data-nombre="{{ $unidad->nombre_unidad }}" data-ruta="{{ $unidad->zona->nombre ?? '' }}" {{ old('unidad_id', $revision->unidad_id) == $unidad->id ? 'selected' : '' }}>
                                            {{ $unidad->numero_economico }} - {{ $unidad->nombre_unidad }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unidad_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nombre_unidad" class="form-label">Nombre de Unidad</label>
                                <input type="text" name="nombre_unidad" id="nombre_unidad" class="form-control" value="{{ old('nombre_unidad', $revision->nombre_unidad) }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="ruta" class="form-label">Ruta (Zona)</label>
                                <input type="text" name="ruta" id="ruta" class="form-control @error('ruta') is-invalid @enderror" value="{{ old('ruta', $revision->ruta) }}" readonly>
                                @error('ruta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="fecha_reporte" class="form-label">Fecha Reporte <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_reporte" id="fecha_reporte" class="form-control @error('fecha_reporte') is-invalid @enderror" value="{{ old('fecha_reporte', $revision->fecha_reporte->format('Y-m-d')) }}" required>
                                @error('fecha_reporte')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="hora_entrada" class="form-label">Hora Entrada <span class="text-danger">*</span></label>
                                <input type="time" name="hora_entrada" id="hora_entrada" class="form-control @error('hora_entrada') is-invalid @enderror" value="{{ old('hora_entrada', $revision->hora_entrada ? date('H:i', strtotime($revision->hora_entrada)) : '') }}" required>
                                @error('hora_entrada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="hora_salida" class="form-label">Hora Salida</label>
                                <input type="time" name="hora_salida" id="hora_salida" class="form-control @error('hora_salida') is-invalid @enderror" value="{{ old('hora_salida', $revision->hora_salida ? date('H:i', strtotime($revision->hora_salida)) : '') }}">
                                @error('hora_salida')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="problema" class="form-label">Problema <span class="text-danger">*</span></label>
                                <textarea name="problema" id="problema" rows="3" class="form-control @error('problema') is-invalid @enderror" required>{{ old('problema', $revision->problema) }}</textarea>
                                @error('problema')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="solucion" class="form-label">Solución</label>
                                <textarea name="solucion" id="solucion" rows="3" class="form-control @error('solucion') is-invalid @enderror">{{ old('solucion', $revision->solucion) }}</textarea>
                                @error('solucion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach($status_opciones as $opcion)
                                        <option value="{{ $opcion }}" {{ old('status', $revision->status) == $opcion ? 'selected' : '' }}>
                                            {{ ucfirst($opcion) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tiempo_estancia" class="form-label">Tiempo Estancia (Horas)</label>
                                <input type="number" name="tiempo_estancia" id="tiempo_estancia" class="form-control @error('tiempo_estancia') is-invalid @enderror" value="{{ old('tiempo_estancia', $revision->tiempo_estancia) }}" step="0.01" min="0">
                                @error('tiempo_estancia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" name="responsable" id="responsable" class="form-control @error('responsable') is-invalid @enderror" value="{{ old('responsable', $revision->responsable) }}">
                                @error('responsable')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                            <a href="{{ route('admin.revisiones-optocontrol.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unidadSelect = document.getElementById('unidad_id');
        const nombreInput = document.getElementById('nombre_unidad');
        const rutaInput = document.getElementById('ruta');

        function updateUnidadData() {
            const selectedOption = unidadSelect.options[unidadSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const nombre = selectedOption.dataset.nombre || '';
                const ruta = selectedOption.dataset.ruta || '';
                nombreInput.value = nombre;
                rutaInput.value = ruta;

                fetch(`/admin/revisiones-optocontrol/unidad-data/${selectedOption.value}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.nombre_unidad) {
                            nombreInput.value = data.nombre_unidad;
                        }
                        if (data.ruta) {
                            rutaInput.value = data.ruta;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                nombreInput.value = '';
                rutaInput.value = '';
            }
        }

        unidadSelect.addEventListener('change', updateUnidadData);
    });
</script>
@endpush