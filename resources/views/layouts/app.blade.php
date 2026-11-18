@extends('layouts.base')

@php
    $menu = [
        'Operacion' => [
            ['admin.dashboard', 'admin.dashboard', 'bi-grid-1x2', 'Inicio'],
            ['admin.movimientos.index', 'admin.movimientos.*', 'bi-arrow-left-right', 'Entradas y salidas'],
            ['admin.mantenimiento.index', 'admin.mantenimiento.*', 'bi-speedometer2', 'Estado de mantenimiento'],
            ['admin.agendamientos.index', 'admin.agendamientos.*', 'bi-calendar2-check', 'Agendamientos'],
            ['admin.revisiones-optocontrol.index', 'admin.revisiones-optocontrol.*', 'bi-tools', 'Revisiones optocontrol'],
        ],
        'Documentos' => [
            ['admin.documentos-mantenimiento.index', 'admin.documentos-mantenimiento.*', 'bi-file-earmark-text', 'Mantenimiento'],
            ['admin.documentos-capacitacion.index', 'admin.documentos-capacitacion.*', 'bi-mortarboard', 'Capacitacion'],
            ['admin.ajustes.index', 'admin.ajustes.*', 'bi-receipt', 'Ajustes'],
        ],
        'Catalogos' => [
            ['admin.unidades.index', 'admin.unidades.*', 'bi-bus-front', 'Unidades'],
            ['admin.operadores.index', 'admin.operadores.*', 'bi-person-badge', 'Operadores'],
            ['admin.tecnologias.index', 'admin.tecnologias.*', 'bi-cpu', 'Tecnologias'],
            ['admin.inventario.index', 'admin.inventario.*', 'bi-box-seam', 'Inventario de sistemas'],
            ['admin.qr.index', 'admin.qr.*', 'bi-qr-code', 'Codigos QR'],
        ],
    ];
    if (auth()->user()->esAdmin()) {
        $menu['Administracion'] = [
            ['admin.usuarios-app.index', 'admin.usuarios-app.*', 'bi-people', 'Usuarios'],
            ['admin.importar.index', 'admin.importar.*', 'bi-upload', 'Importar datos'],
        ];
    }
@endphp

@section('body')
<aside class="sidebar" aria-label="Menu principal">
    <a href="{{ route('admin.dashboard') }}" class="brand">
        <span class="brand-mark">LUSA</span>
        <span class="brand-sub">Gestion<br>de sistemas</span>
    </a>
    @foreach ($menu as $seccion => $items)
        <div class="nav-section">{{ $seccion }}</div>
        <ul class="nav-lusa">
            @foreach ($items as [$ruta, $patron, $icono, $texto])
                <li><a href="{{ route($ruta) }}" @class(['activo' => request()->routeIs($patron)])><i class="bi {{ $icono }}"></i>{{ $texto }}</a></li>
            @endforeach
        </ul>
    @endforeach
    <div class="p-3"></div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-menu" data-toggle-sidebar aria-label="Abrir menu"><i class="bi bi-list"></i></button>
            <span class="text-muted small d-none d-md-inline">{{ now()->translatedFormat('l d \\d\\e F, Y') }}</span>
        </div>
        <div class="dropdown">
            <button class="btn btn-ghost d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="avatar">{{ strtoupper(mb_substr(auth()->user()->nombre_visible, 0, 2)) }}</span>
                <span class="text-start d-none d-sm-block lh-sm">
                    <span class="d-block fw-semibold">{{ auth()->user()->nombre_visible }}</span>
                    <span class="d-block small text-muted">{{ auth()->user()->rol->etiqueta() }}</span>
                </span>
                <i class="bi bi-chevron-down small text-muted"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesion</button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <main class="content">
        @if (session('success'))
            <div class="alert alert-success d-flex gap-2 align-items-start"><i class="bi bi-check-circle"></i><div>{{ session('success') }}</div></div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger d-flex gap-2 align-items-start"><i class="bi bi-exclamation-circle"></i><div>{{ session('error') }}</div></div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1"><i class="bi bi-exclamation-circle me-1"></i>Revisa los siguientes puntos</div>
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
@endsection
