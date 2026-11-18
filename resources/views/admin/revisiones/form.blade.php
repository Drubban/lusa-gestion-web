@extends('layouts.app')
@section('title', $revision->exists ? 'Editar revision' : 'Nueva revision')

@section('content')
<x-page-header :title="$revision->exists ? 'Editar revision' : 'Nueva revision optocontrol'" :back="route('admin.revisiones-optocontrol.index')" back-label="Revisiones" />
<div class="row"><div class="col-xl-8">
<form method="POST" action="{{ $revision->exists ? route('admin.revisiones-optocontrol.update', $revision) : route('admin.revisiones-optocontrol.store') }}" class="card">
    @csrf @if($revision->exists) @method('PUT') @endif
    <div class="card-body">
        <div class="form-section">
            <div class="row">
                <div class="col-md-6"><x-form.select name="unidad_id" label="Unidad" :options="$unidades" :selected="$revision->unidad?->hashid" required /></div>
                <div class="col-md-6"><x-form.select name="disp" label="Dispositivo" :options="$dispositivos" :selected="$revision->disp" required /></div>
            </div>
            <div class="row">
                <div class="col-md-4"><x-form.input type="date" name="fecha_reporte" label="Fecha" :value="$revision->fecha_reporte?->format('Y-m-d')" required /></div>
                <div class="col-md-4"><x-form.input type="time" name="hora_entrada" label="Hora de entrada" :value="substr((string) $revision->hora_entrada, 0, 5)" required /></div>
                <div class="col-md-4"><x-form.input type="time" name="hora_salida" label="Hora de salida" :value="substr((string) $revision->hora_salida, 0, 5)" hint="La estancia se calcula sola." /></div>
            </div>
            <div class="row">
                <div class="col-md-6"><x-form.input name="ruta" label="Ruta" :value="$revision->ruta" maxlength="255" hint="Si se deja vacio se usa la zona de la unidad." /></div>
                <div class="col-md-6"><x-form.input name="responsable" label="Responsable" :value="$revision->responsable" maxlength="255" /></div>
            </div>
        </div>
        <x-form.textarea name="problema" label="Problema reportado" :value="$revision->problema" required />
        <x-form.textarea name="solucion" label="Solucion" :value="$revision->solucion" />
        <x-form.select name="status" label="Estado" :options="$estados" :selected="$revision->status" :placeholder="false" required wrapper="mb-0" />
    </div>
    <div class="card-body border-top"><x-form.actions :cancel="route('admin.revisiones-optocontrol.index')" /></div>
</form>
</div></div>
@endsection
