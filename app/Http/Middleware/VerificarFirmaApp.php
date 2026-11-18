<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifica que la peticion venga de la app oficial y no haya sido alterada ni repetida.
 *
 * La app envia:
 *   X-App-Timestamp: segundos unix
 *   X-App-Nonce:     valor aleatorio unico por peticion
 *   X-App-Signature: hex(HMAC_SHA256(secreto, METODO\nRUTA\nTIMESTAMP\nNONCE\nSHA256(cuerpo)))
 *
 * Nota: el secreto vive dentro del APK, por lo que esto no sustituye al token
 * de usuario; agrega integridad del mensaje y proteccion contra reenvio (replay).
 */
class VerificarFirmaApp
{
    public function handle(Request $request, Closure $next): Response
    {
        $secreto = config('seguridad.firma_app_secreto');
        if (! $secreto) {
            return $next($request);
        }

        $timestamp = (string) $request->header('X-App-Timestamp', '');
        $nonce = (string) $request->header('X-App-Nonce', '');
        $firma = (string) $request->header('X-App-Signature', '');

        if (! ctype_digit($timestamp) || ! preg_match('/^[A-Za-z0-9\-]{16,64}$/', $nonce) || ! preg_match('/^[a-f0-9]{64}$/', $firma)) {
            return $this->rechazar('Firma de peticion ausente o mal formada.');
        }

        if (abs(time() - (int) $timestamp) > config('seguridad.firma_app_ventana', 300)) {
            return $this->rechazar('La peticion expiro. Verifica la hora del dispositivo.');
        }

        $mensaje = implode("\n", [
            strtoupper($request->method()),
            '/' . ltrim($request->path(), '/'),
            $timestamp,
            $nonce,
            hash('sha256', $request->getContent()),
        ]);

        if (! hash_equals(hash_hmac('sha256', $mensaje, $secreto), $firma)) {
            return $this->rechazar('Firma de peticion invalida.');
        }

        // Cada nonce solo se acepta una vez dentro de la ventana
        if (! Cache::add('nonce_app:' . $nonce, 1, config('seguridad.firma_app_ventana', 300) * 2)) {
            return $this->rechazar('Peticion repetida.');
        }

        return $next($request);
    }

    private function rechazar(string $mensaje): Response
    {
        return response()->json(['message' => $mensaje, 'codigo' => 'firma_invalida'], 401);
    }
}
