@extends('layouts.app')
@section('title', $movimiento->exists ? 'Corregir movimiento' : 'Registrar movimiento')

@section('content')
<x-page-header :title="$movimiento->exists ? 'Corregir movimiento' : 'Registrar movimiento'" :back="route('admin.movimientos.index')" back-label="Entradas y salidas"
    :subtitle="$movimiento->exists ? 'Los cambios quedan registrados en la bitacora.' : 'Mismas reglas que la app: no se permite doble entrada ni salida sin entrada.'" />
<div class="row"><div class="col-lg-8 col-xl-6">
<form method="POST" action="{{ $movimiento->exists ? route('admin.movimientos.update', $movimiento) : route('admin.movimientos.store') }}" class="card">
    @csrf @if($movimiento->exists) @method('PUT') @endif
    <div class="card-body">
        <x-form.select name="unidad_id" label="Unidad" :options="$unidades" :selected="$movimiento->unidad?->hashid" required />
        <x-form.select name="departamento_id" label="Departamento" :options="$departamentos" :selected="$movimiento->departamento?->hashid" required />
        <div class="mb-3">
            <label class="form-label">Tipo <span class="text-danger">*</span></label>
            <div class="d-flex gap-2">
                @foreach (\App\Enums\TipoMovimiento::cases() as $t)
                    <label class="check-chip"><input type="radio" name="tipo" value="{{ $t->value }}" @checked(old('tipo', $movimiento->tipo?->value) === $t->value)> {{ $t->etiqueta() }}</label>
                @endforeach
            </div>
        </div>
        <x-form.input name="fecha_hora" type="datetime-local" label="Fecha y hora" :value="optional($movimiento->fecha_hora)->format('Y-m-d\TH:i')" required />
        <x-form.textarea name="observaciones" label="Observaciones" :value="$movimiento->observaciones" />
    </div>
    <div class="card-body border-top"><x-form.actions :cancel="route('admin.movimientos.index')" /></div>
</form>
</div></div>
@endsection
