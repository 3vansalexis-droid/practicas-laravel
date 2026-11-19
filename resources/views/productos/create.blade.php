@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Registrar Nuevo Producto</h5>
                <a href="{{ route('productos.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('productos.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del producto</label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="{{ old('nombre') }}"
                            class="form-control @error('nombre') is-invalid @enderror"
                            required
                            autofocus
                        >
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea
                            id="descripcion"
                            name="descripcion"
                            rows="3"
                            class="form-control @error('descripcion') is-invalid @enderror"
                            required
                        >{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select
                                id="categoria_id"
                                name="categoria_id"
                                class="form-select @error('categoria_id') is-invalid @enderror"
                                required
                            >
                                <option value="">-- Selecciona una categoría --</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" @selected(old('categoria_id') == $categoria->id)>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="estado" class="form-label">Estado</label>
                            <select
                                id="estado"
                                name="estado"
                                class="form-select @error('estado') is-invalid @enderror"
                                required
                            >
                                <option value="Disponible" @selected(old('estado', 'Disponible') === 'Disponible')>Disponible</option>
                                <option value="Agotado" @selected(old('estado') === 'Agotado')>Agotado</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="precio" class="form-label">Precio ($)</label>
                            <input
                                type="number"
                                id="precio"
                                name="precio"
                                value="{{ old('precio') }}"
                                step="0.01"
                                min="0.01"
                                class="form-control @error('precio') is-invalid @enderror"
                                required
                            >
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="existencias" class="form-label">Existencias</label>
                            <input
                                type="number"
                                id="existencias"
                                name="existencias"
                                value="{{ old('existencias', 0) }}"
                                min="0"
                                class="form-control @error('existencias') is-invalid @enderror"
                                required
                            >
                            @error('existencias')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
