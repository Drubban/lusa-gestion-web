<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AjusteRequest;
use App\Models\Ajuste;
use App\Models\Unidad;
use App\Services\BitacoraService;
use App\Support\Catalogos;
use Illuminate\Http\Request;

class AjusteController extends Controller
{
    public function index(Request $request)
    {
        $ajustes = Ajuste::with(['operador', 'unidad', 'zona'])
            ->filtrar($request->only(['search', 'desde', 'hasta', 'firmado']))
            ->latest('fecha')->latest('id')
            ->paginate(20)->withQueryString();

        $totales = Ajuste::filtrar($request->only(['search', 'desde', 'hasta', 'firmado']))
            ->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(monto_total),0) as monto')->first();

        return view('admin.ajustes.index', compact('ajustes', 'totales'));
    }

    public function create()
    {
        return view('admin.ajustes.form', $this->datosFormulario(new Ajuste(['fecha' => today(), 'hora' => now()->format('H:i')])));
    }

    public function store(AjusteRequest $request)
    {
        $datos = $request->validated();
        // Si no se indica zona se toma la de la unidad
        $datos['zona_id'] ??= Unidad::find($datos['unidad_id'])?->zona_id;
        $datos['created_by'] = $request->user()->id;
        $ajuste = Ajuste::create($datos);
        BitacoraService::registrar('ajuste.creado', $ajuste);

        return redirect()->route('admin.ajustes.show', $ajuste)->with('success', "Ajuste {$ajuste->folio} registrado.");
    }

    public function show(Ajuste $ajuste)
    {
        $ajuste->load(['operador', 'unidad', 'zona', 'creador']);

        return view('admin.ajustes.show', compact('ajuste'));
    }

    public function edit(Ajuste $ajuste)
    {
        return view('admin.ajustes.form', $this->datosFormulario($ajuste));
    }

    public function update(AjusteRequest $request, Ajuste $ajuste)
    {
        $datos = $request->validated();
        $datos['zona_id'] ??= Unidad::find($datos['unidad_id'])?->zona_id;
        $ajuste->update($datos);
        BitacoraService::registrar('ajuste.actualizado', $ajuste);

        return redirect()->route('admin.ajustes.show', $ajuste)->with('success', "Ajuste {$ajuste->folio} actualizado.");
    }

    public function destroy(Ajuste $ajuste)
    {
        BitacoraService::registrar('ajuste.eliminado', $ajuste, ['folio' => $ajuste->folio]);
        $ajuste->delete();

        return redirect()->route('admin.ajustes.index')->with('success', 'Ajuste eliminado.');
    }

    private function datosFormulario(Ajuste $ajuste): array
    {
        return ['ajuste' => $ajuste, 'operadores' => Catalogos::operadores(), 'unidades' => Catalogos::unidades(), 'zonas' => Catalogos::zonas()];
    }
}
