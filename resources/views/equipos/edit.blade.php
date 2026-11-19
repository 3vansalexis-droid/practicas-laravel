@extends('layouts.app')

@section('title', 'Editar Equipo')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Editar Equipo {{ $equipo->codigo }}</h5>
                <a href="{{ route('equipos.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('equipos.update', $equipo) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código</label>
                        <input
                            type="text"
                            id="codigo"
                            name="codigo"
                            value="{{ old('codigo', $equipo->codigo) }}"
                            class="form-control @error('codigo') is-invalid @enderror"
                            required
                        >
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del equipo</label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="{{ old('nombre', $equipo->nombre) }}"
                            class="form-control @error('nombre') is-invalid @enderror"
                            required
                        >
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Ubicación</label>
                        <input
                            type="text"
                            id="ubicacion"
                            name="ubicacion"
                            value="{{ old('ubicacion', $equipo->ubicacion) }}"
                            class="form-control @error('ubicacion') is-invalid @enderror"
                            required
                        >
                        @error('ubicacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="especificaciones" class="form-label">Especificaciones (opcional)</label>
                        <input
                            type="text"
                            id="especificaciones"
                            name="especificaciones"
                            value="{{ old('especificaciones', $equipo->especificaciones) }}"
                            class="form-control @error('especificaciones') is-invalid @enderror"
                        >
                        @error('especificaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="estado" class="form-label">Estado</label>
                        <select
                            id="estado"
                            name="estado"
                            class="form-select @error('estado') is-invalid @enderror"
                            required
                        >
                            <option value="Disponible" @selected(old('estado', $equipo->estado) === 'Disponible')>Disponible</option>
                            <option value="Reservado" @selected(old('estado', $equipo->estado) === 'Reservado')>Reservado</option>
                            <option value="Mantenimiento" @selected(old('estado', $equipo->estado) === 'Mantenimiento')>Mantenimiento</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar Equipo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
