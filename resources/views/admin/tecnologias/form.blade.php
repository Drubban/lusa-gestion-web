@extends('layouts.app')
@section('title', $tecnologia->exists ? 'Editar tecnologia' : 'Registrar tecnologia')

@section('content')
<x-page-header :title="$tecnologia->exists ? 'Editar tecnologia' : 'Registrar tecnologia'" :back="route('admin.tecnologias.index')" back-label="Tecnologias" />
<div class="row"><div class="col-lg-8 col-xl-6">
<form method="POST" action="{{ $tecnologia->exists ? route('admin.tecnologias.update', $tecnologia) : route('admin.tecnologias.store') }}" class="card">
    @csrf @if($tecnologia->exists) @method('PUT') @endif
    <div class="card-body">
        <div class="form-section">
            <x-form.select name="unidad_id" label="Unidad" :options="$unidades" :selected="$tecnologia->unidad?->hashid" required />
            <div class="row">
                <div class="col-sm-6"><x-form.select name="tipo" label="Tipo" :options="$tipos" :selected="$tecnologia->tipo" required data-tipo-select /></div>
                <div class="col-sm-6"><x-form.input name="nombre" label="Alias (opcional)" :value="$tecnologia->nombre" maxlength="100" /></div>
            </div>
            <x-form.switch name="activo" label="Activa" :checked="$tecnologia->activo" wrapper="mb-0" />
        </div>
        @foreach ($camposPorTipo as $tipo => $campos)
            <div class="form-section d-none" data-campos-tipo="{{ $tipo }}">
                <div class="form-section-title">Datos de {{ $tipos[$tipo] }}</div>
                <div class="row">
                    @foreach ($campos as $campo => $etiqueta)
                        <div class="col-sm-6"><x-form.input :name="'detalle[' . $campo . ']'" :label="$etiqueta" :value="$detalle[$campo] ?? null" :type="$campo === 'costo_plan' ? 'number' : 'text'" step="0.01" maxlength="100" /></div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <div class="card-body border-top"><x-form.actions :cancel="route('admin.tecnologias.index')" /></div>
</form>
</div></div>
@endsection
