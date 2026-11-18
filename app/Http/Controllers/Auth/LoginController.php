<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre_usuario' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $llave = 'login-web:' . Str::lower($datos['nombre_usuario']) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($llave, 5)) {
            throw ValidationException::withMessages([
                'nombre_usuario' => 'Demasiados intentos. Intenta de nuevo en ' . RateLimiter::availableIn($llave) . ' segundos.',
            ]);
        }

        $usuario = Usuario::where('nombre_usuario', $datos['nombre_usuario'])->first();

        // Se valida el hash aunque el usuario no exista para no revelar cuales existen (timing)
        $valido = Hash::check($datos['password'], $usuario?->password ?? '$2y$12$' . str_repeat('a', 53));

        if (! $usuario || ! $valido || ! $usuario->accedePanel() || $usuario->estaBloqueado()) {
            RateLimiter::hit($llave, 60);
            $usuario?->increment('intentos_fallidos');
            if ($usuario && $usuario->intentos_fallidos >= config('seguridad.max_intentos_login')) {
                $usuario->forceFill(['bloqueado_hasta' => now()->addMinutes(config('seguridad.minutos_bloqueo')), 'intentos_fallidos' => 0])->save();
            }
            throw ValidationException::withMessages(['nombre_usuario' => 'Credenciales incorrectas o cuenta sin acceso.']);
        }

        RateLimiter::clear($llave);
        Auth::login($usuario, $request->boolean('recordar'));
        $request->session()->regenerate();
        $usuario->forceFill(['ultimo_acceso_at' => now(), 'intentos_fallidos' => 0, 'bloqueado_hasta' => null])->save();
        BitacoraService::registrar('login.web', $usuario);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
