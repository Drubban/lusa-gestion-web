<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

/*
| API de la app movil. Todas las rutas:
| - responden JSON,
| - verifican la firma HMAC de la app (si APP_SIGNATURE_SECRET esta definido),
| - tienen limite de peticiones.
| Los ids viajan ofuscados (hashids).
*/

Route::middleware(['json', 'firma.app'])->group(function () {
    Route::get('health', fn () => response()->json(['ok' => true, 'hora' => now()->toIso8601String()]))->middleware('throttle:30,1');
    Route::post('login', [Api\AuthController::class, 'login'])->middleware('throttle:login-app');

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('logout', [Api\AuthController::class, 'logout']);
        Route::get('perfil', [Api\AuthController::class, 'perfil']);
        Route::get('resumen', Api\ResumenController::class);

        Route::middleware('abilities:catalogos:leer')->group(function () {
            Route::get('departamentos', [Api\CatalogoController::class, 'departamentos']);
            Route::get('unidades', [Api\CatalogoController::class, 'unidades']);
            Route::get('operadores', [Api\CatalogoController::class, 'operadores']);
            Route::get('catalogos/documentos', [Api\CatalogoController::class, 'catalogosDocumento']);
            Route::post('unidades/qr', [Api\CatalogoController::class, 'porQr']);
        });

        Route::get('movimientos', [Api\MovimientoController::class, 'index']);
        Route::get('movimientos/dentro', [Api\MovimientoController::class, 'dentro']);
        Route::post('movimientos', [Api\MovimientoController::class, 'store']);
        Route::post('movimientos/sincronizar', [Api\MovimientoController::class, 'sincronizar']);

        Route::post('documentos/mantenimiento', [Api\DocumentoController::class, 'mantenimiento']);
        Route::post('documentos/capacitacion', [Api\DocumentoController::class, 'capacitacion']);

        Route::get('agendamientos', [Api\AgendamientoController::class, 'index']);
        Route::get('agendamientos/motivos', [Api\AgendamientoController::class, 'motivos']);
        Route::post('agendamientos/{agendamiento}/cumplir', [Api\AgendamientoController::class, 'cumplir']);
        Route::post('agendamientos/{agendamiento}/no-presentado', [Api\AgendamientoController::class, 'noPresentado']);

        Route::get('revisiones', [Api\RevisionController::class, 'index']);
        Route::get('revisiones/catalogos', [Api\RevisionController::class, 'catalogos']);
        Route::post('revisiones', [Api\RevisionController::class, 'store']);
        Route::patch('revisiones/{revision}', [Api\RevisionController::class, 'update']);

        Route::get('inventario', [Api\InventarioController::class, 'index']);
    });
});
