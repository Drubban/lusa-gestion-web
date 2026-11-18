<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartamentoResource;
use App\Http\Resources\MovimientoResource;
use App\Http\Resources\OperadorResource;
use App\Http\Resources\UnidadResource;
use App\Models\Departamento;
use App\Models\DocumentoMantenimiento;
use App\Models\Operador;
use App\Models\Unidad;
use App\Services\MovimientoService;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function departamentos()
    {
        return DepartamentoResource::collection(Departamento::activos()->get());
    }

    public function unidades(Request $request)
    {
        $unidades = Unidad::with(['zona', 'asignacionVigente.operador'])
            ->where('activo', true)
            ->buscar($request->query('q'))
            ->orderBy('numero_economico')
            ->limit(min((int) $request->query('limite', 500), 1000))
            ->get();

        return UnidadResource::collection($unidades);
    }

    public function operadores(Request $request)
    {
        return OperadorResource::collection(Operador::activos()->buscar($request->query('q'))->limit(1000)->get());
    }

    public function catalogosDocumento()
    {
        return response()->json([
            'tecnologias' => DocumentoMantenimiento::TECNOLOGIAS,
            'firmas_mantenimiento' => DocumentoMantenimiento::FIRMAS,
            'firmas_capacitacion' => \App\Models\DocumentoCapacitacion::FIRMAS,
        ]);
    }

    /**
     * Resuelve el QR escaneado y devuelve el estado de la unidad para el departamento.
     */
    public function porQr(Request $request, MovimientoService $movimientos)
    {
        $datos = $request->validate([
            'token' => ['required', 'string', 'max:64'],
            'departamento_id' => ['nullable', 'string', 'max:64'],
        ]);

        $unidad = Unidad::with(['zona', 'asignacionVigente.operador', 'tecnologias', 'agendamientoPendiente'])
            ->where('token_qr', $datos['token'])->first();

        if (! $unidad) {
            return response()->json(['message' => 'El QR no corresponde a ninguna unidad registrada.'], 404);
        }

        $departamento = Departamento::findByHashid($datos['departamento_id'] ?? null) ?? $request->user()->departamento;
        $abiertaAqui = $departamento ? $movimientos->estanciaAbierta($unidad, $departamento) : null;
        $abiertaOtra = $movimientos->estanciaAbierta($unidad);

        return response()->json([
            'unidad' => new UnidadResource($unidad),
            'departamento' => $departamento ? new DepartamentoResource($departamento) : null,
            'tipo_sugerido' => $abiertaAqui ? 'salida' : 'entrada',
            'estancia_abierta' => $abiertaAqui ? new MovimientoResource($abiertaAqui->loadMissing('departamento')) : null,
            'dentro_de' => $abiertaOtra && ! $abiertaOtra->is($abiertaAqui) ? $abiertaOtra->departamento->nombre : null,
            'agendamiento_pendiente' => $unidad->agendamientoPendiente?->fecha_agendada?->toDateString(),
        ]);
    }
}
