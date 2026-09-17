<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Lusa') | Lusa Gestión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* ====== LAYOUT BASE ====== */
        body {
            overflow-x: hidden;
            background-color: #f5f6fa;
        }

        /* ====== SIDEBAR ====== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            background-color: #1e293b;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1030;
            transition: transform 0.25s ease;
            padding-bottom: 1rem;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #0f172a;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 3px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        .sidebar .brand {
            padding: 1rem 1.25rem;
            color: #fff;
            font-size: 1.25rem;
            font-weight: 700;
            border-bottom: 1px solid #334155;
            display: flex;
            align-items: center;
            gap: .5rem;
            position: sticky;
            top: 0;
            background: #1e293b;
            z-index: 2;
        }

        .sidebar .brand i {
            color: #38bdf8;
        }

        .sidebar-section {
            padding: .75rem 1.25rem .25rem;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 700;
        }

        .nav .nav-link {
            padding: 9px 15px;
            border-radius: 8px;
            color: #cbd5e1 !important;
            font-size: .9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1px 8px;
            transition: all 0.2s;
        }

        .nav .nav-link:hover {
            background-color: rgba(56, 189, 248, 0.12);
            color: #fff !important;
        }

        .nav .nav-link.active {
            background-color: #0d6efd;
            color: #fff !important;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.4);
        }

        .nav .nav-link i {
            width: 20px;
            text-align: center;
            font-size: .95rem;
            flex-shrink: 0;
        }

        .nav .nav-link .badge {
            margin-left: auto;
            font-size: .65rem;
        }

        /* ====== MAIN ====== */
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            transition: margin-left 0.25s ease;
        }

        .topbar {
            background: #fff;
            padding: .75rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1020;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar .btn-toggle {
            display: none;
            background: transparent;
            border: none;
            font-size: 1.25rem;
            color: #334155;
        }

        .content-area {
            padding: 1.5rem;
        }

        /* ====== PAGINACIÓN ====== */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            flex-wrap: wrap;
            margin: 0;
            padding: 0;
        }
        .pagination .page-item { list-style: none; }
        .pagination .page-link {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 8px;
            color: #0d6efd;
            background-color: #fff;
            border: 1px solid #dee2e6;
            text-decoration: none;
            transition: all 0.2s;
        }
        .pagination .page-link:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        .pagination .active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }
        .pagination .disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }

        /* ====== RESPONSIVE ====== */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .topbar .btn-toggle {
                display: block;
            }
            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, .5);
                z-index: 1029;
                display: none;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ============ SIDEBAR ============ --}}
<nav class="sidebar" id="sidebar">
    <div class="brand">
        <i class="fas fa-bus"></i> Lusa Gestión
    </div>

    <ul class="nav flex-column mt-2">

        {{-- DASHBOARD --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>

        {{-- CATÁLOGOS --}}
        <li class="sidebar-section">Catálogos</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.unidades.*') ? 'active' : '' }}"
               href="{{ route('admin.unidades.index') }}">
                <i class="fas fa-bus"></i> Unidades
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.operadores.*') ? 'active' : '' }}"
               href="{{ route('admin.operadores.index') }}">
                <i class="fas fa-users"></i> Operadores
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.usuarios-app.*') ? 'active' : '' }}"
               href="{{ route('admin.usuarios-app.index') }}">
                <i class="fas fa-mobile-alt"></i> Usuarios App
            </a>
        </li>

        {{-- MANTENIMIENTO --}}
        <li class="sidebar-section">Mantenimiento</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.documentos-mantenimiento.*') ? 'active' : '' }}"
               href="{{ route('admin.documentos-mantenimiento.index') }}">
                <i class="fas fa-clipboard-list"></i> Documentos Mantenimiento
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.mantenimiento.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.mantenimiento.dashboard') }}">
                <i class="fas fa-calendar-check"></i> Tablero Mantenimiento
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.agendamientos.*') ? 'active' : '' }}"
               href="{{ route('admin.agendamientos.index') }}">
                <i class="fas fa-calendar-alt"></i> Agendamientos
            </a>
        </li>

        {{-- REVISIONES OPTO CONTROL --}}
        <li class="sidebar-section">Revisiones Optocontrol</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.revisiones-optocontrol.index') ? 'active' : '' }}"
               href="{{ route('admin.revisiones-optocontrol.index') }}">
                <i class="fas fa-tools"></i> Revisiones
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.revisiones-optocontrol.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.revisiones-optocontrol.dashboard') }}">
                <i class="fas fa-chart-pie"></i> Tablero Revisiones
            </a>
        </li>

        {{-- CAPACITACIÓN --}}
        <li class="sidebar-section">Capacitación</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.documentos-capacitacion.*') ? 'active' : '' }}"
               href="{{ route('admin.documentos-capacitacion.index') }}">
                <i class="fas fa-graduation-cap"></i> Documentos Capacitación
            </a>
        </li>

        {{-- OPERACIONES --}}
        <li class="sidebar-section">Operaciones</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.movimientos.*') ? 'active' : '' }}"
               href="{{ route('admin.movimientos.index') }}">
                <i class="fas fa-exchange-alt"></i> Entradas / Salidas
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.ajustes.*') ? 'active' : '' }}"
               href="{{ route('admin.ajustes.index') }}">
                <i class="fas fa-table"></i> Ajustes
            </a>
        </li>

        {{-- INVENTARIO --}}
        <li class="sidebar-section">Inventario</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.inventario.*') ? 'active' : '' }}"
               href="{{ route('admin.inventario.index') }}">
                <i class="fas fa-boxes"></i> Inventario
            </a>
        </li>

        {{-- TECNOLOGÍA --}}
        <li class="sidebar-section">Tecnología</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.tecnologias.*') && request('tipo') == 'barras' ? 'active' : '' }}"
               href="{{ route('admin.tecnologias.index', ['tipo' => 'barras']) }}">
                <i class="fas fa-barcode"></i> Barras
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.tecnologias.*') && request('tipo') == 'telpo' ? 'active' : '' }}"
               href="{{ route('admin.tecnologias.index', ['tipo' => 'telpo']) }}">
                <i class="fas fa-mobile-alt"></i> Telpo
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.tecnologias.*') && request('tipo') == 'gps' ? 'active' : '' }}"
               href="{{ route('admin.tecnologias.index', ['tipo' => 'gps']) }}">
                <i class="fas fa-satellite"></i> GPS
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.tecnologias.*') && request('tipo') == 'mdvr' ? 'active' : '' }}"
               href="{{ route('admin.tecnologias.index', ['tipo' => 'mdvr']) }}">
                <i class="fas fa-video"></i> MDVR
            </a>
        </li>

        {{-- HERRAMIENTAS --}}
        <li class="sidebar-section">Herramientas</li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.qr.*') ? 'active' : '' }}"
               href="{{ route('admin.qr.exportar') }}">
                <i class="fas fa-qrcode"></i> Exportar QR
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.importar.*') ? 'active' : '' }}"
               href="{{ route('admin.importar.index') }}">
                <i class="fas fa-upload"></i> Importar datos
            </a>
        </li>
    </ul>
</nav>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

{{-- ============ MAIN ============ --}}
<div class="main-wrapper">

    {{-- TOPBAR --}}
    <div class="topbar">
        <button class="btn-toggle" id="btnToggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-muted small">
                <i class="fas fa-user-circle me-1"></i>
                {{ auth()->user()->nombre_usuario ?? 'Invitado' }}
            </span>
        </div>
    </div>

    {{-- CONTENIDO --}}
    <div class="content-area">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

{{-- ============ SCRIPTS ============ --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar   = document.getElementById('sidebar');
        const backdrop  = document.getElementById('sidebarBackdrop');
        const btnToggle = document.getElementById('btnToggleSidebar');

        if (btnToggle) {
            btnToggle.addEventListener('click', function () {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', function () {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            });
        }

        // Auto-cerrar alertas después de 5s
        document.querySelectorAll('.alert-dismissible').forEach(function (el) {
            setTimeout(function () {
                const alert = bootstrap.Alert.getOrCreateInstance(el);
                alert.close();
            }, 5000);
        });
    });
</script>

@stack('scripts')
</body>
</html>