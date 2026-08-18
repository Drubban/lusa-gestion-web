<?php

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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('unidades', UnidadController::class);
    Route::resource('operadores', OperadorController::class);
    Route::resource('documentos-mantenimiento', DocumentoMantenimientoController::class);
    Route::resource('documentos-capacitacion', DocumentoCapacitacionController::class);
    Route::resource('movimientos', MovimientoController::class);
    Route::resource('usuarios-app', UsuarioDepartamentoController::class);
    Route::resource('inventario', InventarioController::class);
    
    Route::prefix('unidades')->name('unidades.')->group(function () {
        Route::get('regenerar-token/{unidad}', [UnidadController::class, 'regenerarToken'])->name('regenerar-token');
    });
    
    // Rutas para QR
    Route::prefix('qr')->name('qr.')->group(function () {
        Route::get('exportar', [QRController::class, 'exportar'])->name('exportar');
        Route::get('generar/{unidad}', [QRController::class, 'generar'])->name('generar');
        Route::get('descargar-pdf', [QRController::class, 'descargarTodos'])->name('descargar-pdf');
    });
    
    // Rutas de exportación para documentos
    Route::prefix('documentos-mantenimiento')->name('documentos-mantenimiento.')->group(function () {
        Route::get('{id}/pdf', [DocumentoMantenimientoController::class, 'exportarPdf'])->name('exportar-pdf');
        Route::get('{id}/word', [DocumentoMantenimientoController::class, 'exportarWord'])->name('exportar-word');
    });
    
    Route::prefix('documentos-capacitacion')->name('documentos-capacitacion.')->group(function () {
        Route::get('{id}/pdf', [DocumentoCapacitacionController::class, 'exportarPdf'])->name('exportar-pdf');
    });
    
    // Rutas de importación
    Route::prefix('importar')->name('importar.')->group(function () {
        Route::get('/', [ImportacionController::class, 'index'])->name('index');
        Route::post('unidades', [ImportacionController::class, 'importarUnidades'])->name('unidades');
        Route::post('operadores', [ImportacionController::class, 'importarOperadores'])->name('operadores');
    });
});

// Ruta pública para ver PDFs (sin autenticación, si es necesario)
Route::get('documentos-mantenimiento/{id}/pdf', [DocumentoMantenimientoController::class, 'exportarPdf'])->name('documentos-mantenimiento.exportar-pdf');
Route::get('documentos-capacitacion/{id}/pdf', [DocumentoCapacitacionController::class, 'exportarPdf'])->name('documentos-capacitacion.exportar-pdf');
