<?php

use App\Http\Middleware\DepartamentoMiddleware;
use App\Http\Middleware\EsAdministrador;
use App\Http\Middleware\LogCsrfTokenMismatch;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Configuración CSRF moderna
        $middleware->validateCsrfTokens();

        // Middleware para API (Sanctum)
        $middleware->api([
            EnsureFrontendRequestsAreStateful::class,
        ]);

        // Middleware para web (incluyendo el log)
        $middleware->web([
            LogCsrfTokenMismatch::class,
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            SubstituteBindings::class,
        ]);

        // Aliases
        $middleware->alias([
            'departamento' => DepartamentoMiddleware::class,
            'admin' => EsAdministrador::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejo específico para peticiones API no autenticadas
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autenticado'], 401);
            }
            return redirect()->guest(route('login'));
        });
    })
    ->create();