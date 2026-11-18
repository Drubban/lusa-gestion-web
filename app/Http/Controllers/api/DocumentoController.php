<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentoCapacitacionRequest;
use App\Http\Requests\DocumentoMantenimientoRequest;
use App\Services\DocumentoService;

class DocumentoController extends Controller
{
    public function __construct(private readonly DocumentoService $documentos) {}

    public function mantenimiento(DocumentoMantenimientoRequest $request)
    {
        $doc = $this->documentos->guardarMantenimiento($request->validated());

        return response()->json(['id' => $doc->hashid, 'folio' => $doc->folio, 'uuid' => $doc->uuid], $doc->wasRecentlyCreated ? 201 : 200);
    }

    public function capacitacion(DocumentoCapacitacionRequest $request)
    {
        $doc = $this->documentos->guardarCapacitacion($request->validated());

        return response()->json(['id' => $doc->hashid, 'folio' => $doc->folio, 'uuid' => $doc->uuid], $doc->wasRecentlyCreated ? 201 : 200);
    }
}
