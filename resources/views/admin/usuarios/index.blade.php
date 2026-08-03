@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Usuarios de la aplicación móvil</h1>
        <a href="{{ route('admin.usuarios-app.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus"></i> Nuevo usuario
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Departamento</th>
                            <th>Generar documentos</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->nombre_usuario }}</td>
                            <td>{{ ucfirst($user->departamento->nombre ?? 'N/A') }}</td>
                            <td>{!! $user->puede_generar_documentos ? '<i class="fas fa-check-circle text-success"></i> Sí' : '<i class="fas fa-times-circle text-danger"></i> No' !!}</td>
                            <td>{!! $user->activo ? '<span class="badge bg-success rounded-pill">Activo</span>' : '<span class="badge bg-danger rounded-pill">Inactivo</span>' !!}</td>
                            <td>
                                <a href="{{ route('admin.usuarios-app.show', $user) }}" class="btn btn-sm btn-outline-info rounded-circle"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.usuarios-app.edit', $user) }}" class="btn btn-sm btn-outline-warning rounded-circle"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.usuarios-app.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-circle"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4">No hay usuarios registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection