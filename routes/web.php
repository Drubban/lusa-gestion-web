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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('unidades', UnidadController::class);
    Route::resource('operadores', OperadorController::class);
    Route::resource('documentos-mantenimiento', DocumentoMantenimientoController::class);
    Route::resource('documentos-capacitacion', DocumentoCapacitacionController::class);
    Route::resource('movimientos', MovimientoController::class);
    Route::resource('usuarios-app', UsuarioDepartamentoController::class);
    
    // Rutas adicionales
    Route::get('qr/exportar', [QRController::class, 'exportar'])->name('qr.exportar');
    Route::get('qr/generar/{unidad}', [QRController::class, 'generar'])->name('qr.generar');
    Route::get('qr/descargar-pdf', [QRController::class, 'descargarTodos'])->name('qr.descargar-pdf');
    
    Route::get('unidades/regenerar-token/{unidad}', [UnidadController::class, 'regenerarToken'])->name('unidades.regenerar-token');
    
    // Exportación a PDF/Word
    Route::get('documentos-mantenimiento/{id}/pdf', [DocumentoMantenimientoController::class, 'exportarPdf'])->name('documentos-mantenimiento.exportar-pdf');
    Route::get('documentos-mantenimiento/{id}/word', [DocumentoMantenimientoController::class, 'exportarWord'])->name('documentos-mantenimiento.exportar-word');
    Route::get('documentos-capacitacion/{id}/pdf', [DocumentoCapacitacionController::class, 'exportarPdf'])->name('documentos-capacitacion.exportar-pdf');
    
    // Importación
    Route::get('importar', [ImportacionController::class, 'index'])->name('importar.index');
    Route::post('importar/unidades', [ImportacionController::class, 'importarUnidades'])->name('importar.unidades');
    Route::post('importar/operadores', [ImportacionController::class, 'importarOperadores'])->name('importar.operadores');

    //usuarios
    Route::resource('usuarios-app', UsuarioDepartamentoController::class);
    Route::resource('usuarios-app', UsuarioDepartamentoController::class)->except(['show']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/movimientos', [MovimientoController::class, 'store']);
    Route::post('/documentos-mantenimiento', [DocumentoMantenimientoController::class, 'store']);
    Route::post('/documentos-capacitacion', [DocumentoCapacitacionController::class, 'store']);
});


Route::get('documentos-mantenimiento/{id}/pdf', [DocumentoMantenimientoController::class, 'exportarPdf'])->name('documentos-mantenimiento.exportar-pdf');
Route::get('documentos-capacitacion/{id}/pdf', [DocumentoCapacitacionController::class, 'exportarPdf'])->name('documentos-capacitacion.exportar-pdf');