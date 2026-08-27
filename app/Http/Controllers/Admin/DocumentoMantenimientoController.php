<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionOperadorUnidad;
use App\Models\DocumentoMantenimiento;
use App\Models\Unidad;
use App\Services\DocumentoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Support\Facades\Log;

class DocumentoMantenimientoController extends Controller
{
    protected DocumentoService $documentoService;

    public function __construct(DocumentoService $documentoService)
    {
        $this->documentoService = $documentoService;
    }

    public function index(Request $request)
    {
        $query = DocumentoMantenimiento::with('asignacion.operador', 'asignacion.unidad');

        if ($request->filled('unidad')) {
            $query->whereHas('asignacion.unidad', fn($q) => $q->where('numero_economico', 'LIKE', "%{$request->unidad}%"));
        }

        $documentos = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.documentos.mantenimiento.index', compact('documentos'));
    }

    public function show($id)
    {
        $documento = DocumentoMantenimiento::with(['asignacion.unidad.zona', 'asignacion.operador'])->findOrFail($id);

        return view('admin.documentos.mantenimiento.show', compact('documento'));
    }

    public function create(Request $request)
    {
        // Obtener todas las unidades activas con su zona
        $unidades = Unidad::with(['zona'])
            ->where('activo', true)
            ->orderBy('numero_economico')
            ->get();

        // Si viene una unidad por parámetro, usarla para pre-seleccionar
        $unidadSeleccionada = null;
        if ($request->has('unidad')) {
            $unidadSeleccionada = Unidad::with(['zona', 'asignacionVigente.operador'])
                ->find($request->unidad);
        }

        return view('admin.documentos.mantenimiento.create', compact('unidades', 'unidadSeleccionada'));
    }

    public function store(Request $request)
    {
        Log::info('=== INTENTO DE GUARDAR MANTENIMIENTO ===');
        Log::info('Datos recibidos:', $request->all());

        try {
            $validated = $request->validate([
                'unidad_id' => 'required|exists:unidades,id',
                'zona' => 'required|in:reyes,apaxco,citrus',
                'tecnologia' => 'required|array',
                'tecnologia.*' => 'string',
                'camara1' => 'sometimes|boolean',
                'camara2' => 'sometimes|boolean',
                'camara3' => 'sometimes|boolean',
                'camara4' => 'sometimes|boolean',
                'prueba_barras' => 'nullable|in:SI,NO',
                'comentarios' => 'nullable|string',
                'fecha' => 'required|date',
                'hora' => 'required',
                // 🔥 ELIMINAR LA VALIDACION DE 'vigente' porque la manejamos manualmente
            ]);

            // Obtener la unidad
            $unidad = Unidad::with('zona')->findOrFail($validated['unidad_id']);

            // Buscar la asignacion vigente de la unidad (para obtener el operador)
            $asignacion = AsignacionOperadorUnidad::where('unidad_id', $validated['unidad_id'])
                ->where('vigente', true)
                ->first();

            // Si no hay asignacion, crear una temporal
            if (!$asignacion) {
                $asignacion = AsignacionOperadorUnidad::create([
                    'unidad_id' => $validated['unidad_id'],
                    'operador_id' => null,
                    'fecha_inicio' => now(),
                    'vigente' => true,
                ]);
            }

            // Construir el string de tecnologias
            $tecnologias = implode(',', $validated['tecnologia']);

            // Construir estado de camaras
            $camaras = [];
            if ($request->has('camara1')) $camaras[] = 'Camara 1 OK';
            if ($request->has('camara2')) $camaras[] = 'Camara 2 OK';
            if ($request->has('camara3')) $camaras[] = 'Camara 3 OK';
            if ($request->has('camara4')) $camaras[] = 'Camara 4 OK';
            $estadoCamaras = !empty($camaras) ? implode(', ', $camaras) : 'Sin camaras funcionales';

            // 🔥 MANEJAR VIGENTE MANUALMENTE - Convertir 'on' a true/false
            $vigente = $request->has('vigente') && $request->input('vigente') !== 'off';

            $documento = DocumentoMantenimiento::create([
                'asignacion_id' => $asignacion->id,
                'rol' => $validated['zona'],
                'tecnologia_reportada' => $tecnologias,
                'estado_camaras' => $estadoCamaras,
                'prueba_barras' => $validated['prueba_barras'] ?? null,
                'comentarios' => $validated['comentarios'] ?? null,
                'fecha' => $validated['fecha'],
                'hora' => $validated['hora'],
                'vigente' => $vigente,
            ]);

            Log::info('✅ Documento de mantenimiento creado con ID: ' . $documento->id . ' - Vigente: ' . ($vigente ? 'SI' : 'NO'));

            return redirect()->route('admin.documentos-mantenimiento.index')
                ->with('success', 'Documento de mantenimiento creado exitosamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Error de validacion: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            Log::error('❌ Error al crear documento de mantenimiento: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->withErrors(['error' => 'Error al crear el documento: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $documento = DocumentoMantenimiento::findOrFail($id);
        $asignaciones = AsignacionOperadorUnidad::with('operador', 'unidad')
            ->where('vigente', true)
            ->get();

        // Convertir tecnología guardada (string) a array para los checkboxes
        $tecnologiaArray = explode(',', $documento->tecnologia_reportada ?? '');

        return view('admin.documentos.mantenimiento.edit', compact('documento', 'asignaciones', 'tecnologiaArray'));
    }

    public function update(Request $request, $id)
    {
        $documento = DocumentoMantenimiento::findOrFail($id);

        try {
            $validated = $request->validate([
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

            $tecnologia = implode(',', $validated['tecnologia']);

            $documento->update([
                'asignacion_id' => $validated['asignacion_id'],
                'rol' => $validated['rol'],
                'tecnologia_reportada' => $tecnologia,
                'prueba_barras' => $validated['prueba_barras'] ?? null,
                'comentarios' => $validated['comentarios'] ?? null,
                'fecha' => $validated['fecha'],
                'hora' => $validated['hora'],
                'veces_adeudo' => $validated['veces_adeudo'] ?? 0,
                'observaciones_adeudo' => $validated['observaciones_adeudo'] ?? null,
                'vigente' => $request->has('vigente'),
            ]);

            Log::info('Documento de mantenimiento actualizado ID: ' . $id);

            return redirect()->route('admin.documentos-mantenimiento.index')
                ->with('success', 'Documento de mantenimiento actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar documento: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $documento = DocumentoMantenimiento::findOrFail($id);
            $documento->delete();

            Log::info('Documento de mantenimiento eliminado ID: ' . $id);

            return redirect()->route('admin.documentos-mantenimiento.index')
                ->with('success', 'Documento de mantenimiento eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar documento: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    public function exportarPdf($id)
    {
        $documento = DocumentoMantenimiento::with(['asignacion.unidad.zona', 'asignacion.operador'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('admin.documentos.plantilla_mantenimiento', compact('documento'));

        return $pdf->download("mantenimiento_{$documento->id}.pdf");
    }

    public function exportarWord($id)
    {
        $documento = DocumentoMantenimiento::with(['asignacion.unidad.zona', 'asignacion.operador'])
            ->findOrFail($id);

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

        return response()->download($tempFile, "mantenimiento_{$documento->id}.docx")
            ->deleteFileAfterSend(true);
    }
}
