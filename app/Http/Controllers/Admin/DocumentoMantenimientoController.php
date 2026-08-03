<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionOperadorUnidad;
use App\Models\DocumentoMantenimiento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class DocumentoMantenimientoController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentoMantenimiento::with('asignacion.operador', 'asignacion.unidad');
        if ($request->filled('unidad')) {
            $query->whereHas('asignacion.unidad', fn ($q) => $q->where('numero_economico', 'LIKE', "%{$request->unidad}%"));
        }
        $documentos = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.documentos.mantenimiento.index', compact('documentos'));
    }

    public function show($id)
    {
        $documento = DocumentoMantenimiento::with(['asignacion.unidad.zona', 'asignacion.operador'])->findOrFail($id);

        return view('admin.documentos.mantenimiento.show', compact('documento'));
    }

    public function create()
    {
        $asignaciones = AsignacionOperadorUnidad::with('operador', 'unidad')->where('vigente', true)->get();

        return view('admin.documentos.mantenimiento.create', compact('asignaciones'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'asignacion_id' => 'required|exists:asignacion_operador_unidad,id',
            'rol' => 'required|string|max:50',
            'tecnologia' => 'required|array',
            'tecnologia.*' => 'string',
            'prueba_barras' => 'nullable|in:SI,NO',
            'comentarios' => 'nullable|string',
            'fecha' => 'required|date',
            'hora' => 'required',
            'veces_adeudo' => 'nullable|integer',
            'observaciones_adeudo' => 'nullable|string',
            'vigente' => 'sometimes|boolean',

        ]);

        $tecnologia = implode(',', $request->tecnologia);

        DocumentoMantenimiento::create([
            'asignacion_id' => $request->asignacion_id,
            'rol' => $request->rol,
            'tecnologia_reportada' => $tecnologia,
            'prueba_barras' => $request->prueba_barras,
            'comentarios' => $request->comentarios,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'veces_adeudo' => $request->veces_adeudo ?? 0,
            'observaciones_adeudo' => $request->observaciones_adeudo,
            'vigente' => $request->has('vigente'),
        ]);

        return redirect()->route('admin.documentos-mantenimiento.index')->with('success', 'Documento creado.');
        \Log::info('Datos recibidos:', $request->all());
        try {
            $doc = DocumentoMantenimiento::create($request->all());
            \Log::info('Documento creado con ID: '.$doc->id);
        } catch (\Exception $e) {
            \Log::error('Error al crear: '.$e->getMessage());
        }
    }

    public function edit($id)
    {
        $documento = DocumentoMantenimiento::findOrFail($id);
        $asignaciones = AsignacionOperadorUnidad::with('operador', 'unidad')->where('vigente', true)->get();

        // Convertir tecnología guardada (string) a array para los checkboxes
        $tecnologiaArray = explode(',', $documento->tecnologia_reportada ?? '');

        return view('admin.documentos.mantenimiento.edit', compact('documento', 'asignaciones', 'tecnologiaArray'));
    }

    public function update(Request $request, $id)
    {
        $documento = DocumentoMantenimiento::findOrFail($id);

        $request->validate([
            'asignacion_id' => 'required|exists:asignacion_operador_unidad,id',
            'rol' => 'required|string|max:50',
            'tecnologia' => 'required|array',
            'tecnologia.*' => 'string',
            'prueba_barras' => 'nullable|in:SI,NO',
            'comentarios' => 'nullable|string',
            'fecha' => 'required|date',
            'hora' => 'required',
            'veces_adeudo' => 'nullable|integer',
            'observaciones_adeudo' => 'nullable|string',
            'vigente' => 'sometimes|boolean',
        ]);

        $tecnologia = implode(',', $request->tecnologia);

        $documento->update([
            'asignacion_id' => $request->asignacion_id,
            'rol' => $request->rol,
            'tecnologia_reportada' => $tecnologia,
            'prueba_barras' => $request->prueba_barras,
            'comentarios' => $request->comentarios,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'veces_adeudo' => $request->veces_adeudo ?? 0,
            'observaciones_adeudo' => $request->observaciones_adeudo,
            'vigente' => $request->has('vigente'),
        ]);

        return redirect()->route('admin.documentos-mantenimiento.index')->with('success', 'Documento actualizado.');
    }

    public function destroy($id)
    {
        $documento = DocumentoMantenimiento::findOrFail($id);
        $documento->delete();

        return redirect()->route('admin.documentos-mantenimiento.index')->with('success', 'Documento eliminado.');
    }

    public function exportarPdf($id)
    {
        $documento = DocumentoMantenimiento::with(['asignacion.unidad.zona', 'asignacion.operador'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.documentos.plantilla_mantenimiento', compact('documento'));

        return $pdf->download("mantenimiento_{$documento->id}.pdf");
    }

    public function exportarWord($id)
    {
        $documento = DocumentoMantenimiento::with(['asignacion.unidad.zona', 'asignacion.operador'])->findOrFail($id);
        $phpWord = new PhpWord;
        $section = $phpWord->addSection();

        $section->addTitle('Formato de Mantenimiento', 1);
        $section->addText("Unidad: {$documento->asignacion->unidad->numero_economico} - {$documento->asignacion->unidad->nombre_unidad}");
        $section->addText("Rol: {$documento->rol}");
        $section->addText("Operador: {$documento->asignacion->operador->nombre_completo}");
        $section->addText("Clave: {$documento->asignacion->operador->clave_operador}");
        $section->addText("Tecnología reportada: {$documento->tecnologia_reportada}");
        $section->addText("Prueba barras: {$documento->prueba_barras}");
        $section->addText("Comentarios: {$documento->comentarios}");
        $section->addText("Fecha: {$documento->fecha} Hora: {$documento->hora}");
        $section->addText("Adeudos: {$documento->veces_adeudo} - {$documento->observaciones_adeudo}");

        $section->addTextBreak(2);
        $section->addText('______________________');
        $section->addText('Firma del operador', ['size' => 8]);
        $section->addText('______________________');
        $section->addText('Firma del Ing. a cargo', ['size' => 8]);
        $section->addText('______________________');
        $section->addText('Firma de tabulación', ['size' => 8]);

        $tempFile = tempnam(sys_get_temp_dir(), 'word_');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, "mantenimiento_{$documento->id}.docx")->deleteFileAfterSend(true);
    }
}
