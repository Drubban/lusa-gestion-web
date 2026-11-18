<?php

use App\Exceptions\ReglaNegocioException;
use App\Http\Middleware\AccesoPanel;
use App\Http\Middleware\CabecerasSeguridad;
use App\Http\Middleware\ForzarJson;
use App\Http\Middleware\SoloAdmin;
use App\Http\Middleware\VerificarFirmaApp;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(CabecerasSeguridad::class);
        $middleware->alias([
            'panel' => AccesoPanel::class,
            'admin' => SoloAdmin::class,
            'json' => ForzarJson::class,
            'firma.app' => VerificarFirmaApp::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
        // Confiar en el proxy/balanceador local para detectar HTTPS correctamente
        $middleware->trustProxies(at: env('TRUSTED_PROXIES', '127.0.0.1'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Errores de negocio: mensaje claro al usuario
        $exceptions->render(function (ReglaNegocioException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage(), 'codigo' => 'regla_negocio'], 422);
            }

            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        });

        // La API nunca expone trazas, rutas de archivo ni mensajes internos
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }
            if ($e instanceof AuthenticationException) {
                return response()->json(['message' => 'Sesion expirada o invalida.', 'codigo' => 'no_autenticado'], 401);
            }
            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return response()->json(['message' => 'Recurso no encontrado.'], 404);
            }
            if ($e instanceof HttpExceptionInterface) {
                return response()->json(['message' => $e->getMessage() ?: 'Solicitud no permitida.'], $e->getStatusCode());
            }

            return null;
        });

        $exceptions->dontFlash(['password', 'password_confirmation', 'current_password']);
    })
    ->create();
