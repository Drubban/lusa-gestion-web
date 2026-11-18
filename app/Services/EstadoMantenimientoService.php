<?php

namespace App\Services;

use App\Enums\SemaforoMantenimiento;
use App\Models\AgendamientoMantenimiento;
use App\Models\DocumentoMantenimiento;
use App\Models\Unidad;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Calcula el semaforo de mantenimiento de las unidades con pocas consultas.
 * Reemplaza el ciclo N+1 que hacia una consulta por unidad en el dashboard.
 */
class EstadoMantenimientoService
{
    /**
     * @return Collection<int, array{unidad: Unidad, ultimo: ?string, dias: ?int, semaforo: SemaforoMantenimiento, agendamiento: ?AgendamientoMantenimiento, proximo: ?string}>
     */
    public function tablero(array $filtros = []): Collection
    {
        $unidades = Unidad::with(['zona', 'asignacionVigente.operador', 'tecnologias:id,unidad_id,tipo'])
            ->where('activo', true)
            ->when($filtros['zona_id'] ?? null, fn ($q, $z) => $q->where('zona_id', $z))
            ->buscar($filtros['search'] ?? null)
            ->orderBy('numero_economico')
            ->get();

        $ids = $unidades->pluck('id');

        $ultimos = DocumentoMantenimiento::whereIn('unidad_id', $ids)
            ->select('unidad_id', DB::raw('MAX(fecha) as ultima_fecha'))
            ->groupBy('unidad_id')
            ->pluck('ultima_fecha', 'unidad_id');

        $pendientes = AgendamientoMantenimiento::pendientes()->whereIn('unidad_id', $ids)
            ->orderBy('fecha_agendada')->get()->unique('unidad_id')->keyBy('unidad_id');

        $filas = $unidades->map(function (Unidad $unidad) use ($ultimos, $pendientes) {
            $ultima = $ultimos[$unidad->id] ?? null;
            $dias = $ultima ? (int) \Illuminate\Support\Carbon::parse($ultima)->startOfDay()->diffInDays(today()) : null;

            return [
                'unidad' => $unidad,
                'ultimo' => $ultima,
                'dias' => $dias,
                'semaforo' => SemaforoMantenimiento::desdeDias($dias),
                'agendamiento' => $pendientes->get($unidad->id),
                'proximo' => $ultima ? \Illuminate\Support\Carbon::parse($ultima)->addDays(45)->format('d/m/Y') : null,
            ];
        });

        return $filas
            ->when($filtros['semaforo'] ?? null, fn ($c, $s) => $c->filter(fn ($f) => $f['semaforo']->value === $s))
            ->when(($filtros['agendamiento'] ?? null) === 'con', fn ($c) => $c->filter(fn ($f) => $f['agendamiento'] !== null))
            ->when(($filtros['agendamiento'] ?? null) === 'sin', fn ($c) => $c->filter(fn ($f) => $f['agendamiento'] === null))
            ->values();
    }

    /** Conteo por semaforo para graficas y tarjetas. */
    public function resumen(?Collection $tablero = null): array
    {
        $tablero ??= $this->tablero();
        $conteo = [];
        foreach (SemaforoMantenimiento::cases() as $s) {
            $conteo[$s->value] = 0;
        }
        foreach ($tablero as $fila) {
            $conteo[$fila['semaforo']->value]++;
        }
        $vencidos = $tablero->filter(fn ($f) => $f['agendamiento'] && $f['agendamiento']->fecha_agendada->isBefore(today()))->count();

        return [
            'total' => $tablero->count(),
            'por_semaforo' => $conteo,
            'con_agendamiento' => $tablero->filter(fn ($f) => $f['agendamiento'])->count(),
            'agendamientos_vencidos' => $vencidos,
        ];
    }
}
