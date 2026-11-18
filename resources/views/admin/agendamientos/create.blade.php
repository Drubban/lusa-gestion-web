@extends('layouts.app')
@section('title', 'Agendar mantenimiento')

@section('content')
<x-page-header title="Agendar mantenimiento" :back="route('admin.agendamientos.index')" back-label="Agendamientos" subtitle="Para varias unidades a la vez usa el tablero de estado de mantenimiento." />
<div class="row"><div class="col-lg-7 col-xl-5">
<form method="POST" action="{{ route('admin.agendamientos.store') }}" class="card">
    @csrf
    <div class="card-body">
        <x-form.select name="unidad_id" label="Unidad" :options="$unidades" :selected="$unidadSeleccionada" required />
        <x-form.input type="date" name="fecha_agendada" label="Fecha" required :min="today()->format('Y-m-d')" :value="today()->format('Y-m-d')" />
        <x-form.textarea name="observaciones" label="Observaciones" wrapper="mb-0" />
    </div>
    <div class="card-body border-top"><x-form.actions :cancel="route('admin.agendamientos.index')" submit="Agendar" /></div>
</form>
</div></div>
@endsection
