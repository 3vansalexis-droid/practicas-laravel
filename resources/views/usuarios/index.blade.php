@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="mb-0">Gestión de Usuarios</h3>
        <p class="text-muted small mb-0">Práctica 1.2 — Administración de cuentas y roles</p>
    </div>

    <div class="d-flex gap-2">
        @if (auth()->user()->role && auth()->user()->role->nombre === 'administrador')
            <a href="{{ route('usuarios.trashed') }}" class="btn btn-outline-secondary">
                Ver Papelera (Deshabilitados)
            </a>
        @endif

        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            Nuevo Usuario
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td class="fw-semibold">{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                <span class="badge bg-secondary text-uppercase">
                                    {{ $usuario->role->nombre ?? 'Sin rol' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                    <a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-sm btn-outline-info">
                                        Ver
                                    </a>
                                    <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-warning">
                                        Editar
                                    </a>

                                    @if (auth()->user()->role && auth()->user()->role->nombre === 'administrador')
                                        <form
                                            action="{{ route('usuarios.destroy', $usuario) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Deseas deshabilitar a {{ $usuario->name }}? Podrás restaurarlo desde la papelera.');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                Deshabilitar (Soft Delete)
                                            </button>
                                        </form>

                                        <form
                                            action="{{ route('usuarios.forceDelete', $usuario->id) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('ATENCIÓN: ¿Deseas eliminar permanentemente a {{ $usuario->name }}? Esta acción NO se podrá deshacer.');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Eliminar Permanente
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No hay usuarios activos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
