<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UsuarioDepartamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        Log::info('Login intentado con: ' . $request->nombre_usuario);

        $usuario = UsuarioDepartamento::where('nombre_usuario', $request->nombre_usuario)
            ->where('activo', true)
            ->first();

        Log::info('Usuario encontrado? ' . ($usuario ? 'Sí' : 'No'));
        if ($usuario) {
            Log::info('Hash almacenado: ' . $usuario->password);
            Log::info('Contraseña recibida (longitud): ' . strlen($request->password));
            $check = Hash::check($request->password, $usuario->password);
            Log::info('Verificación Hash::check: ' . ($check ? 'OK' : 'FALLO'));
        }

        // 👇 CRUCIAL: Validar credenciales ANTES de continuar
        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'nombre_usuario' => ['Credenciales incorrectas.'],
            ]);
        }

        // Revocar tokens previos (opcional, pero recomendado)
        $usuario->tokens()->delete();

        $token = $usuario->createToken('app-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre_usuario' => $usuario->nombre_usuario,
                'departamento' => $usuario->departamento->nombre,
                'puede_generar_documentos' => $usuario->puede_generar_documentos,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true]);
    }

    public function perfil(Request $request)
    {
        return response()->json($request->user()->load('departamento'));
    }
}