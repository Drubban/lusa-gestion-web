<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionOperadorUnidad;
use App\Models\DocumentoCapacitacion;
use App\Services\DocumentoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DocumentoCapacitacionController extends Controller
{
    protected DocumentoService $documentoService;

    public function __construct(DocumentoService $documentoService)
    {
        $this->documentoService = $documentoService;
    }

    public function index()
    {
        $documentos = DocumentoCapacitacion::with('asignacion.operador', 'asignacion.unidad')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.documentos.capacitacion.index', compact('documentos'));
    }

    public function show($id)
    {
        $documento = DocumentoCapacitacion::with(['asignacion.unidad.zona', 'asignacion.operador'])
            ->findOrFail($id);

        return view('admin.documentos.capacitacion.show', compact('documento'));
    }

    public function create()
    {
        $asignaciones = AsignacionOperadorUnidad::with('operador', 'unidad')
            ->where('vigente', true)
            ->get();

        return view('admin.documentos.capacitacion.create', compact('asignaciones'));
    }

    public function store(Request $request)
    {
        Log::info('=== INTENTO DE GUARDAR CAPACITACIÓN ===');
        Log::info('Datos recibidos:', $request->all());

        try {
            $validated = $request->validate([
                'asignacion_id' => 'required|exists:asignacion_operador_unidad,id',
                'fecha' => 'required|date',
                'hora' => 'required',
                'vigente' => 'sometimes|boolean',
            ]);

            // Obtener la asignación para obtener datos adicionales
            $asignacion = AsignacionOperadorUnidad::with('operador', 'unidad')
                ->findOrFail($validated['asignacion_id']);

            // Preparar datos para el servicio
            $data = [
                'asignacion_id' => $validated['asignacion_id'],
                'operador_id' => $asignacion->operador_id,
                'unidad_id' => $asignacion->unidad_id,
                'fecha' => $validated['fecha'],
                'hora' => $validated['hora'],
                'vigente' => $request->has('vigente'),
            ];

            // Usar el servicio para crear el documento
            $documento = $this->documentoService->crearDocumentoCapacitacion($data);

            Log::info('✅ Documento de capacitación creado con ID: ' . $documento->id);

            return redirect()->route('admin.documentos-capacitacion.index')
                ->with('success', 'Documento de capacitación creado exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Error de validación: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            Log::error('❌ Error al crear documento de capacitación: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->withErrors(['error' => 'Error al crear el documento: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $documento = DocumentoCapacitacion::findOrFail($id);
        // Convertir la fecha a objeto Carbon si no lo está
        $documento->fecha = Carbon::parse($documento->fecha);
        
        $asignaciones = AsignacionOperadorUnidad::with('operador', 'unidad')
            ->where('vigente', true)
            ->get();

        return view('admin.documentos.capacitacion.edit', compact('documento', 'asignaciones'));
    }

    public function update(Request $request, $id)
    {
        $documento = DocumentoCapacitacion::findOrFail($id);

        try {
            $validated = $request->validate([
                'asignacion_id' => 'required|exists:asignacion_operador_unidad,id',
                'fecha' => 'required|date',
                'hora' => 'required',
                'vigente' => 'sometimes|boolean',
            ]);

            // Obtener la asignación para actualizar datos relacionados
            $asignacion = AsignacionOperadorUnidad::findOrFail($validated['asignacion_id']);

            $documento->update([
                'asignacion_id' => $validated['asignacion_id'],
                'operador_id' => $asignacion->operador_id,
                'unidad_id' => $asignacion->unidad_id,
                'fecha' => $validated['fecha'],
                'hora' => $validated['hora'],
                'vigente' => $request->has('vigente'),
            ]);

            Log::info('✅ Documento de capacitación actualizado ID: ' . $id);

            return redirect()->route('admin.documentos-capacitacion.index')
                ->with('success', 'Documento de capacitación actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('❌ Error al actualizar documento: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $documento = DocumentoCapacitacion::findOrFail($id);
            $documento->delete();

            Log::info('✅ Documento de capacitación eliminado ID: ' . $id);

            return redirect()->route('admin.documentos-capacitacion.index')
                ->with('success', 'Documento de capacitación eliminado exitosamente.');

        } catch (\Exception $e) {
            Log::error('❌ Error al eliminar documento: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    public function exportarPdf($id)
    {
        $documento = DocumentoCapacitacion::with(['asignacion.unidad.zona', 'asignacion.operador'])
            ->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.documentos.plantilla_capacitacion', compact('documento'));

        return $pdf->download("capacitacion_{$documento->id}.pdf");
    }

    public function exportarWord($id)
    {
        $documento = DocumentoCapacitacion::with(['asignacion.unidad', 'asignacion.operador.zona'])
            ->findOrFail($id);
        
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

        return response()->download($tempFile, "capacitacion_{$documento->id}.docx")
            ->deleteFileAfterSend(true);
    }
}