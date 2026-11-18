<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartamentoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }
        
        // Verificar que el usuario tenga departamento asignado
        if (!$user->departamento_id) {
            return response()->json(['error' => 'Usuario sin departamento asignado'], 403);
        }
        
        // Obtener el departamento del usuario
        $departamento = $user->departamento;
        
        if (!$departamento || !$departamento->activo) {
            return response()->json(['error' => 'Departamento no válido o inactivo'], 403);
        }
        
        // Agregar información del departamento al request
        $request->attributes->set('departamento', $departamento);
        $request->attributes->set('departamento_id', $departamento->id);
        
        return $next($request);
    }
}
