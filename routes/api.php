<?php

use App\Http\Controllers\Api\DocumentoMantenimientoController;
use App\Http\Controllers\Api\DocumentoCapacitacionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogoController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/login', [AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/perfil', [AuthController::class, 'perfil']);
    Route::get('/operadores', [CatalogoController::class, 'operadores']);
    Route::get('/unidades', [CatalogoController::class, 'unidades']);
    Route::get('/asignaciones', [CatalogoController::class, 'asignaciones']);
     Route::post('/documentos-mantenimiento', [DocumentoMantenimientoController::class, 'store']);
     Route::post('/documentos-mantenimiento', [DocumentoMantenimientoController::class, 'store']);
    Route::post('/documentos-capacitacion', [DocumentoCapacitacionController::class, 'store']);
// });
