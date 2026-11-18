@extends('layouts.app')
@section('title', $inventario->exists ? 'Editar entrega' : 'Registrar entrega')

@section('content')
<x-page-header :title="$inventario->exists ? 'Editar entrega' : 'Registrar entrega'" :back="route('admin.inventario.index')" back-label="Inventario"
    subtitle="Un solo formulario para equipo y consumibles: los campos de equipo aparecen segun la categoria." />
<div class="row"><div class="col-xl-9">
<form method="POST" enctype="multipart/form-data" action="{{ $inventario->exists ? route('admin.inventario.update', $inventario) : route('admin.inventario.store') }}" class="card">
    @csrf @if($inventario->exists) @method('PUT') @endif
    <div class="card-body">
        <div class="form-section">
            <div class="form-section-title">Articulo</div>
            <div class="row">
                <div class="col-md-4"><x-form.select name="categoria" label="Categoria" :options="$categorias" :selected="$inventario->categoria" required data-categoria-select="{{ json_encode($categoriasEquipo) }}" /></div>
                <div class="col-md-5"><x-form.input name="nombre" label="Nombre del articulo" :value="$inventario->nombre" required maxlength="100" placeholder="Ej. Laptop, toner, tarjeta SIM" /></div>
                <div class="col-md-3"><x-form.input type="number" name="cantidad" label="Cantidad" :value="$inventario->cantidad" min="1" required /></div>
            </div>
            <div class="row">
                <div class="col-md-4"><x-form.input name="marca" label="Marca" :value="$inventario->marca" maxlength="100" /></div>
                <div class="col-md-4" data-solo-equipo><x-form.input name="modelo" label="Modelo" :value="$inventario->modelo" maxlength="100" data-requerido="1" /></div>
                <div class="col-md-4" data-solo-equipo><x-form.input name="numero_serie" label="Numero de serie" :value="$inventario->numero_serie" maxlength="100" data-requerido="1" /></div>
            </div>
            <x-form.textarea name="descripcion" label="Descripcion / datos adicionales" :value="$inventario->descripcion" />
        </div>
        <div class="form-section">
            <div class="form-section-title">Entrega</div>
            <div class="row">
                <div class="col-md-4"><x-form.input type="date" name="fecha_entrega" label="Fecha de entrega" :value="$inventario->fecha_entrega?->format('Y-m-d')" required /></div>
                <div class="col-md-4"><x-form.select name="departamento_id" label="Departamento" :options="$departamentos" :selected="$inventario->departamento?->hashid" required /></div>
                <div class="col-md-4"><x-form.input name="area" label="Area" :value="$inventario->area" maxlength="100" /></div>
            </div>
            <div class="row">
                <div class="col-md-8"><x-form.input name="nombre_recibe" label="Nombre de quien recibe" :value="$inventario->nombre_recibe" required maxlength="100" /></div>
                <div class="col-md-4"><x-form.input name="clave_empleado" label="Clave de empleado" :value="$inventario->clave_empleado" required maxlength="50" /></div>
            </div>
        </div>
        <div class="form-section">
            <div class="form-section-title">Evidencia</div>
            @if($inventario->imagen_url)<p class="small"><a href="{{ $inventario->imagen_url }}" target="_blank" rel="noopener">Ver archivo actual</a></p>@endif
            <x-form.input type="file" name="imagen" label="Foto o PDF de la responsiva" accept=".jpg,.jpeg,.png,.webp,.pdf" hint="Maximo 5 MB." wrapper="mb-0" />
        </div>
    </div>
    <div class="card-body border-top"><x-form.actions :cancel="route('admin.inventario.index')" /></div>
</form>
</div></div>
@endsection
