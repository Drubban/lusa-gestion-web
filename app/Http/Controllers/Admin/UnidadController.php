<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnidadRequest;
use App\Models\Operador;
use App\Models\Unidad;
use App\Models\Zona;
use App\Services\AsignacionService;
use App\Services\BitacoraService;
use App\Support\Catalogos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnidadController extends Controller
{
    public function __construct(private readonly AsignacionService $asignaciones) {}

    public function index(Request $request)
    {
        [$sort, $dir] = $this->orden($request->sort, $request->direction, ['numero_economico', 'nombre_unidad', 'created_at'], 'numero_economico');

        $unidades = Unidad::with(['zona', 'asignacionVigente.operador'])
            ->buscar($request->search)
            ->when($request->filled('estado'), fn ($q) => $q->where('activo', $request->estado === 'activo'))
            ->when($request->zona, fn ($q, $z) => $q->where('zona_id', Zona::decodeHashid($z)))
            ->orderBy($sort, $dir)
            ->paginate(20)
            ->withQueryString();

        return view('admin.unidades.index', ['unidades' => $unidades, 'zonas' => Catalogos::zonas()]);
    }

    public function create()
    {
        return view('admin.unidades.form', $this->datosFormulario(new Unidad(['activo' => true])));
    }

    public function store(UnidadRequest $request)
    {
        $unidad = DB::transaction(function () use ($request) {
            $unidad = Unidad::create($request->safe()->except('operador_id'));
            $this->asignaciones->asignar($unidad, Operador::find($request->validated('operador_id')));

            return $unidad;
        });
        BitacoraService::registrar('unidad.creada', $unidad);

        return redirect()->route('admin.unidades.show', $unidad)->with('success', 'Unidad creada correctamente.');
    }

    public function show(Unidad $unidad)
    {
        $unidad->load(['zona', 'asignaciones' => fn ($q) => $q->with('operador')->latest('fecha_inicio'), 'tecnologias', 'agendamientoPendiente']);
        $movimientos = $unidad->movimientos()->with(['departamento', 'usuario'])->latest('fecha_hora')->limit(10)->get();
        $mantenimientos = $unidad->documentosMantenimiento()->latest('fecha')->limit(5)->get();

        return view('admin.unidades.show', compact('unidad', 'movimientos', 'mantenimientos'));
    }

    public function edit(Unidad $unidad)
    {
        return view('admin.unidades.form', $this->datosFormulario($unidad));
    }

    public function update(UnidadRequest $request, Unidad $unidad)
    {
        DB::transaction(function () use ($request, $unidad) {
            $unidad->update($request->safe()->except('operador_id'));
            $this->asignaciones->asignar($unidad, Operador::find($request->validated('operador_id')));
        });
        BitacoraService::registrar('unidad.actualizada', $unidad);

        return redirect()->route('admin.unidades.show', $unidad)->with('success', 'Unidad actualizada correctamente.');
    }

    /**
     * Si la unidad tiene historial se desactiva en lugar de borrarse
     * (antes se borraba el historial de asignaciones completo).
     */
    public function destroy(Unidad $unidad)
    {
        $tieneHistorial = $unidad->movimientos()->exists() || $unidad->documentosMantenimiento()->exists() || $unidad->documentosCapacitacion()->exists();

        if ($tieneHistorial) {
            $unidad->update(['activo' => false]);
            $this->asignaciones->liberarUnidad($unidad);
            BitacoraService::registrar('unidad.desactivada', $unidad);

            return redirect()->route('admin.unidades.index')->with('success', "La unidad {$unidad->numero_economico} tiene historial, se desactivo en lugar de eliminarse.");
        }

        DB::transaction(function () use ($unidad) {
            $unidad->asignaciones()->delete();
            $unidad->delete();
        });
        BitacoraService::registrar('unidad.eliminada', null, ['numero_economico' => $unidad->numero_economico]);

        return redirect()->route('admin.unidades.index')->with('success', "Unidad {$unidad->numero_economico} eliminada.");
    }

    public function regenerarToken(Unidad $unidad)
    {
        $unidad->update(['token_qr' => Unidad::nuevoTokenQr()]);
        BitacoraService::registrar('unidad.token_regenerado', $unidad);

        return redirect()->route('admin.unidades.show', $unidad)->with('success', 'Token QR regenerado. Imprime de nuevo la etiqueta de la unidad.');
    }

    private function datosFormulario(Unidad $unidad): array
    {
        return [
            'unidad' => $unidad,
            'zonas' => Catalogos::zonas(),
            'operadores' => Catalogos::operadores(),
            'operadorActual' => $unidad->exists ? $unidad->operadorActual?->hashid : null,
        ];
    }
}
