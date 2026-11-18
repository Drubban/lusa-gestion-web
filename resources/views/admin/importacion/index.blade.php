@extends('layouts.app')
@section('title', 'Importar datos')

@section('content')
<x-page-header title="Importar datos" subtitle="Carga masiva desde CSV (separado por coma o punto y coma, UTF-8)" />

@if ($errores = session('errores'))
    @if (count($errores))
    <div class="card mb-3"><div class="card-header">Avisos de la importacion <x-badge tone="warning">{{ count($errores) }}</x-badge></div>
        <div class="card-body small" style="max-height:240px;overflow:auto"><ul class="mb-0 ps-3">@foreach ($errores as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    </div>
    @endif
@endif

<div class="row g-3">
    @foreach ([
        ['unidades', 'Unidades', 'bi-bus-front', 'numero_economico, zona', 'nombre_unidad, activo'],
        ['operadores', 'Operadores', 'bi-person-badge', 'clave_operador, nombre_completo', 'numero_economico (asigna unidad), activo'],
        ['tecnologias', 'Tecnologias', 'bi-cpu', 'numero_economico, tipo', 'nombre, activo y los campos del tipo'],
    ] as [$ruta, $titulo, $icono, $req, $opc])
        <div class="col-lg-4">
            <form method="POST" action="{{ route('admin.importar.' . $ruta) }}" enctype="multipart/form-data" class="card h-100">
                @csrf
                <div class="card-body">
                    <span class="stat-icon tone-primary mb-3"><i class="bi {{ $icono }}"></i></span>
                    <h2 class="h5">{{ $titulo }}</h2>
                    <p class="small text-muted mb-1"><strong>Obligatorias:</strong> {{ $req }}</p>
                    <p class="small text-muted"><strong>Opcionales:</strong> {{ $opc }}</p>
                    <input type="file" name="archivo" accept=".csv,.txt" class="form-control" required>
                    @if ($ruta === 'tecnologias')
                        <a href="{{ route('admin.importar.plantilla.tecnologias') }}" class="small d-inline-block mt-2"><i class="bi bi-download me-1"></i>Descargar plantilla</a>
                    @endif
                </div>
                <div class="card-body border-top"><button class="btn btn-primary w-100"><i class="bi bi-upload me-1"></i>Importar {{ strtolower($titulo) }}</button></div>
            </form>
        </div>
    @endforeach
</div>
@endsection
