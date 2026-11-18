@extends('layouts.app')
@section('title', $unidad->exists ? 'Editar unidad' : 'Nueva unidad')

@section('content')
<x-page-header :title="$unidad->exists ? 'Editar unidad ' . $unidad->numero_economico : 'Nueva unidad'" :back="route('admin.unidades.index')" back-label="Unidades" />

<div class="row"><div class="col-lg-8 col-xl-6">
<form method="POST" action="{{ $unidad->exists ? route('admin.unidades.update', $unidad) : route('admin.unidades.store') }}" class="card">
    @csrf @if($unidad->exists) @method('PUT') @endif
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5"><x-form.input name="numero_economico" label="Numero economico" :value="$unidad->numero_economico" required maxlength="20" /></div>
            <div class="col-sm-7"><x-form.input name="nombre_unidad" label="Nombre / descripcion" :value="$unidad->nombre_unidad" maxlength="255" /></div>
        </div>
        <x-form.select name="zona_id" label="Zona" :options="$zonas" :selected="$unidad->zona?->hashid" required />
        <x-form.select name="operador_id" label="Operador asignado" :options="$operadores" :selected="$operadorActual" placeholder="Sin operador" hint="Al cambiarlo se cierra la asignacion anterior y queda en el historial." />
        <x-form.switch name="activo" label="Unidad activa" :checked="$unidad->activo" />
        <p class="form-text mb-0">Las tecnologias instaladas (Telpo, GPS, barras, MDVR) se registran en el modulo de Tecnologias.</p>
    </div>
    <div class="card-body border-top"><x-form.actions :cancel="route('admin.unidades.index')" /></div>
</form>
</div></div>
@endsection
