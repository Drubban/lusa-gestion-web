<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogCsrfTokenMismatch
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si es una petición POST, PUT, PATCH, DELETE
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            Log::info('=== VERIFICACIÓN CSRF ===');
            Log::info('Método: ' . $request->method());
            Log::info('URI: ' . $request->fullUrl());
            Log::info('IP: ' . $request->ip());
            
            // Verificar token enviado
            $tokenEnviado = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');
            Log::info('Token enviado: ' . ($tokenEnviado ? substr($tokenEnviado, 0, 20) . '...' : 'NULL'));
            
            // Verificar token de sesión
            $tokenSesion = $request->session()->token();
            Log::info('Token de sesión: ' . ($tokenSesion ? substr($tokenSesion, 0, 20) . '...' : 'NULL'));
            
            // Verificar si coinciden
            if ($tokenEnviado && $tokenSesion && $tokenEnviado !== $tokenSesion) {
                Log::warning('⚠️ TOKEN MISMATCH: El token enviado no coincide con el de sesión');
                Log::warning('Token enviado: ' . $tokenEnviado);
                Log::warning('Token sesión: ' . $tokenSesion);
            }
        }
        
        return $next($request);
    }
}