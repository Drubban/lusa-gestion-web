<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentoCapacitacionRequest;
use App\Models\DocumentoCapacitacion;
use App\Services\BitacoraService;
use App\Services\DocumentoService;
use App\Support\Catalogos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocumentoCapacitacionController extends Controller
{
    public function __construct(private readonly DocumentoService $documentos) {}

    public function index(Request $request)
    {
        $documentos = DocumentoCapacitacion::with(['unidad', 'operador'])
            ->when($request->search, fn ($q, $t) => $q->where(fn ($w) => $w
                ->whereHas('operador', fn ($o) => $o->buscar($t))
                ->orWhereHas('unidad', fn ($u) => $u->where('numero_economico', 'ILIKE', "%{$t}%"))))
            ->latest('fecha')->latest('id')
            ->paginate(20)->withQueryString();

        return view('admin.documentos.capacitacion.index', compact('documentos'));
    }

    public function create()
    {
        return view('admin.documentos.capacitacion.form', $this->datosFormulario(
            new DocumentoCapacitacion(['fecha' => today(), 'hora' => now()->format('H:i'), 'vigente' => true]),
        ));
    }

    public function store(DocumentoCapacitacionRequest $request)
    {
        $documento = $this->documentos->guardarCapacitacion($request->validated());

        return redirect()->route('admin.documentos-capacitacion.show', $documento)->with('success', 'Constancia de capacitacion registrada.');
    }

    public function show(DocumentoCapacitacion $documento)
    {
        $documento->load(['unidad.zona', 'operador', 'usuario', 'firmas']);

        return view('admin.documentos.capacitacion.show', compact('documento'));
    }

    public function edit(DocumentoCapacitacion $documento)
    {
        return view('admin.documentos.capacitacion.form', $this->datosFormulario($documento));
    }

    public function update(DocumentoCapacitacionRequest $request, DocumentoCapacitacion $documento)
    {
        $this->documentos->guardarCapacitacion($request->validated(), $documento);

        return redirect()->route('admin.documentos-capacitacion.show', $documento)->with('success', 'Constancia actualizada.');
    }

    public function destroy(DocumentoCapacitacion $documento)
    {
        BitacoraService::registrar('documento_capacitacion.eliminado', $documento);
        $documento->firmas()->delete();
        $documento->delete();

        return redirect()->route('admin.documentos-capacitacion.index')->with('success', 'Constancia eliminada.');
    }

    public function pdf(DocumentoCapacitacion $documento)
    {
        $documento->load(['unidad.zona', 'operador', 'firmas']);

        return Pdf::loadView('admin.documentos.plantilla_capacitacion', compact('documento'))
            ->setPaper('letter')
            ->setOptions(['isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans'])
            ->download("capacitacion_{$documento->folio}.pdf");
    }

    private function datosFormulario(DocumentoCapacitacion $documento): array
    {
        return ['documento' => $documento, 'unidades' => Catalogos::unidades(), 'operadores' => Catalogos::operadores()];
    }
}
