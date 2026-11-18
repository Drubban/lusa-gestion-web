<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UsuarioDepartamento;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsuarioDepartamentoController extends Controller
{
    public function index()
    {
        $usuarios = UsuarioDepartamento::with('departamento')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function show($id)
    {
        $usuario = UsuarioDepartamento::with('departamento')->findOrFail($id);
        return view('admin.usuarios.show', compact('usuario'));
    }

    public function create()
    {
        $departamentos = Departamento::all(); // sistemas, diesel, taller, lavado
        return view('admin.usuarios.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string|max:50|unique:usuarios_departamento',
            'password' => 'required|string|min:6',
            'departamento_id' => 'required|exists:departamentos,id',
            'puede_generar_documentos' => 'sometimes|boolean',
            'activo' => 'sometimes|boolean',
        ]);

        UsuarioDepartamento::create([
            'nombre_usuario' => $request->nombre_usuario,
            'password' => Hash::make($request->password),
            'departamento_id' => $request->departamento_id,
            'puede_generar_documentos' => $request->has('puede_generar_documentos'),
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('admin.usuarios-app.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit($id)
    {
        $usuario = UsuarioDepartamento::findOrFail($id);
        $departamentos = Departamento::whereIn('nombre', ['sistemas', 'diesel', 'taller', 'lavado'])->get();
        return view('admin.usuarios.edit', compact('usuario', 'departamentos'));
    }

    public function update(Request $request, $id)
    {
        $usuario = UsuarioDepartamento::findOrFail($id);

        $request->validate([
            'nombre_usuario' => 'required|string|max:50|unique:usuarios_departamento,nombre_usuario,' . $id,
            'password' => 'nullable|string|min:6',
            'departamento_id' => 'required|exists:departamentos,id',
            'puede_generar_documentos' => 'sometimes|boolean',
            'activo' => 'sometimes|boolean',
        ]);

        $data = [
            'nombre_usuario' => $request->nombre_usuario,
            'departamento_id' => $request->departamento_id,
            'puede_generar_documentos' => $request->has('puede_generar_documentos'),
            'activo' => $request->has('activo'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios-app.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy($id)
    {
        $usuario = UsuarioDepartamento::findOrFail($id);
        $usuario->delete(); // O puedes hacer soft delete si lo prefieres
        return redirect()->route('admin.usuarios-app.index')->with('success', 'Usuario eliminado.');
    }
}