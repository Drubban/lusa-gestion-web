<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TipoTecnologia;
use App\Http\Controllers\Controller;
use App\Http\Requests\TecnologiaRequest;
use App\Models\Tecnologia;
use App\Models\Unidad;
use App\Services\BitacoraService;
use App\Support\Catalogos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TecnologiaController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->only(['tipo', 'search']);
        $filtros['unidad_id'] = Unidad::decodeHashid($request->unidad);

        $tecnologias = Tecnologia::with(['unidad', 'barras', 'telpo', 'gps', 'mdvr'])
            ->filtrar($filtros)
            ->latest()
            ->paginate(20)->withQueryString();

        $conteo = Tecnologia::selectRaw('tipo, COUNT(*) as total')->groupBy('tipo')->pluck('total', 'tipo');

        return view('admin.tecnologias.index', [
            'tecnologias' => $tecnologias,
            'tipos' => TipoTecnologia::opciones(),
            'conteo' => $conteo,
            'unidades' => Catalogos::unidades(),
        ]);
    }

    public function create()
    {
        return view('admin.tecnologias.form', $this->datosFormulario(new Tecnologia(['activo' => true])));
    }

    /** Un solo metodo de guardado para todos los subtipos (antes eran 3 switch duplicados). */
    public function store(TecnologiaRequest $request)
    {
        $tecnologia = DB::transaction(function () use ($request) {
            $tecnologia = Tecnologia::create($request->safe()->except('detalle') + ['created_by' => $request->user()->id]);
            $tecnologia->{$tecnologia->tipo->relacion()}()->create($request->detalle());

            return $tecnologia;
        });
        BitacoraService::registrar('tecnologia.creada', $tecnologia);

        return redirect()->route('admin.tecnologias.show', $tecnologia)->with('success', 'Tecnologia registrada.');
    }

    public function show(Tecnologia $tecnologia)
    {
        $tecnologia->load(['unidad.zona', 'creador', $tecnologia->tipo->relacion()]);

        return view('admin.tecnologias.show', compact('tecnologia'));
    }

    public function edit(Tecnologia $tecnologia)
    {
        $tecnologia->load($tecnologia->tipo->relacion());

        return view('admin.tecnologias.form', $this->datosFormulario($tecnologia));
    }

    public function update(TecnologiaRequest $request, Tecnologia $tecnologia)
    {
        DB::transaction(function () use ($request, $tecnologia) {
            $tipoAnterior = $tecnologia->tipo;
            $tecnologia->update($request->safe()->except('detalle'));
            if ($tipoAnterior !== $tecnologia->tipo) {
                $tecnologia->{$tipoAnterior->relacion()}()->delete();
            }
            $tecnologia->{$tecnologia->tipo->relacion()}()->updateOrCreate(['tecnologia_id' => $tecnologia->id], $request->detalle());
        });
        BitacoraService::registrar('tecnologia.actualizada', $tecnologia);

        return redirect()->route('admin.tecnologias.show', $tecnologia)->with('success', 'Tecnologia actualizada.');
    }

    public function destroy(Tecnologia $tecnologia)
    {
        // El detalle se elimina por ON DELETE CASCADE
        BitacoraService::registrar('tecnologia.eliminada', $tecnologia);
        $tecnologia->delete();

        return redirect()->route('admin.tecnologias.index')->with('success', 'Tecnologia eliminada.');
    }

    private function datosFormulario(Tecnologia $tecnologia): array
    {
        $campos = [];
        foreach (TipoTecnologia::cases() as $tipo) {
            $campos[$tipo->value] = $tipo->campos();
        }

        return [
            'tecnologia' => $tecnologia,
            'unidades' => Catalogos::unidades(),
            'tipos' => TipoTecnologia::opciones(),
            'camposPorTipo' => $campos,
            'detalle' => $tecnologia->exists ? ($tecnologia->detalle()?->toArray() ?? []) : [],
        ];
    }
}
