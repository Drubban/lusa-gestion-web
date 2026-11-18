<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CategoriaInventario;
use App\Http\Controllers\Controller;
use App\Http\Requests\InventarioRequest;
use App\Models\Inventario;
use App\Services\BitacoraService;
use App\Support\Catalogos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $inventarios = Inventario::with('departamento')
            ->filtrar($request->only(['categoria', 'desde', 'hasta', 'search']))
            ->latest('fecha_entrega')->latest('id')
            ->paginate(20)->withQueryString();

        return view('admin.inventario.index', ['inventarios' => $inventarios, 'categorias' => CategoriaInventario::opciones()]);
    }

    public function create()
    {
        return view('admin.inventario.form', $this->datosFormulario(new Inventario(['fecha_entrega' => today(), 'cantidad' => 1])));
    }

    public function store(InventarioRequest $request)
    {
        $datos = $request->safe()->except('imagen');
        $datos['created_by'] = $request->user()->id;
        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('inventario', 'public');
        }
        $inventario = Inventario::create($datos);
        BitacoraService::registrar('inventario.creado', $inventario);

        return redirect()->route('admin.inventario.show', $inventario)->with('success', 'Entrega registrada en inventario.');
    }

    public function show(Inventario $inventario)
    {
        $inventario->load(['departamento', 'creador']);

        return view('admin.inventario.show', compact('inventario'));
    }

    public function edit(Inventario $inventario)
    {
        return view('admin.inventario.form', $this->datosFormulario($inventario));
    }

    public function update(InventarioRequest $request, Inventario $inventario)
    {
        $datos = $request->safe()->except('imagen');
        if ($request->hasFile('imagen')) {
            if ($inventario->imagen) {
                Storage::disk('public')->delete($inventario->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('inventario', 'public');
        }
        $inventario->update($datos);
        BitacoraService::registrar('inventario.actualizado', $inventario);

        return redirect()->route('admin.inventario.show', $inventario)->with('success', 'Registro actualizado.');
    }

    public function destroy(Inventario $inventario)
    {
        if ($inventario->imagen) {
            Storage::disk('public')->delete($inventario->imagen);
        }
        BitacoraService::registrar('inventario.eliminado', $inventario);
        $inventario->delete();

        return redirect()->route('admin.inventario.index')->with('success', 'Registro eliminado.');
    }

    private function datosFormulario(Inventario $inventario): array
    {
        return [
            'inventario' => $inventario,
            'departamentos' => Catalogos::departamentos(),
            'categorias' => CategoriaInventario::opciones(),
            'categoriasEquipo' => CategoriaInventario::valoresEquipo(),
        ];
    }
}
