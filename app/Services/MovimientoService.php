<?php

namespace App\Services;

use App\Enums\TipoMovimiento;
use App\Exceptions\ReglaNegocioException;
use App\Models\Departamento;
use App\Models\MovimientoDepartamento;
use App\Models\Unidad;
use App\Models\Usuario;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Registro de entradas y salidas de unidades a los departamentos (estancias).
 *
 * Reglas:
 * - Una ENTRADA abre una estancia; no puede haber dos abiertas en el mismo departamento.
 * - Si "estancia_unica_por_unidad" esta activo, la unidad no puede estar en dos departamentos.
 * - Una SALIDA cierra la estancia abierta y calcula su duracion.
 * - El uuid generado por la app hace la operacion idempotente (reintentos offline).
 */
class MovimientoService
{
    /**
     * @return array{0: MovimientoDepartamento, 1: bool} movimiento y si fue creado ahora
     */
    public function registrar(
        Unidad $unidad,
        Departamento $departamento,
        TipoMovimiento $tipo,
        Usuario $usuario,
        ?CarbonInterface $fechaHora = null,
        ?string $observaciones = null,
        ?string $uuid = null,
    ): array {
        if ($uuid && $existente = MovimientoDepartamento::where('uuid', $uuid)->first()) {
            return [$existente, false];
        }

        if (! $unidad->activo) {
            throw new ReglaNegocioException("La unidad {$unidad->numero_economico} esta inactiva.");
        }
        if (! $departamento->activo) {
            throw new ReglaNegocioException("El departamento {$departamento->nombre} esta inactivo.");
        }

        $fechaHora = $fechaHora ? Carbon::instance($fechaHora) : now();
        if ($fechaHora->isAfter(now()->addMinutes(5))) {
            throw new ReglaNegocioException('La fecha del movimiento no puede estar en el futuro.');
        }

        return DB::transaction(function () use ($unidad, $departamento, $tipo, $usuario, $fechaHora, $observaciones, $uuid) {
            // Bloqueo por unidad para evitar dos registros simultaneos de la misma unidad
            Unidad::whereKey($unidad->id)->lockForUpdate()->first();

            $abierta = $this->estanciaAbierta($unidad, $departamento);

            $datos = [
                'uuid' => $uuid,
                'unidad_id' => $unidad->id,
                'departamento_id' => $departamento->id,
                'usuario_departamento_id' => $usuario->id,
                'tipo' => $tipo->value,
                'fecha_hora' => $fechaHora,
                'observaciones' => $observaciones,
            ];

            if ($tipo === TipoMovimiento::Entrada) {
                if ($abierta) {
                    throw new ReglaNegocioException(
                        "La unidad {$unidad->numero_economico} ya tiene una entrada abierta en {$departamento->nombre} desde {$abierta->fecha_hora->format('d/m/Y H:i')}.",
                    );
                }
                if (config('seguridad.estancia_unica_por_unidad')) {
                    $otra = $this->estanciaAbierta($unidad);
                    if ($otra) {
                        throw new ReglaNegocioException(
                            "La unidad {$unidad->numero_economico} sigue dentro de {$otra->departamento->nombre}. Registre primero su salida.",
                        );
                    }
                }
            } else {
                if (! $abierta) {
                    throw new ReglaNegocioException(
                        "La unidad {$unidad->numero_economico} no tiene una entrada abierta en {$departamento->nombre}.",
                    );
                }
                if ($fechaHora->lessThan($abierta->fecha_hora)) {
                    throw new ReglaNegocioException('La salida no puede ser anterior a la entrada.');
                }
                $datos['entrada_id'] = $abierta->id;
                $datos['duracion_minutos'] = (int) $abierta->fecha_hora->diffInMinutes($fechaHora);
            }

            $movimiento = MovimientoDepartamento::create($datos);
            BitacoraService::registrar('movimiento.' . $tipo->value, $movimiento);

            return [$movimiento, true];
        });
    }

    /**
     * Sugiere el tipo de movimiento: si la unidad esta dentro del departamento, toca salida.
     */
    public function tipoSugerido(Unidad $unidad, Departamento $departamento): TipoMovimiento
    {
        return $this->estanciaAbierta($unidad, $departamento) ? TipoMovimiento::Salida : TipoMovimiento::Entrada;
    }

    public function estanciaAbierta(Unidad $unidad, ?Departamento $departamento = null): ?MovimientoDepartamento
    {
        return MovimientoDepartamento::estanciasAbiertas()
            ->with('departamento')
            ->where('unidad_id', $unidad->id)
            ->when($departamento, fn ($q) => $q->where('departamento_id', $departamento->id))
            ->latest('fecha_hora')
            ->first();
    }
}
