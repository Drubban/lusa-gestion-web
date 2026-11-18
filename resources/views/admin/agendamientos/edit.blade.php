@extends('layouts.app')
@section('title', 'Editar agendamiento')

@section('content')
<x-page-header :title="'Agendamiento de ' . $agendamiento->unidad?->numero_economico" :back="route('admin.agendamientos.index')" back-label="Agendamientos" subtitle="Para cambiar el estado usa las acciones de la lista (cumplido, no presentado o reagendar)." />
<div class="row"><div class="col-lg-7 col-xl-5">
<form method="POST" action="{{ route('admin.agendamientos.update', $agendamiento) }}" class="card">
    @csrf @method('PUT')
    <div class="card-body">
        <x-form.input type="date" name="fecha_agendada" label="Fecha" required :min="today()->format('Y-m-d')" :value="$agendamiento->fecha_agendada?->format('Y-m-d')" />
        <x-form.textarea name="observaciones" label="Observaciones" :value="$agendamiento->observaciones" wrapper="mb-0" />
    </div>
    <div class="card-body border-top"><x-form.actions :cancel="route('admin.agendamientos.index')" /></div>
</form>
</div></div>
@endsection
