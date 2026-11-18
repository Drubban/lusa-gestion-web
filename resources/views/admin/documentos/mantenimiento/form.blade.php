@extends('layouts.app')
@section('title', $documento->exists ? 'Editar documento' : 'Nuevo documento de mantenimiento')

@php
    $tecSel = old('tecnologias', $documento->tecnologias);
    $camSel = old('camaras', collect(range(1, 4))->filter(fn ($n) => str_contains((string) $documento->estado_camaras, "Camara {$n} OK"))->values()->all());
@endphp

@section('content')
<x-page-header :title="$documento->exists ? 'Editar ' . $documento->folio : 'Nuevo documento de mantenimiento'" :back="route('admin.documentos-mantenimiento.index')" back-label="Documentos" />

<form method="POST" action="{{ $documento->exists ? route('admin.documentos-mantenimiento.update', $documento) : route('admin.documentos-mantenimiento.store') }}">
    @csrf @if($documento->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card"><div class="card-body">
                <div class="form-section">
                    <div class="form-section-title">Unidad</div>
                    <x-form.select name="unidad_id" label="Unidad" :options="$unidades" :selected="$documento->unidad?->hashid" required hint="La zona se toma automaticamente de la unidad." />
                    <x-form.select name="operador_id" label="Operador" :options="$operadores" :selected="$documento->operador?->hashid" placeholder="El asignado actualmente a la unidad" />
                    <div class="row">
                        <div class="col-sm-6"><x-form.input type="date" name="fecha" label="Fecha" :value="$documento->fecha?->format('Y-m-d')" required /></div>
                        <div class="col-sm-6"><x-form.input type="time" name="hora" label="Hora" :value="substr((string) $documento->hora, 0, 5)" required /></div>
                    </div>
                </div>
                <div class="form-section">
                    <div class="form-section-title">Revision</div>
                    <label class="form-label">Tecnologia reportada <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach ($tecnologias as $valor => $texto)
                            <label class="check-chip"><input type="checkbox" name="tecnologias[]" value="{{ $valor }}" @checked(in_array($valor, $tecSel ?? []))> {{ $texto }}</label>
                        @endforeach
                    </div>
                    <label class="form-label">Camaras funcionando</label>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach (range(1, 4) as $n)
                            <label class="check-chip"><input type="checkbox" name="camaras[]" value="{{ $n }}" @checked(in_array($n, array_map('intval', $camSel ?? [])))> Camara {{ $n }}</label>
                        @endforeach
                    </div>
                    <x-form.select name="prueba_barras" label="Prueba de barras" :options="['SI' => 'Si', 'NO' => 'No']" :selected="$documento->prueba_barras" placeholder="No aplica" />
                    <x-form.textarea name="comentarios" label="Comentarios" :value="$documento->comentarios" />
                </div>
                <div class="form-section">
                    <div class="form-section-title">Adeudos</div>
                    <div class="row">
                        <div class="col-sm-4"><x-form.input type="number" name="veces_adeudo" label="Veces" :value="$documento->veces_adeudo ?? 0" min="0" max="99" /></div>
                        <div class="col-sm-8"><x-form.input name="observaciones_adeudo" label="Observaciones" :value="$documento->observaciones_adeudo" maxlength="500" /></div>
                    </div>
                    <x-form.switch name="vigente" label="Documento vigente" :checked="$documento->vigente ?? true" wrapper="mb-0" />
                </div>
            </div></div>
        </div>
        <div class="col-lg-5">
            <div class="card"><div class="card-body">
                <div class="form-section-title">Firmas</div>
                @foreach (\App\Models\DocumentoMantenimiento::FIRMAS as $tipo => $etiqueta)
                    <x-firma-pad :name="'firmas[' . $tipo . ']'" :label="$etiqueta" :actual="$documento->exists ? $documento->firma($tipo)?->data_uri : null" />
                @endforeach
            </div></div>
        </div>
    </div>
    <div class="mt-3"><x-form.actions :cancel="route('admin.documentos-mantenimiento.index')" /></div>
</form>
@endsection
