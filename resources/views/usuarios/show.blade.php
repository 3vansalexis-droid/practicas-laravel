@extends('layouts.app')

@section('title', 'Detalle de Usuario')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Detalle del Usuario</h5>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">ID</dt>
                    <dd class="col-sm-8">{{ $usuario->id }}</dd>

                    <dt class="col-sm-4 text-muted">Nombre</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $usuario->name }}</dd>

                    <dt class="col-sm-4 text-muted">Correo electrónico</dt>
                    <dd class="col-sm-8">{{ $usuario->email }}</dd>

                    <dt class="col-sm-4 text-muted">Rol</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-primary text-uppercase">
                            {{ $usuario->role->nombre ?? 'Sin rol' }}
                        </span>
                    </dd>

                    <dt class="col-sm-4 text-muted">Fecha de creación</dt>
                    <dd class="col-sm-8">{{ $usuario->created_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</dd>

                    <dt class="col-sm-4 text-muted">Última actualización</dt>
                    <dd class="col-sm-8 mb-0">{{ $usuario->updated_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</dd>
                </dl>
            </div>
            <div class="card-footer bg-white d-flex justify-content-end gap-2 py-3">
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Volver al listado</a>
                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-warning">Editar Usuario</a>
            </div>
        </div>
    </div>
</div>
@endsection
