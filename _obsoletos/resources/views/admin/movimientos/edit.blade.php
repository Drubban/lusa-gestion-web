@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1>Editar Movimiento #{{ $movimiento->id }}</h1>
    <a href="{{ route('admin.movimientos.index') }}" class="btn btn-secondary">Volver</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.movimientos.update', $movimiento) }}">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Unidad *</label>
                    <select name="unidad_id" class="form-select @error('unidad_id') is-invalid @enderror" required>
                        <option value="">Seleccionar</option>
                        @foreach($unidades as $unidad)
                            <option value="{{ $unidad->id }}" {{ old('unidad_id', $movimiento->unidad_id) == $unidad->id ? 'selected' : '' }}>
                                {{ $unidad->numero_economico }} - {{ $unidad->nombre_unidad }}
                            </option>
                        @endforeach
                    </select>
                    @error('unidad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Departamento *</label>
                    <select name="departamento_id" class="form-select @error('departamento_id') is-invalid @enderror" required>
                        <option value="">Seleccionar</option>
                        @foreach($departamentos as $depto)
                            <option value="{{ $depto->id }}" {{ old('departamento_id', $movimiento->departamento_id) == $depto->id ? 'selected' : '' }}>
                                {{ ucfirst($depto->nombre) }}
                            </option>
                        @endforeach
                    </select>
                    @error('departamento_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo *</label>
                    <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                        <option value="entrada" {{ old('tipo', $movimiento->tipo) == 'entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="salida" {{ old('tipo', $movimiento->tipo) == 'salida' ? 'selected' : '' }}>Salida</option>
                    </select>
                    @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha y hora *</label>
                    <input type="datetime-local" name="fecha_hora" class="form-control @error('fecha_hora') is-invalid @enderror"
                           value="{{ old('fecha_hora', \Carbon\Carbon::parse($movimiento->fecha_hora)->format('Y-m-d\TH:i')) }}" required>
                    @error('fecha_hora') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" rows="3" class="form-control @error('observaciones') is-invalid @enderror">{{ old('observaciones', $movimiento->observaciones) }}</textarea>
                @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary">Actualizar movimiento</button>
            <a href="{{ route('admin.movimientos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection