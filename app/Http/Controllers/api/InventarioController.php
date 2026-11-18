<?php

namespace App\Http\Controllers\Api;

use App\Enums\CategoriaInventario;
use App\Http\Controllers\Controller;
use App\Http\Resources\InventarioResource;
use App\Models\Inventario;
use Illuminate\Http\Request;

/** Consulta de inventario desde la app (solo lectura). */
class InventarioController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->tokenCan('inventario:leer'), 403);

        return InventarioResource::collection(
            Inventario::with('departamento')
                ->filtrar($request->only(['categoria', 'search']))
                ->latest('fecha_entrega')
                ->paginate(30),
        )->additional(['categorias' => CategoriaInventario::opciones()]);
    }
}
