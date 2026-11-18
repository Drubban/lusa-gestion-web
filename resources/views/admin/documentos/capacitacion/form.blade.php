@extends('layouts.app')
@section('title', $documento->exists ? 'Editar constancia' : 'Nueva constancia')

@section('content')
<x-page-header :title="$documento->exists ? 'Editar ' . $documento->folio : 'Nueva constancia de capacitacion'" :back="route('admin.documentos-capacitacion.index')" back-label="Capacitacion" />
<form method="POST" action="{{ $documento->exists ? route('admin.documentos-capacitacion.update', $documento) : route('admin.documentos-capacitacion.store') }}">
    @csrf @if($documento->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-lg-7"><div class="card"><div class="card-body">
            <x-form.select name="unidad_id" label="Unidad" :options="$unidades" :selected="$documento->unidad?->hashid" required />
            <x-form.select name="operador_id" label="Operador" :options="$operadores" :selected="$documento->operador?->hashid" placeholder="El asignado actualmente a la unidad" />
            <x-form.input name="tipo_capacitacion" label="Tipo de capacitacion" :value="$documento->tipo_capacitacion" maxlength="200" placeholder="Ej. Uso de validador Telpo" />
            <div class="row">
                <div class="col-sm-4"><x-form.input type="date" name="fecha" label="Fecha" :value="$documento->fecha?->format('Y-m-d')" required /></div>
                <div class="col-sm-4"><x-form.input type="time" name="hora" label="Hora" :value="substr((string) $documento->hora, 0, 5)" required /></div>
                <div class="col-sm-4"><x-form.input type="date" name="fecha_vencimiento" label="Vence" :value="$documento->fecha_vencimiento?->format('Y-m-d')" /></div>
            </div>
            <div class="row">
                <div class="col-sm-8"><x-form.input name="instructor" label="Instructor" :value="$documento->instructor" maxlength="200" /></div>
                <div class="col-sm-4"><x-form.input type="number" step="0.5" name="duracion_horas" label="Horas" :value="$documento->duracion_horas" min="0" /></div>
            </div>
            <x-form.textarea name="observaciones" label="Observaciones" :value="$documento->observaciones" />
            <x-form.switch name="vigente" label="Constancia vigente" :checked="$documento->vigente ?? true" wrapper="mb-0" />
        </div></div></div>
        <div class="col-lg-5"><div class="card"><div class="card-body">
            <div class="form-section-title">Firmas</div>
            @foreach (\App\Models\DocumentoCapacitacion::FIRMAS as $tipo => $etiqueta)
                <x-firma-pad :name="'firmas[' . $tipo . ']'" :label="$etiqueta" :actual="$documento->exists ? $documento->firma($tipo)?->data_uri : null" />
            @endforeach
        </div></div></div>
    </div>
    <div class="mt-3"><x-form.actions :cancel="route('admin.documentos-capacitacion.index')" /></div>
</form>
@endsection
