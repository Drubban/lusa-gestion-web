<?php

namespace App\Services;

use App\Models\Bitacora;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class BitacoraService
{
    public static function registrar(string $accion, ?Model $entidad = null, array $detalle = []): void
    {
        try {
            $request = request();
            Bitacora::create([
                'usuario_id' => $request?->user()?->id,
                'accion' => $accion,
                'entidad' => $entidad?->getTable(),
                'entidad_id' => $entidad?->getKey(),
                'canal' => $request?->is('api/*') ? 'app' : 'web',
                'ip' => $request?->ip(),
                'detalle' => $detalle ?: null,
            ]);
        } catch (\Throwable $e) {
            // La auditoria nunca debe romper la operacion principal
            Log::warning('No se pudo escribir en bitacora', ['accion' => $accion, 'error' => $e->getMessage()]);
        }
    }
}
