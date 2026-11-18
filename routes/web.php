<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

// Autenticacion del panel
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
});
Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Todo el panel requiere sesion y rol admin/supervisor (antes estaba abierto a cualquiera)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'panel'])->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::resource('unidades', Admin\UnidadController::class)->parameters(['unidades' => 'unidad']);
    Route::post('unidades/{unidad}/regenerar-token', [Admin\UnidadController::class, 'regenerarToken'])->name('unidades.regenerar-token');

    Route::resource('operadores', Admin\OperadorController::class)->parameters(['operadores' => 'operador']);
    Route::resource('movimientos', Admin\MovimientoController::class)->parameters(['movimientos' => 'movimiento']);

    Route::resource('documentos-mantenimiento', Admin\DocumentoMantenimientoController::class)->parameters(['documentos-mantenimiento' => 'documento']);
    Route::get('documentos-mantenimiento/{documento}/pdf', [Admin\DocumentoMantenimientoController::class, 'pdf'])->name('documentos-mantenimiento.pdf');
    Route::get('documentos-mantenimiento/{documento}/word', [Admin\DocumentoMantenimientoController::class, 'word'])->name('documentos-mantenimiento.word');

    Route::resource('documentos-capacitacion', Admin\DocumentoCapacitacionController::class)->parameters(['documentos-capacitacion' => 'documento']);
    Route::get('documentos-capacitacion/{documento}/pdf', [Admin\DocumentoCapacitacionController::class, 'pdf'])->name('documentos-capacitacion.pdf');

    Route::resource('inventario', Admin\InventarioController::class)->parameters(['inventario' => 'inventario']);
    Route::resource('ajustes', Admin\AjusteController::class)->parameters(['ajustes' => 'ajuste']);
    Route::resource('tecnologias', Admin\TecnologiaController::class)->parameters(['tecnologias' => 'tecnologia']);

    Route::resource('agendamientos', Admin\AgendamientoController::class)->except('show')->parameters(['agendamientos' => 'agendamiento']);
    Route::post('agendamientos/{agendamiento}/cumplir', [Admin\AgendamientoController::class, 'cumplir'])->name('agendamientos.cumplir');
    Route::post('agendamientos/{agendamiento}/no-presentado', [Admin\AgendamientoController::class, 'noPresentado'])->name('agendamientos.no-presentado');
    Route::post('agendamientos/{agendamiento}/reagendar', [Admin\AgendamientoController::class, 'reagendar'])->name('agendamientos.reagendar');

    Route::prefix('mantenimiento')->name('mantenimiento.')->group(function () {
        Route::get('/', [Admin\MantenimientoController::class, 'index'])->name('index');
        Route::get('unidad/{unidad}', [Admin\MantenimientoController::class, 'show'])->name('detalle');
        Route::post('agendar-masivo', [Admin\MantenimientoController::class, 'agendarMasivo'])->name('agendar-masivo');
    });

    Route::get('revisiones-optocontrol/dashboard', [Admin\RevisionOptocontrolController::class, 'dashboard'])->name('revisiones-optocontrol.dashboard');
    Route::resource('revisiones-optocontrol', Admin\RevisionOptocontrolController::class)->parameters(['revisiones-optocontrol' => 'revision']);

    Route::prefix('qr')->name('qr.')->group(function () {
        Route::get('/', [Admin\QRController::class, 'index'])->name('index');
        Route::get('imprimir', [Admin\QRController::class, 'imprimir'])->name('imprimir');
    });

    Route::get('exportar/agendamientos', [Admin\ExportacionController::class, 'agendamientos'])->name('exportar.agendamientos');

    // Solo administradores
    Route::middleware('admin')->group(function () {
        Route::resource('usuarios-app', Admin\UsuarioController::class)->parameters(['usuarios-app' => 'usuario']);

        Route::prefix('importar')->name('importar.')->group(function () {
            Route::get('/', [Admin\ImportacionController::class, 'index'])->name('index');
            Route::post('unidades', [Admin\ImportacionController::class, 'unidades'])->name('unidades');
            Route::post('operadores', [Admin\ImportacionController::class, 'operadores'])->name('operadores');
            Route::post('tecnologias', [Admin\ImportacionController::class, 'tecnologias'])->name('tecnologias');
            Route::get('plantilla-tecnologias', [Admin\ImportacionController::class, 'plantillaTecnologias'])->name('plantilla.tecnologias');
        });
    });
});
