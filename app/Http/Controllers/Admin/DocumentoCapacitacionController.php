<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionOperadorUnidad;
use App\Models\DocumentoCapacitacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Support\Facades\Log;

class DocumentoCapacitacionController extends Controller
{
    public function index()
    {
        $documentos = DocumentoCapacitacion::with('asignacion.operador', 'asignacion.unidad')->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.documentos.capacitacion.index', compact('documentos'));
    }

    public function show($id)
    {
        $documento = DocumentoCapacitacion::with(['asignacion.unidad.zona', 'asignacion.operador'])->findOrFail($id);

        return view('admin.documentos.capacitacion.show', compact('documento'));
    }

    public function create()
    {
        $asignaciones = AsignacionOperadorUnidad::with('operador', 'unidad')->where('vigente', true)->get();

        return view('admin.documentos.capacitacion.create', compact('asignaciones'));
    }

    public function store(Request $request)
{
    // Log de entrada
    Log::info('=== INTENTO DE GUARDAR CAPACITACIÓN ===');
    Log::info('Método: ' . $request->method());
    Log::info('URL: ' . $request->fullUrl());
    Log::info('Cabeceras:', $request->headers->all());
    Log::info('Datos recibidos:', $request->all());
    Log::info('Token CSRF recibido: ' . ($request->input('_token') ? 'SÍ' : 'NO'));
    
    try {
        $request->validate([
            'asignacion_id' => 'required|exists:asignacion_operador_unidad,id',
            // 'zona' => 'required|string|in:reyes,apaxco,citrus',
            'fecha' => 'required|date',
            'hora' => 'required',
        ]);
        
        Log::info('✅ Validación pasada correctamente');
        
        $documento = DocumentoCapacitacion::create([
            'asignacion_id' => $request->asignacion_id,
            // 'zona' => $request->zona,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'vigente' => $request->has('vigente'),
        ]);
        
        Log::info('✅ Documento guardado con ID: ' . $documento->id);
        
        return redirect()->route('admin.documentos-capacitacion.index')->with('success', 'Documento creado.');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('❌ Error de validación: ' . json_encode($e->errors()));
        throw $e;
    } catch (\Exception $e) {
        Log::error('❌ Error general: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        throw $e;
    }
}

    public function edit($id)
{
    $documento = DocumentoCapacitacion::findOrFail($id);
    // Convertir la fecha a objeto Carbon si no lo está
    $documento->fecha = \Carbon\Carbon::parse($documento->fecha);
    $asignaciones = AsignacionOperadorUnidad::with('operador', 'unidad')->where('vigente', true)->get();

    return view('admin.documentos.capacitacion.edit', compact('documento', 'asignaciones'));
}

    public function update(Request $request, $id)
    {
        $documento = DocumentoCapacitacion::findOrFail($id);
        $request->validate([
            'asignacion_id' => 'required|exists:asignacion_operador_unidad,id',
            // 'zona' => 'required|in:Reyes,Apaxco,Citrus|string',
            'fecha' => 'required|date',
            'hora' => 'required',
            'vigente' => 'sometimes|boolean',
        ]);

        $documento->update([
            'asignacion_id' => $request->asignacion_id,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            // 'zona' => $request->zona,
            'vigente' => $request->has('vigente'),
        ]);

        return redirect()->route('admin.documentos-capacitacion.index')->with('success', 'Documento actualizado.');
    }

    public function destroy($id)
    {
        $documento = DocumentoCapacitacion::findOrFail($id);
        $documento->delete();

        return redirect()->route('admin.documentos-capacitacion.index')->with('success', 'Documento eliminado.');
    }

    public function exportarPdf($id)
    {
        $documento = DocumentoCapacitacion::with(['asignacion.unidad.zona', 'asignacion.operador'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.documentos.plantilla_capacitacion', compact('documento'));

        return $pdf->download("capacitacion_{$documento->id}.pdf");
    }

    public function exportarWord($id)
    {
        $documento = DocumentoCapacitacion::with(['asignacion.unidad', 'asignacion.operador.zona'])->findOrFail($id);
        $phpWord = new PhpWord;
        $section = $phpWord->addSection();
        $section->addTitle('Conformidad de Capacitación', 1);
        $zonaNombre = $documento->asignacion->unidad->zona->nombre ?? 'N/A';
        $section->addText("Zona: {$zonaNombre}");
        $section->addText("Fecha: {$documento->fecha} Hora: {$documento->hora}");
        $section->addText("Operador: {$documento->asignacion->operador->nombre_completo} (Clave: {$documento->asignacion->operador->clave_operador})");
        $section->addText("Unidad: {$documento->asignacion->unidad->numero_economico}");
        $section->addTextBreak(2);
        $section->addText('______________________', ['size' => 10]);
        $section->addText('Firma del operador', ['size' => 8]);
        $section->addText('______________________', ['size' => 10]);
        $section->addText('Firma del Ing. a cargo', ['size' => 8]);

        $tempFile = tempnam(sys_get_temp_dir(), 'word_');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, "capacitacion_{$documento->id}.docx")->deleteFileAfterSend(true);
    }
}
