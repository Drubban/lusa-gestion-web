<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DispositivoRevision;
use App\Enums\EstadoRevision;
use App\Http\Controllers\Controller;
use App\Http\Requests\RevisionRequest;
use App\Models\RevisionOptocontrol;
use App\Models\Unidad;
use App\Services\BitacoraService;
use App\Support\Catalogos;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RevisionOptocontrolController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $this->filtros($request);
        $revisiones = RevisionOptocontrol::with('unidad')
            ->filtrar($filtros)
            ->latest('fecha_reporte')->latest('id')
            ->paginate(25)->withQueryString();

        $stats = RevisionOptocontrol::selectRaw("
            COUNT(*) as total,
            COUNT(*) FILTER (WHERE status = 'pendiente') as pendientes,
            COUNT(*) FILTER (WHERE status = 'en_proceso') as en_proceso,
            COUNT(*) FILTER (WHERE status = 'resuelto') as resueltos,
            COUNT(*) FILTER (WHERE fecha_reporte >= ?) as ultimos30
        ", [now()->subDays(30)->toDateString()])->first();

        return view('admin.revisiones.index', [
            'revisiones' => $revisiones,
            'stats' => $stats,
            'dispositivos' => DispositivoRevision::opciones(),
            'estados' => EstadoRevision::opciones(),
            'unidades' => Catalogos::unidades(),
        ]);
    }

    public function dashboard(Request $request)
    {
        $filtros = $this->filtros($request);
        $base = fn () => RevisionOptocontrol::filtrar($filtros);

        return view('admin.revisiones.dashboard', [
            'porDispositivo' => $base()->selectRaw('disp, COUNT(*) as total')->groupBy('disp')->pluck('total', 'disp'),
            'porEstado' => $base()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
            'porRuta' => $base()->whereNotNull('ruta')->selectRaw('ruta, COUNT(*) as total')->groupBy('ruta')->orderByDesc('total')->limit(8)->pluck('total', 'ruta'),
            'topUnidades' => $base()->join('unidades', 'unidades.id', '=', 'revisiones_optocontrol.unidad_id')
                ->selectRaw('unidades.numero_economico, COUNT(*) as total')->groupBy('unidades.numero_economico')
                ->orderByDesc('total')->limit(5)->pluck('total', 'numero_economico'),
            'tendencia' => RevisionOptocontrol::where('fecha_reporte', '>=', now()->subDays(30))
                ->selectRaw('fecha_reporte, COUNT(*) as total')->groupBy('fecha_reporte')->orderBy('fecha_reporte')
                ->get()->mapWithKeys(fn ($r) => [Carbon::parse($r->fecha_reporte)->format('d/m') => $r->total]),
            'promedioHoras' => (float) $base()->avg('tiempo_estancia'),
            'recientes' => $base()->with('unidad')->latest()->limit(8)->get(),
            'dispositivos' => DispositivoRevision::opciones(),
            'estados' => EstadoRevision::opciones(),
        ]);
    }

    public function create()
    {
        return view('admin.revisiones.form', $this->datosFormulario(new RevisionOptocontrol([
            'fecha_reporte' => today(), 'hora_entrada' => now()->format('H:i'), 'status' => EstadoRevision::Pendiente,
        ])));
    }

    public function store(RevisionRequest $request)
    {
        $revision = RevisionOptocontrol::create($this->completar($request->validated()) + ['created_by' => $request->user()->id]);
        BitacoraService::registrar('revision.creada', $revision);

        return redirect()->route('admin.revisiones-optocontrol.show', $revision)->with('success', 'Revision registrada.');
    }

    public function show(RevisionOptocontrol $revision)
    {
        return view('admin.revisiones.show', ['revision' => $revision->load(['unidad.zona', 'creador'])]);
    }

    public function edit(RevisionOptocontrol $revision)
    {
        return view('admin.revisiones.form', $this->datosFormulario($revision));
    }

    public function update(RevisionRequest $request, RevisionOptocontrol $revision)
    {
        $revision->update($this->completar($request->validated()));
        BitacoraService::registrar('revision.actualizada', $revision);

        return redirect()->route('admin.revisiones-optocontrol.show', $revision)->with('success', 'Revision actualizada.');
    }

    public function destroy(RevisionOptocontrol $revision)
    {
        BitacoraService::registrar('revision.eliminada', $revision);
        $revision->delete();

        return redirect()->route('admin.revisiones-optocontrol.index')->with('success', 'Revision eliminada.');
    }

    /** Calcula ruta por defecto y tiempo de estancia (antes duplicado en store y update). */
    private function completar(array $datos): array
    {
        if (empty($datos['ruta'])) {
            $datos['ruta'] = Unidad::with('zona')->find($datos['unidad_id'])?->zona?->nombre;
        }
        $datos['tiempo_estancia'] = null;
        if (! empty($datos['hora_salida'])) {
            $entrada = Carbon::createFromFormat('H:i', $datos['hora_entrada']);
            $salida = Carbon::createFromFormat('H:i', $datos['hora_salida']);
            if ($salida->lessThan($entrada)) {
                $salida->addDay();
            }
            $datos['tiempo_estancia'] = round($entrada->diffInMinutes($salida) / 60, 2);
        }

        return $datos;
    }

    private function filtros(Request $request): array
    {
        return $request->only(['disp', 'status', 'desde', 'hasta', 'search']) + ['unidad_id' => Unidad::decodeHashid($request->unidad)];
    }

    private function datosFormulario(RevisionOptocontrol $revision): array
    {
        return [
            'revision' => $revision,
            'unidades' => Catalogos::unidades(),
            'dispositivos' => DispositivoRevision::opciones(),
            'estados' => EstadoRevision::opciones(),
        ];
    }
}
