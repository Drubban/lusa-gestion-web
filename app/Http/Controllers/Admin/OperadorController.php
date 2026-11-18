<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OperadorRequest;
use App\Models\Operador;
use App\Models\Unidad;
use App\Models\Zona;
use App\Services\AsignacionService;
use App\Services\BitacoraService;
use App\Support\Catalogos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperadorController extends Controller
{
    public function __construct(private readonly AsignacionService $asignaciones) {}

    public function index(Request $request)
    {
        [$sort, $dir] = $this->orden($request->sort, $request->direction, ['clave_operador', 'nombre_completo'], 'clave_operador');

        $operadores = Operador::with('asignacionVigente.unidad.zona')
            ->buscar($request->search)
            ->when($request->zona, fn ($q, $z) => $q->whereHas('asignacionVigente.unidad', fn ($u) => $u->where('zona_id', Zona::decodeHashid($z))))
            ->when($request->filled('estado'), fn ($q) => $q->where('activo', $request->estado === 'activo'))
            ->orderBy($sort, $dir)
            ->paginate(20)
            ->withQueryString();

        return view('admin.operadores.index', ['operadores' => $operadores, 'zonas' => Catalogos::zonas()]);
    }

    public function create()
    {
        return view('admin.operadores.form', $this->datosFormulario(new Operador(['activo' => true])));
    }

    public function store(OperadorRequest $request)
    {
        $operador = DB::transaction(function () use ($request) {
            $operador = Operador::create($request->safe()->except('unidad_id'));
            if ($unidad = Unidad::find($request->validated('unidad_id'))) {
                $this->asignaciones->asignar($unidad, $operador);
            }

            return $operador;
        });
        BitacoraService::registrar('operador.creado', $operador);

        return redirect()->route('admin.operadores.show', $operador)->with('success', 'Operador creado correctamente.');
    }

    public function show(Operador $operador)
    {
        $operador->load(['asignaciones' => fn ($q) => $q->with('unidad.zona')->latest('fecha_inicio')]);

        return view('admin.operadores.show', compact('operador'));
    }

    public function edit(Operador $operador)
    {
        return view('admin.operadores.form', $this->datosFormulario($operador));
    }

    public function update(OperadorRequest $request, Operador $operador)
    {
        DB::transaction(function () use ($request, $operador) {
            $operador->update($request->safe()->except('unidad_id'));
            $unidad = Unidad::find($request->validated('unidad_id'));
            $unidad ? $this->asignaciones->asignar($unidad, $operador) : $this->asignaciones->liberarOperador($operador);
        });
        BitacoraService::registrar('operador.actualizado', $operador);

        return redirect()->route('admin.operadores.show', $operador)->with('success', 'Operador actualizado correctamente.');
    }

    public function destroy(Operador $operador)
    {
        $this->asignaciones->liberarOperador($operador);

        if ($operador->asignaciones()->exists()) {
            $operador->update(['activo' => false]);

            return redirect()->route('admin.operadores.index')->with('success', 'El operador tiene historial; se desactivo en lugar de eliminarse.');
        }

        $operador->delete();
        BitacoraService::registrar('operador.eliminado', null, ['clave' => $operador->clave_operador]);

        return redirect()->route('admin.operadores.index')->with('success', 'Operador eliminado.');
    }

    private function datosFormulario(Operador $operador): array
    {
        return [
            'operador' => $operador,
            'unidades' => Catalogos::unidades(),
            'unidadActual' => $operador->exists ? $operador->asignacionVigente?->unidad?->hashid : null,
        ];
    }
}
