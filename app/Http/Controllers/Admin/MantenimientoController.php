<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoAgendamiento;
use App\Enums\MotivoNoPresentado;
use App\Enums\SemaforoMantenimiento;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgendarMasivoRequest;
use App\Models\AgendamientoMantenimiento;
use App\Models\Unidad;
use App\Models\Zona;
use App\Services\AgendamientoService;
use App\Services\EstadoMantenimientoService;
use App\Support\Catalogos;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Tablero de estado de mantenimiento por unidad (antes MantenimientoDashboardController de 470 lineas).
 */
class MantenimientoController extends Controller
{
    public function __construct(
        private readonly EstadoMantenimientoService $estado,
        private readonly AgendamientoService $agendamientos,
    ) {}

    public function index(Request $request)
    {
        $tablero = $this->estado->tablero([
            'zona_id' => Zona::decodeHashid($request->zona),
            'search' => $request->search,
            'semaforo' => $request->semaforo,
            'agendamiento' => $request->agendamiento,
        ]);

        $hoy = AgendamientoMantenimiento::with('unidad.zona')
            ->where('estado', EstadoAgendamiento::Pendiente)
            ->whereDate('fecha_agendada', '<=', today())
            ->orderBy('fecha_agendada')
            ->get();

        return view('admin.mantenimiento.tablero', [
            'tablero' => $tablero,
            'resumen' => $this->estado->resumen($tablero),
            'semaforos' => SemaforoMantenimiento::cases(),
            'zonas' => Catalogos::zonas(),
            'porAtender' => $hoy,
            'motivos' => MotivoNoPresentado::opciones(),
        ]);
    }

    public function show(Unidad $unidad)
    {
        $unidad->load(['zona', 'asignacionVigente.operador', 'tecnologias', 'agendamientos' => fn ($q) => $q->latest()->limit(20)]);
        $mantenimientos = $unidad->documentosMantenimiento()->with('operador')->latest('fecha')->latest('hora')->get();

        return view('admin.mantenimiento.detalle', compact('unidad', 'mantenimientos'));
    }

    public function agendarMasivo(AgendarMasivoRequest $request)
    {
        $r = $this->agendamientos->agendarMasivo(
            $request->validated('unidades'),
            $request->validated('fecha_agendada'),
            $request->validated('observaciones'),
        );

        $mensaje = "Se agendaron {$r['agendadas']} unidades para el " . Carbon::parse($request->validated('fecha_agendada'))->format('d/m/Y') . '.';
        if ($r['omitidas']) {
            $mensaje .= " {$r['omitidas']} ya tenian un agendamiento vigente y se omitieron.";
        }

        return redirect()->route('admin.mantenimiento.index')->with('success', $mensaje);
    }
}
