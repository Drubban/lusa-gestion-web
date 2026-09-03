<?php

use App\Http\Controllers\Admin\AjusteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UnidadController;
use App\Http\Controllers\Admin\OperadorController;
use App\Http\Controllers\Admin\DocumentoMantenimientoController;
use App\Http\Controllers\Admin\DocumentoCapacitacionController;
use App\Http\Controllers\Admin\MovimientoController;
use App\Http\Controllers\Admin\UsuarioDepartamentoController;
use App\Http\Controllers\Admin\QRController;
use App\Http\Controllers\Admin\ImportacionController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\MantenimientoDashboardController;
use App\Http\Controllers\Admin\TecnologiaController;
use App\Http\Controllers\Admin\AgendamientoMantenimientoController;
use App\Http\Controllers\Admin\ExportacionMantenimientoController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Recursos principales
    Route::resource('unidades', UnidadController::class);
    Route::resource('operadores', OperadorController::class);
    Route::resource('documentos-mantenimiento', DocumentoMantenimientoController::class);
    Route::resource('documentos-capacitacion', DocumentoCapacitacionController::class);
    Route::resource('movimientos', MovimientoController::class);
    Route::resource('usuarios-app', UsuarioDepartamentoController::class);
    Route::resource('inventario', InventarioController::class);
    Route::resource('ajustes', AjusteController::class);
    Route::resource('tecnologias', TecnologiaController::class);

    // 🔥 RUTAS DE AGENDAMIENTOS - AGREGAR ESTAS
    Route::resource('agendamientos', AgendamientoMantenimientoController::class);
    Route::get('agendamientos/{id}/marcar-cumplido', [AgendamientoMantenimientoController::class, 'marcarCumplido'])->name('agendamientos.marcar-cumplido');
    Route::post('agendamientos/{id}/reagendar', [AgendamientoMantenimientoController::class, 'reagendar'])->name('agendamientos.reagendar');

    // Rutas para obtener datos via AJAX (ajustes)
    Route::get('ajustes/operadores', [AjusteController::class, 'getOperadores'])->name('ajustes.operadores');
    Route::get('ajustes/unidades', [AjusteController::class, 'getUnidades'])->name('ajustes.unidades');

    // Rutas específicas de unidades
    Route::prefix('unidades')->name('unidades.')->group(function () {
        Route::get('regenerar-token/{unidad}', [UnidadController::class, 'regenerarToken'])->name('regenerar-token');
    });

    // Obtener operador actual de una unidad (AJAX)
    Route::get('unidades/{id}/operador-actual', function ($id) {
        $unidad = \App\Models\Unidad::with('asignacionVigente.operador')->findOrFail($id);
        $operador = $unidad->asignacionVigente->operador ?? null;
        return response()->json(['operador' => $operador]);
    })->name('unidades.operador-actual');

    Route::prefix('mantenimiento')->name('mantenimiento.')->group(function () {
        Route::get('/', [MantenimientoDashboardController::class, 'index'])->name('dashboard');
        Route::get('/unidad/{id}', [MantenimientoDashboardController::class, 'show'])->name('detalle');
        Route::post('/agendar-masivo', [MantenimientoDashboardController::class, 'agendarMasivo'])->name('agendar-masivo');
    });

    // Rutas para QR
    Route::prefix('qr')->name('qr.')->group(function () {
        Route::get('exportar', [QRController::class, 'exportar'])->name('exportar');
        Route::get('generar/{unidad}', [QRController::class, 'generar'])->name('generar');
        Route::get('descargar-pdf', [QRController::class, 'descargarTodos'])->name('descargar-pdf');
    });

    // Rutas de exportación para documentos de mantenimiento
    Route::prefix('documentos-mantenimiento')->name('documentos-mantenimiento.')->group(function () {
        Route::get('{id}/pdf', [DocumentoMantenimientoController::class, 'exportarPdf'])->name('exportar-pdf');
        Route::get('{id}/word', [DocumentoMantenimientoController::class, 'exportarWord'])->name('exportar-word');
    });

    // Rutas de exportación para documentos de capacitación
    Route::prefix('documentos-capacitacion')->name('documentos-capacitacion.')->group(function () {
        Route::get('{id}/pdf', [DocumentoCapacitacionController::class, 'exportarPdf'])->name('exportar-pdf');
    });

    // Rutas de importación
    Route::prefix('importar')->name('importar.')->group(function () {
        Route::get('/', [ImportacionController::class, 'index'])->name('index');
        Route::post('unidades', [ImportacionController::class, 'importarUnidades'])->name('unidades');
        Route::post('operadores', [ImportacionController::class, 'importarOperadores'])->name('operadores');
        Route::post('tecnologias', [ImportacionController::class, 'importarTecnologias'])->name('tecnologias');
        Route::get('plantilla-tecnologias', [ImportacionController::class, 'descargarPlantillaTecnologias'])->name('plantilla.tecnologias');
    });

    Route::prefix('exportar-mantenimientos')->name('exportar.mantenimientos.')->group(function () {
        Route::get('/csv', [ExportacionMantenimientoController::class, 'exportarCSV'])->name('csv');
        Route::get('/excel', [ExportacionMantenimientoController::class, 'exportarExcel'])->name('excel');
        Route::get('/todos', [ExportacionMantenimientoController::class, 'exportarTodos'])->name('todos');
    });

});

// Ruta pública para ver PDFs (sin autenticación, si es necesario)
Route::get('documentos-mantenimiento/{id}/pdf', [DocumentoMantenimientoController::class, 'exportarPdf'])->name('documentos-mantenimiento.exportar-pdf');
Route::get('documentos-capacitacion/{id}/pdf', [DocumentoCapacitacionController::class, 'exportarPdf'])->name('documentos-capacitacion.exportar-pdf');

Route::get('/csrf-token', function () {
    return response()->json([
        'token' => csrf_token(),
        'session_token' => session()->token(),
        'session_has_token' => session()->has('_token'),
    ]);
});