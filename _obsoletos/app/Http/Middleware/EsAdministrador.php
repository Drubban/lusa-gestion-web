<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsAdministrador
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user() || !auth()->user()->is_admin) { // asumiendo que el admin web usa tabla 'users' con campo is_admin
        abort(403, 'No autorizado');
    }
        return $next($request);
    }
}
