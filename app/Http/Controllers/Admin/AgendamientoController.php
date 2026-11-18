<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EstadoAgendamiento;
use App\Enums\MotivoNoPresentado;
use App\Exceptions\ReglaNegocioException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgendamientoRequest;
use App\Http\Requests\NoPresentadoRequest;
use App\Http\Requests\ReagendarRequest;
use App\Models\AgendamientoMantenimiento;
use App\Models\Unidad;
use App\Services\AgendamientoService;
use App\Support\Catalogos;
use Illuminate\Http\Request;

class AgendamientoController extends Controller
{
    public function __construct(private readonly AgendamientoService $servicio) {}

    public function index(Request $request)
    {
        $filtros = $request->only(['estado', 'motivo', 'desde', 'hasta', 'search']);
        $filtros['unidad_id'] = Unidad::decodeHashid($request->unidad);

        $agendamientos = AgendamientoMantenimiento::with(['unidad.zona', 'creador', 'reportadoPor'])
            ->filtrar($filtros)
            ->orderByRaw("CASE WHEN estado = 'pendiente' THEN 0 ELSE 1 END")
            ->orderBy('fecha_agendada')
            ->paginate(25)->withQueryString();

        return view('admin.agendamientos.index', [
            'agendamientos' => $agendamientos,
            'estados' => EstadoAgendamiento::opciones(),
            'motivos' => MotivoNoPresentado::opciones(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.agendamientos.create', [
            'unidades' => Catalogos::unidades(),
            'unidadSeleccionada' => $request->unidad,
        ]);
    }

    public function store(AgendamientoRequest $request)
    {
        $this->servicio->agendar(
            Unidad::findOrFail($request->validated('unidad_id')),
            $request->validated('fecha_agendada'),
            $request->validated('observaciones'),
        );

        return redirect()->route('admin.agendamientos.index')->with('success', 'Mantenimiento agendado.');
    }

    public function edit(AgendamientoMantenimiento $agendamiento)
    {
        $this->exigirPendiente($agendamiento);

        return view('admin.agendamientos.edit', ['agendamiento' => $agendamiento->load('unidad'), 'motivos' => MotivoNoPresentado::opciones()]);
    }

    /** Solo se edita la fecha / observaciones; los cambios de estado tienen su propia accion. */
    public function update(Request $request, AgendamientoMantenimiento $agendamiento)
    {
        $this->exigirPendiente($agendamiento);
        $datos = $request->validate([
            'fecha_agendada' => ['required', 'date', 'after_or_equal:today'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);
        $agendamiento->update($datos);

        return redirect()->route('admin.agendamientos.index')->with('success', 'Agendamiento actualizado.');
    }

    public function destroy(AgendamientoMantenimiento $agendamiento)
    {
        $agendamiento->delete();

        return redirect()->route('admin.agendamientos.index')->with('success', 'Agendamiento eliminado.');
    }

    public function cumplir(AgendamientoMantenimiento $agendamiento)
    {
        $this->exigirPendiente($agendamiento);
        $this->servicio->marcarCumplido($agendamiento);

        return back()->with('success', 'Agendamiento marcado como cumplido.');
    }

    public function noPresentado(NoPresentadoRequest $request, AgendamientoMantenimiento $agendamiento)
    {
        $this->exigirPendiente($agendamiento);
        $nuevo = $this->servicio->reportarNoPresentado(
            $agendamiento,
            MotivoNoPresentado::from($request->validated('motivo')),
            $request->validated('fecha_reprogramada'),
            $request->validated('observaciones'),
        );

        return back()->with('success', $nuevo ? 'Reportado como no presentado y reprogramado.' : 'Reportado como no presentado.');
    }

    public function reagendar(ReagendarRequest $request, AgendamientoMantenimiento $agendamiento)
    {
        $this->exigirPendiente($agendamiento);
        $this->servicio->reagendar($agendamiento, $request->validated('nueva_fecha'), $request->validated('motivo'));

        return back()->with('success', 'Agendamiento reprogramado.');
    }

    private function exigirPendiente(AgendamientoMantenimiento $agendamiento): void
    {
        if ($agendamiento->estado !== EstadoAgendamiento::Pendiente) {
            throw new ReglaNegocioException('Solo se pueden modificar agendamientos pendientes.');
        }
    }
}
