<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RolUsuario;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsuarioRequest;
use App\Models\Usuario;
use App\Services\BitacoraService;
use App\Support\Catalogos;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = Usuario::with('departamento')
            ->when($request->search, fn ($q, $t) => $q->where(fn ($w) => $w->where('nombre_usuario', 'ILIKE', "%{$t}%")->orWhere('nombre_completo', 'ILIKE', "%{$t}%")))
            ->when($request->rol, fn ($q, $r) => $q->where('rol', $r))
            ->orderBy('nombre_usuario')
            ->paginate(25)->withQueryString();

        return view('admin.usuarios.index', ['usuarios' => $usuarios, 'roles' => RolUsuario::opciones()]);
    }

    public function create()
    {
        return view('admin.usuarios.form', $this->datosFormulario(new Usuario(['activo' => true, 'rol' => RolUsuario::Departamento])));
    }

    public function store(UsuarioRequest $request)
    {
        $usuario = Usuario::create($request->safe()->except('password_confirmation'));
        BitacoraService::registrar('usuario.creado', $usuario);

        return redirect()->route('admin.usuarios-app.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(Usuario $usuario)
    {
        return redirect()->route('admin.usuarios-app.edit', $usuario);
    }

    public function edit(Usuario $usuario)
    {
        return view('admin.usuarios.form', $this->datosFormulario($usuario));
    }

    public function update(UsuarioRequest $request, Usuario $usuario)
    {
        $datos = $request->safe()->except(['password', 'password_confirmation']);
        if ($request->filled('password')) {
            $datos['password'] = $request->validated('password');
            // Al cambiar la contrasena se revocan los tokens de la app
            $usuario->tokens()->delete();
        }
        if ($usuario->is($request->user()) && ($datos['rol'] ?? null) !== RolUsuario::Admin->value) {
            return back()->withErrors(['rol' => 'No puedes quitarte tu propio rol de administrador.']);
        }
        $usuario->update($datos);
        if (! $usuario->activo) {
            $usuario->tokens()->delete();
        }
        BitacoraService::registrar('usuario.actualizado', $usuario);

        return redirect()->route('admin.usuarios-app.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(Request $request, Usuario $usuario)
    {
        abort_if($usuario->is($request->user()), 422, 'No puedes eliminar tu propia cuenta.');
        $usuario->tokens()->delete();
        $usuario->update(['activo' => false]);
        BitacoraService::registrar('usuario.desactivado', $usuario);

        return redirect()->route('admin.usuarios-app.index')->with('success', 'Usuario desactivado (se conserva para auditoria).');
    }

    private function datosFormulario(Usuario $usuario): array
    {
        return ['usuario' => $usuario, 'departamentos' => Catalogos::departamentos(), 'roles' => RolUsuario::opciones()];
    }
}
