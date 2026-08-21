@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Tecnología</h1>
        <a href="{{ route('admin.tecnologias.index') }}" class="btn btn-secondary rounded-pill px-4">
            Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.tecnologias.update', $tecnologia->id) }}" id="tecnologiaForm">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Unidad -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidad *</label>
                        <select name="unidad_id" class="form-select @error('unidad_id') is-invalid @enderror" required>
                            <option value="">Seleccione una unidad</option>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad->id }}" {{ old('unidad_id', $tecnologia->unidad_id) == $unidad->id ? 'selected' : '' }}>
                                    {{ $unidad->numero_economico }} - {{ $unidad->nombre_unidad ?? 'Sin nombre' }}
                                </option>
                            @endforeach
                        </select>
                        @error('unidad_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tipo -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tipo de Tecnología *</label>
                        <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                            <option value="">Seleccione un tipo</option>
                            @foreach($tipos as $key => $nombre)
                                <option value="{{ $key }}" {{ old('tipo', $tecnologia->tipo) == $key ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nombre -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" name="nombre" 
                               class="form-control @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre', $tecnologia->nombre) }}" placeholder="Nombre descriptivo">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Activo -->
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" 
                                   class="form-check-input" id="activo" 
                                   value="1" {{ old('activo', $tecnologia->activo) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activo">
                                <i class="fas fa-check-circle me-1"></i> Activo
                            </label>
                        </div>
                    </div>

                    <!-- Campos dinámicos según tipo -->
                    <div class="col-12">
                        <hr class="border-2 border-secondary">
                        <h5 id="titulo-campos"><i class="fas fa-microchip me-2"></i>Datos Específicos</h5>
                    </div>

                    @php
                        $datos = $tecnologia->datos ?? [];
                    @endphp

                    <!-- BARRAS -->
                    <div id="campos-barras" class="row g-3" style="display: none;">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-barcode me-2"></i>Datos de Barras</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ID Barra</label>
                            <input type="text" name="id_barra" class="form-control" value="{{ old('id_barra', $datos['id_barra'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Barras</label>
                            <input type="text" name="barras" class="form-control" value="{{ old('barras', $datos['barras'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono_barras" class="form-control" value="{{ old('telefono_barras', $datos['telefono'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Plan</label>
                            <input type="text" name="plan_barras" class="form-control" value="{{ old('plan_barras', $datos['plan'] ?? '') }}">
                        </div>
                    </div>

                    <!-- TELPO -->
                    <div id="campos-telpo" class="row g-3" style="display: none;">
                        <div class="col-12">
                            <h6 class="text-success"><i class="fas fa-mobile-alt me-2"></i>Datos de Telpo</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IMEI Antes</label>
                            <input type="text" name="imei_antes" class="form-control" value="{{ old('imei_antes', $datos['imei_antes'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">V. APK</label>
                            <input type="text" name="v_apk" class="form-control" value="{{ old('v_apk', $datos['v_apk'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Telpo</label>
                            <input type="text" name="telpo" class="form-control" value="{{ old('telpo', $datos['telpo'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IMEI Telpo</label>
                            <input type="text" name="imei_telpo" class="form-control" value="{{ old('imei_telpo', $datos['imei_telpo'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono_telpo" class="form-control" value="{{ old('telefono_telpo', $datos['telefono'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Plan</label>
                            <input type="text" name="plan_telpo" class="form-control" value="{{ old('plan_telpo', $datos['plan'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Costo del Plan</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="costo_plan" class="form-control" value="{{ old('costo_plan', $datos['costo_plan'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- GPS -->
                    <div id="campos-gps" class="row g-3" style="display: none;">
                        <div class="col-12">
                            <h6 class="text-warning"><i class="fas fa-satellite me-2"></i>Datos de GPS</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IMEI GPS</label>
                            <input type="text" name="imei_gps" class="form-control" value="{{ old('imei_gps', $datos['imei_gps'] ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono_gps" class="form-control" value="{{ old('telefono_gps', $datos['telefono'] ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Plan</label>
                            <input type="text" name="plan_gps" class="form-control" value="{{ old('plan_gps', $datos['plan'] ?? '') }}">
                        </div>
                    </div>

                    <!-- MDVR -->
                    <div id="campos-mdvr" class="row g-3" style="display: none;">
                        <div class="col-12">
                            <h6 class="text-danger"><i class="fas fa-video me-2"></i>Datos de MDVR</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">DVR</label>
                            <input type="text" name="dvr" class="form-control" value="{{ old('dvr', $datos['dvr'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" name="modelo" class="form-control" value="{{ old('modelo', $datos['modelo'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cámaras</label>
                            <input type="text" name="camaras" class="form-control" value="{{ old('camaras', $datos['camaras'] ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Memoria</label>
                            <input type="text" name="memoria" class="form-control" value="{{ old('memoria', $datos['memoria'] ?? '') }}">
                        </div>
                    </div>

                    <div class="col-12">
                        <hr>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-save me-2"></i> Actualizar Tecnología
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo');
        const camposBarras = document.getElementById('campos-barras');
        const camposTelpo = document.getElementById('campos-telpo');
        const camposGps = document.getElementById('campos-gps');
        const camposMdvr = document.getElementById('campos-mdvr');
        const tituloCampos = document.getElementById('titulo-campos');

        function toggleCampos() {
            const tipo = tipoSelect.value;
            
            camposBarras.style.display = 'none';
            camposTelpo.style.display = 'none';
            camposGps.style.display = 'none';
            camposMdvr.style.display = 'none';

            if (tipo === 'barras') {
                camposBarras.style.display = 'flex';
                tituloCampos.textContent = '📱 Datos de Barras';
            } else if (tipo === 'telpo') {
                camposTelpo.style.display = 'flex';
                tituloCampos.textContent = '📱 Datos de Telpo';
            } else if (tipo === 'gps') {
                camposGps.style.display = 'flex';
                tituloCampos.textContent = '📡 Datos de GPS';
            } else if (tipo === 'mdvr') {
                camposMdvr.style.display = 'flex';
                tituloCampos.textContent = '🎥 Datos de MDVR';
            } else {
                tituloCampos.textContent = 'Seleccione un tipo de tecnología';
            }
        }

        toggleCampos();
        tipoSelect.addEventListener('change', toggleCampos);
    });
</script>
@endsection