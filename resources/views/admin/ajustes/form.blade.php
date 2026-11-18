@extends('layouts.app')
@section('title', $ajuste->exists ? 'Editar ajuste' : 'Nuevo ajuste')

@section('content')
<x-page-header :title="$ajuste->exists ? 'Editar ajuste ' . $ajuste->folio : 'Nuevo ajuste'" :back="route('admin.ajustes.index')" back-label="Ajustes" />
<div class="row"><div class="col-lg-8 col-xl-6">
<form method="POST" action="{{ $ajuste->exists ? route('admin.ajustes.update', $ajuste) : route('admin.ajustes.store') }}" class="card">
    @csrf @if($ajuste->exists) @method('PUT') @endif
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6"><x-form.input name="folio" label="Folio" :value="$ajuste->folio" required maxlength="50" /></div>
            <div class="col-sm-6"><x-form.input type="number" step="0.01" min="0" name="monto_total" label="Monto total" :value="$ajuste->monto_total" required /></div>
        </div>
        <div class="row">
            <div class="col-sm-6"><x-form.input type="date" name="fecha" label="Fecha" :value="$ajuste->fecha?->format('Y-m-d')" required /></div>
            <div class="col-sm-6"><x-form.input type="time" name="hora" label="Hora" :value="substr((string) $ajuste->hora, 0, 5)" required /></div>
        </div>
        <x-form.select name="operador_id" label="Operador" :options="$operadores" :selected="$ajuste->operador?->hashid" required hint="La clave se toma del catalogo de operadores." />
        <x-form.select name="unidad_id" label="Unidad" :options="$unidades" :selected="$ajuste->unidad?->hashid" required />
        <x-form.select name="zona_id" label="Zona" :options="$zonas" :selected="$ajuste->zona?->hashid" placeholder="La de la unidad" />
        <x-form.switch name="firmado" label="Ajuste firmado" :checked="$ajuste->firmado" wrapper="mb-0" />
    </div>
    <div class="card-body border-top"><x-form.actions :cancel="route('admin.ajustes.index')" /></div>
</form>
</div></div>
@endsection
