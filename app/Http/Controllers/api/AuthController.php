<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $datos = $request->validate([
            'nombre_usuario' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'max:255'],
            'dispositivo' => ['nullable', 'string', 'max:100'],
        ]);

        $llave = 'login-app:' . Str::lower($datos['nombre_usuario']) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($llave, 5)) {
            throw ValidationException::withMessages([
                'nombre_usuario' => 'Demasiados intentos. Espera ' . RateLimiter::availableIn($llave) . ' segundos.',
            ]);
        }

        $usuario = Usuario::with('departamento')->where('nombre_usuario', $datos['nombre_usuario'])->first();
        $valido = Hash::check($datos['password'], $usuario?->password ?? '$2y$12$' . str_repeat('a', 53));

        if (! $usuario || ! $valido || ! $usuario->activo || $usuario->estaBloqueado()) {
            RateLimiter::hit($llave, 60);
            throw ValidationException::withMessages(['nombre_usuario' => 'Credenciales incorrectas.']);
        }
        if (! $usuario->departamento_id && ! $usuario->accedePanel()) {
            throw ValidationException::withMessages(['nombre_usuario' => 'Tu usuario no tiene departamento asignado.']);
        }

        RateLimiter::clear($llave);

        // Un token por dispositivo: el login en un equipo no cierra la sesion de otro
        $nombreToken = 'app:' . Str::limit($datos['dispositivo'] ?? 'movil', 60, '');
        $usuario->tokens()->where('name', $nombreToken)->delete();
        $expira = now()->addMinutes(config('seguridad.token_app_minutos'));
        $token = $usuario->createToken($nombreToken, $usuario->habilidadesApp(), $expira);

        $usuario->forceFill(['ultimo_acceso_at' => now()])->save();
        BitacoraService::registrar('login.app', $usuario);

        return response()->json([
            'token' => $token->plainTextToken,
            'expira_en' => $expira->toIso8601String(),
            'usuario' => new UsuarioResource($usuario),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Sesion cerrada.']);
    }

    public function perfil(Request $request)
    {
        return new UsuarioResource($request->user()->load('departamento'));
    }
}
