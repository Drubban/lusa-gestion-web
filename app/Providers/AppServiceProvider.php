<?php

namespace App\Providers;

use App\Models\DocumentoCapacitacion;
use App\Models\DocumentoMantenimiento;
use App\Models\Usuario;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Nombres cortos y estables en columnas polimorficas (firmas y tokens)
        Relation::enforceMorphMap([
            'usuario' => Usuario::class,
            'documento_mantenimiento' => DocumentoMantenimiento::class,
            'documento_capacitacion' => DocumentoCapacitacion::class,
        ]);

        // En desarrollo avisa si se intenta asignar un campo que no esta en $fillable
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        Paginator::useBootstrapFive();

        // Solo se fuerza HTTPS si el servidor realmente lo tiene (APP_URL con https).
        // Con "php artisan serve" o un servidor solo HTTP esto debe quedar desactivado.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('login-app', fn (Request $request) => [
            Limit::perMinute(10)->by($request->ip()),
            Limit::perMinute(5)->by(strtolower((string) $request->input('nombre_usuario')) . '|' . $request->ip()),
        ]);
    }
}
