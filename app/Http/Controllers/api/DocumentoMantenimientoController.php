<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentoMantenimientoRequest;
use App\Services\DocumentoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class DocumentoMantenimientoController extends Controller
{
    protected DocumentoService $documentoService;

    public function __construct(DocumentoService $documentoService)
    {
        $this->documentoService = $documentoService;
    }

    public function store(StoreDocumentoMantenimientoRequest $request): JsonResponse
    {
        try {
            // Los datos ya vienen validados por el Form Request
            $documento = $this->documentoService->crearDocumentoMantenimiento($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Documento de mantenimiento creado exitosamente',
                'data' => $documento->load(['unidad', 'operador', 'asignacion'])
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el documento de mantenimiento',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}