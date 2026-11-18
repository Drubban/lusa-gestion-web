<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentoMantenimientoRequest;
use App\Models\DocumentoMantenimiento;
use App\Models\Unidad;
use App\Services\BitacoraService;
use App\Services\DocumentoService;
use App\Support\Catalogos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class DocumentoMantenimientoController extends Controller
{
    public function __construct(private readonly DocumentoService $documentos) {}

    public function index(Request $request)
    {
        $documentos = DocumentoMantenimiento::with(['unidad.zona', 'operador'])
            ->when($request->unidad, fn ($q, $u) => $q->whereHas('unidad', fn ($w) => $w->where('numero_economico', 'ILIKE', "%{$u}%")))
            ->when($request->desde, fn ($q, $d) => $q->whereDate('fecha', '>=', $d))
            ->when($request->hasta, fn ($q, $h) => $q->whereDate('fecha', '<=', $h))
            ->latest('fecha')->latest('id')
            ->paginate(20)->withQueryString();

        return view('admin.documentos.mantenimiento.index', compact('documentos'));
    }

    public function create(Request $request)
    {
        $documento = new DocumentoMantenimiento(['fecha' => today(), 'hora' => now()->format('H:i'), 'vigente' => true]);
        if ($unidad = Unidad::findByHashid($request->unidad)) {
            $documento->unidad_id = $unidad->id;
        }

        return view('admin.documentos.mantenimiento.form', $this->datosFormulario($documento));
    }

    public function store(DocumentoMantenimientoRequest $request)
    {
        $documento = $this->documentos->guardarMantenimiento($request->validated());

        return redirect()->route('admin.documentos-mantenimiento.show', $documento)->with('success', 'Documento de mantenimiento registrado.');
    }

    public function show(DocumentoMantenimiento $documento)
    {
        $documento->load(['unidad.zona', 'operador', 'usuario', 'firmas']);

        return view('admin.documentos.mantenimiento.show', compact('documento'));
    }

    public function edit(DocumentoMantenimiento $documento)
    {
        return view('admin.documentos.mantenimiento.form', $this->datosFormulario($documento));
    }

    public function update(DocumentoMantenimientoRequest $request, DocumentoMantenimiento $documento)
    {
        $this->documentos->guardarMantenimiento($request->validated(), $documento);

        return redirect()->route('admin.documentos-mantenimiento.show', $documento)->with('success', 'Documento actualizado.');
    }

    public function destroy(DocumentoMantenimiento $documento)
    {
        BitacoraService::registrar('documento_mantenimiento.eliminado', $documento);
        $documento->firmas()->delete();
        $documento->delete();

        return redirect()->route('admin.documentos-mantenimiento.index')->with('success', 'Documento eliminado.');
    }

    public function pdf(DocumentoMantenimiento $documento)
    {
        $documento->load(['unidad.zona', 'operador', 'firmas']);

        return Pdf::loadView('admin.documentos.plantilla_mantenimiento', compact('documento'))
            ->setPaper('letter')
            // isRemoteEnabled desactivado: evita que el PDF descargue recursos externos (SSRF)
            ->setOptions(['isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans'])
            ->download("mantenimiento_{$documento->folio}.pdf");
    }

    public function word(DocumentoMantenimiento $documento)
    {
        $documento->load(['unidad.zona', 'operador']);
        $word = new PhpWord;
        $s = $word->addSection();
        $s->addTitle('Formato de servicio y analisis de equipos tecnologicos', 1);
        $filas = [
            'Folio' => $documento->folio,
            'Unidad' => $documento->unidad?->etiqueta,
            'Zona' => $documento->unidad?->zona?->nombre,
            'Operador' => $documento->operador ? "{$documento->operador->nombre_completo} ({$documento->operador->clave_operador})" : 'Sin operador',
            'Tecnologia reportada' => implode(', ', $documento->tecnologias),
            'Estado de camaras' => $documento->estado_camaras,
            'Prueba de barras' => $documento->prueba_barras,
            'Comentarios' => $documento->comentarios,
            'Fecha y hora' => $documento->fecha?->format('d/m/Y') . ' ' . substr((string) $documento->hora, 0, 5),
            'Adeudos' => "{$documento->veces_adeudo} {$documento->observaciones_adeudo}",
        ];
        foreach ($filas as $k => $v) {
            $s->addText(htmlspecialchars("{$k}: " . ($v ?? '-')));
        }
        $s->addTextBreak(2);
        foreach (DocumentoMantenimiento::FIRMAS as $etiqueta) {
            $s->addText('______________________________');
            $s->addText($etiqueta, ['size' => 8]);
        }
        $tmp = tempnam(sys_get_temp_dir(), 'mto_');
        IOFactory::createWriter($word, 'Word2007')->save($tmp);

        return response()->download($tmp, "mantenimiento_{$documento->folio}.docx")->deleteFileAfterSend();
    }

    private function datosFormulario(DocumentoMantenimiento $documento): array
    {
        return [
            'documento' => $documento,
            'unidades' => Catalogos::unidades(),
            'operadores' => Catalogos::operadores(),
            'tecnologias' => DocumentoMantenimiento::TECNOLOGIAS,
        ];
    }
}
