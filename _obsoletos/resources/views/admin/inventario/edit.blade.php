@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Registro de Inventario</h1>
        <a href="{{ route('admin.inventario.index') }}" class="btn btn-secondary rounded-pill px-4">
            Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.inventario.update', $inventario->id) }}" 
                  id="inventarioForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Campos comunes -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fecha de Entrega *</label>
                        <input type="date" name="fecha_entrega" 
                               class="form-control @error('fecha_entrega') is-invalid @enderror" 
                               value="{{ old('fecha_entrega', $inventario->fecha_entrega->format('Y-m-d')) }}" required>
                        @error('fecha_entrega')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Departamento *</label>
                        <select name="departamento_id" class="form-select @error('departamento_id') is-invalid @enderror" required>
                            <option value="">Seleccione</option>
                            @foreach($departamentos as $depto)
                                <option value="{{ $depto->id }}" {{ old('departamento_id', $inventario->departamento_id) == $depto->id ? 'selected' : '' }}>
                                    {{ ucfirst($depto->nombre) }}
                                </option>
                            @endforeach
                        </select>
                        @error('departamento_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 🔥 CAMPO ÁREA -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Área</label>
                        <input type="text" name="area" 
                               class="form-control @error('area') is-invalid @enderror" 
                               value="{{ old('area', $inventario->area) }}" placeholder="Ej: Zona Norte, Planta 1, etc.">
                        @error('area')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Especifica el área donde labora</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre de quien recibe *</label>
                        <input type="text" name="nombre_recibe" 
                               class="form-control @error('nombre_recibe') is-invalid @enderror" 
                               value="{{ old('nombre_recibe', $inventario->nombre_recibe) }}" required>
                        @error('nombre_recibe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Clave de Empleado *</label>
                        <input type="text" name="clave_empleado" 
                               class="form-control @error('clave_empleado') is-invalid @enderror" 
                               value="{{ old('clave_empleado', $inventario->clave_empleado) }}" required>
                        @error('clave_empleado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Categoría -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Categoría *</label>
                        <select name="categoria" id="categoria" 
                                class="form-select @error('categoria') is-invalid @enderror" required>
                            <option value="">Seleccione una categoría</option>
                            @foreach($categorias as $key => $nombre)
                                <option value="{{ $key }}" {{ old('categoria', $inventario->categoria) == $key ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Campos para categorías de EQUIPO -->
                    <div id="campos-equipo" class="row g-3" style="display: none;">
                        <div class="col-12">
                            <hr class="border-2 border-primary">
                            <h5 class="text-primary"><i class="fas fa-server me-2"></i>Datos del Equipo</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre del Equipo *</label>
                            <input type="text" name="nombre_equipo" 
                                   class="form-control @error('nombre_equipo') is-invalid @enderror" 
                                   value="{{ old('nombre_equipo', $inventario->nombre_equipo) }}">
                            @error('nombre_equipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Marca *</label>
                            <input type="text" name="marca" 
                                   class="form-control @error('marca') is-invalid @enderror" 
                                   value="{{ old('marca', $inventario->marca) }}">
                            @error('marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Modelo *</label>
                            <input type="text" name="modelo" 
                                   class="form-control @error('modelo') is-invalid @enderror" 
                                   value="{{ old('modelo', $inventario->modelo) }}">
                            @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Número de Serie *</label>
                            <input type="text" name="numero_serie" 
                                   class="form-control @error('numero_serie') is-invalid @enderror" 
                                   value="{{ old('numero_serie', $inventario->numero_serie) }}">
                            @error('numero_serie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Datos Extra</label>
                            <textarea name="datos_extra" class="form-control @error('datos_extra') is-invalid @enderror" 
                                      rows="2">{{ old('datos_extra', $inventario->datos_extra) }}</textarea>
                            @error('datos_extra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Campos para categorías de PRODUCTO -->
                    <div id="campos-producto" class="row g-3" style="display: none;">
                        <div class="col-12">
                            <hr class="border-2 border-success">
                            <h5 class="text-success"><i class="fas fa-box me-2"></i>Datos del Producto</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre del Producto *</label>
                            <input type="text" name="nombre_producto" 
                                   class="form-control @error('nombre_producto') is-invalid @enderror" 
                                   value="{{ old('nombre_producto', $inventario->nombre_producto) }}">
                            @error('nombre_producto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Marca *</label>
                            <input type="text" name="marca_producto" 
                                   class="form-control @error('marca_producto') is-invalid @enderror" 
                                   value="{{ old('marca_producto', $inventario->marca_producto) }}">
                            @error('marca_producto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cantidad *</label>
                            <input type="number" name="cantidad" 
                                   class="form-control @error('cantidad') is-invalid @enderror" 
                                   value="{{ old('cantidad', $inventario->cantidad ?? 1) }}" min="1">
                            @error('cantidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Descripción</label>
                            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                                      rows="2">{{ old('descripcion', $inventario->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- 🔥 CAMPO PARA SUBIR IMAGEN -->
                    <div class="col-12">
                        <hr class="border-2 border-secondary">
                        <h5><i class="fas fa-image me-2"></i>Imagen de la hoja firmada</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                @if($inventario->imagen)
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold">Imagen actual</label>
                                        <div>
                                            <a href="{{ $inventario->imagen_url }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye me-1"></i> Ver imagen actual
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                <label class="form-label fw-semibold">Subir nueva imagen (opcional)</label>
                                <input type="file" name="imagen" 
                                       class="form-control @error('imagen') is-invalid @enderror" 
                                       accept="image/*,application/pdf">
                                @error('imagen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Formatos permitidos: JPG, PNG, GIF, PDF (máx. 5MB)</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">
                            <i class="fas fa-save me-2"></i> Actualizar Registro
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoriaSelect = document.getElementById('categoria');
        const camposEquipo = document.getElementById('campos-equipo');
        const camposProducto = document.getElementById('campos-producto');

        const categoriasEquipo = JSON.parse('{!! json_encode($categoriasEquipo) !!}');
        const categoriasProducto = JSON.parse('{!! json_encode($categoriasProducto) !!}');

        function toggleCampos() {
            const categoria = categoriaSelect.value;
            
            if (categoriasEquipo.includes(categoria)) {
                camposEquipo.style.display = 'flex';
                camposProducto.style.display = 'none';
                camposEquipo.querySelectorAll('input, textarea, select').forEach(el => el.disabled = false);
                camposProducto.querySelectorAll('input, textarea, select').forEach(el => el.disabled = true);
            } else if (categoriasProducto.includes(categoria)) {
                camposEquipo.style.display = 'none';
                camposProducto.style.display = 'flex';
                camposEquipo.querySelectorAll('input, textarea, select').forEach(el => el.disabled = true);
                camposProducto.querySelectorAll('input, textarea, select').forEach(el => el.disabled = false);
            } else {
                camposEquipo.style.display = 'none';
                camposProducto.style.display = 'none';
                camposEquipo.querySelectorAll('input, textarea, select').forEach(el => el.disabled = true);
                camposProducto.querySelectorAll('input, textarea, select').forEach(el => el.disabled = true);
            }
        }

        toggleCampos();
        categoriaSelect.addEventListener('change', toggleCampos);
    });
</script>
@endsection