@extends('layouts.app')

@section('title', 'Papelera de Usuarios')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="mb-0">Papelera de Usuarios (Deshabilitados)</h3>
        <p class="text-muted small mb-0">Registros eliminados lógicamente (Soft Deletes)</p>
    </div>

    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-primary">
        Volver a Usuarios Activos
    </a>
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
                        <th>Deshabilitado el</th>
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
                            <td>{{ $usuario->deleted_at?->format('d/m/Y H:i:s') }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                    <form
                                        action="{{ route('usuarios.restore', $usuario->id) }}"
                                        method="POST"
                                        class="d-inline"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Restaurar
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
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Eliminar Permanente
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay usuarios deshabilitados en la papelera.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
