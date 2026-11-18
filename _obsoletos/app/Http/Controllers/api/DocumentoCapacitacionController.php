<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentoCapacitacionRequest;
use App\Services\DocumentoService;
use Illuminate\Http\JsonResponse;
use Exception;

class DocumentoCapacitacionController extends Controller
{
    protected DocumentoService $documentoService;

    public function __construct(DocumentoService $documentoService)
    {
        $this->documentoService = $documentoService;
    }

    public function store(StoreDocumentoCapacitacionRequest $request): JsonResponse
    {
        try {
            $documento = $this->documentoService->crearDocumentoCapacitacion($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Documento de capacitación creado exitosamente',
                'data' => $documento->load(['operador', 'unidad', 'asignacion'])
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el documento de capacitación',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}