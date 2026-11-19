@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Editar Usuario #{{ $usuario->id }}</h5>
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('usuarios.update', $usuario) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre completo</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $usuario->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $usuario->email) }}"
                            class="form-control @error('email') is-invalid @enderror"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">Rol</label>
                        <select
                            id="role_id"
                            name="role_id"
                            class="form-select @error('role_id') is-invalid @enderror"
                            required
                        >
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}" @selected(old('role_id', $usuario->role_id) == $rol->id)>
                                    {{ ucfirst($rol->nombre) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">
                    <p class="text-muted small mb-3">
                        Deja los campos de contraseña vacíos si no deseas cambiar la contraseña actual.
                    </p>

                    <div class="mb-3">
                        <label for="password" class="form-label">Nueva contraseña (opcional)</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                        >
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
