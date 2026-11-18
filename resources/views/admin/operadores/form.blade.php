@extends('layouts.app')
@section('title', $operador->exists ? 'Editar operador' : 'Nuevo operador')

@section('content')
<x-page-header :title="$operador->exists ? 'Editar operador' : 'Nuevo operador'" :back="route('admin.operadores.index')" back-label="Operadores" />
<div class="row"><div class="col-lg-8 col-xl-6">
<form method="POST" action="{{ $operador->exists ? route('admin.operadores.update', $operador) : route('admin.operadores.store') }}" class="card">
    @csrf @if($operador->exists) @method('PUT') @endif
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4"><x-form.input name="clave_operador" label="Clave" :value="$operador->clave_operador" required maxlength="20" /></div>
            <div class="col-sm-8"><x-form.input name="nombre_completo" label="Nombre completo" :value="$operador->nombre_completo" required maxlength="255" /></div>
        </div>
        <x-form.select name="unidad_id" label="Unidad asignada" :options="$unidades" :selected="$unidadActual" placeholder="Sin unidad" hint="La zona del operador se toma de la unidad asignada." />
        <x-form.switch name="activo" label="Operador activo" :checked="$operador->activo" />
    </div>
    <div class="card-body border-top"><x-form.actions :cancel="route('admin.operadores.index')" /></div>
</form>
</div></div>
@endsection
