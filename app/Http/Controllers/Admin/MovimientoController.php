<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TipoMovimiento;
use App\Http\Controllers\Controller;
use App\Http\Requests\MovimientoRequest;
use App\Models\Departamento;
use App\Models\MovimientoDepartamento;
use App\Models\Unidad;
use App\Services\BitacoraService;
use App\Services\MovimientoService;
use App\Support\Catalogos;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    public function __construct(private readonly MovimientoService $movimientos) {}

    public function index(Request $request)
    {
        $departamentoId = Departamento::decodeHashid($request->departamento);

        $movimientos = MovimientoDepartamento::with(['unidad', 'departamento', 'usuario', 'entrada'])
            ->when($request->tipo, fn ($q, $t) => $q->where('tipo', $t))
            ->when($request->fecha, fn ($q, $f) => $q->whereDate('fecha_hora', $f))
            ->when($departamentoId, fn ($q, $d) => $q->where('departamento_id', $d))
            ->when($request->unidad, fn ($q, $u) => $q->whereHas('unidad', fn ($w) => $w->where('numero_economico', 'ILIKE', "%{$u}%")))
            ->latest('fecha_hora')
            ->paginate(25)->withQueryString();

        // Unidades que estan dentro de algun departamento en este momento
        $dentro = MovimientoDepartamento::estanciasAbiertas()
            ->with(['unidad', 'departamento'])
            ->when($departamentoId, fn ($q, $d) => $q->where('departamento_id', $d))
            ->orderBy('fecha_hora')
            ->get();

        return view('admin.movimientos.index', [
            'movimientos' => $movimientos,
            'dentro' => $dentro,
            'departamentos' => Catalogos::departamentos(),
        ]);
    }

    public function create()
    {
        return view('admin.movimientos.form', [
            'movimiento' => new MovimientoDepartamento(['fecha_hora' => now(), 'tipo' => TipoMovimiento::Entrada]),
            'unidades' => Catalogos::unidades(),
            'departamentos' => Catalogos::departamentos(),
        ]);
    }

    /** Registro manual desde el panel con las mismas reglas que la app. */
    public function store(MovimientoRequest $request)
    {
        $this->movimientos->registrar(
            Unidad::findOrFail($request->validated('unidad_id')),
            Departamento::findOrFail($request->validated('departamento_id')),
            TipoMovimiento::from($request->validated('tipo')),
            $request->user(),
            Carbon::parse($request->validated('fecha_hora')),
            $request->validated('observaciones'),
        );

        return redirect()->route('admin.movimientos.index')->with('success', 'Movimiento registrado.');
    }

    public function show(MovimientoDepartamento $movimiento)
    {
        $movimiento->load(['unidad', 'departamento', 'usuario', 'entrada', 'salida']);
        $operador = DB::table('asignacion_operador_unidad as a')
            ->join('operadores as o', 'o.id', '=', 'a.operador_id')
            ->where('a.unidad_id', $movimiento->unidad_id)
            ->whereDate('a.fecha_inicio', '<=', $movimiento->fecha_hora)
            ->where(fn ($q) => $q->whereNull('a.fecha_fin')->orWhereDate('a.fecha_fin', '>=', $movimiento->fecha_hora))
            ->orderByDesc('a.fecha_inicio')
            ->first(['o.nombre_completo', 'o.clave_operador']);

        return view('admin.movimientos.show', compact('movimiento', 'operador'));
    }

    public function edit(MovimientoDepartamento $movimiento)
    {
        return view('admin.movimientos.form', [
            'movimiento' => $movimiento,
            'unidades' => Catalogos::unidades(false),
            'departamentos' => Catalogos::departamentos(),
        ]);
    }

    /** Correccion administrativa: recalcula la duracion si es una salida. */
    public function update(MovimientoRequest $request, MovimientoDepartamento $movimiento)
    {
        $movimiento->fill($request->validated());
        if ($movimiento->tipo === TipoMovimiento::Salida && $movimiento->entrada) {
            $movimiento->duracion_minutos = max(0, (int) $movimiento->entrada->fecha_hora->diffInMinutes($movimiento->fecha_hora, false));
        }
        $movimiento->save();
        $movimiento->salida?->update([
            'duracion_minutos' => max(0, (int) $movimiento->fecha_hora->diffInMinutes($movimiento->salida->fecha_hora, false)),
        ]);
        BitacoraService::registrar('movimiento.corregido', $movimiento);

        return redirect()->route('admin.movimientos.show', $movimiento)->with('success', 'Movimiento actualizado.');
    }

    public function destroy(MovimientoDepartamento $movimiento)
    {
        if ($movimiento->salida()->exists()) {
            return back()->withErrors(['error' => 'Esta entrada ya tiene salida registrada; elimina primero la salida.']);
        }
        BitacoraService::registrar('movimiento.eliminado', $movimiento, ['tipo' => $movimiento->tipo->value]);
        $movimiento->delete();

        return redirect()->route('admin.movimientos.index')->with('success', 'Movimiento eliminado.');
    }
}
