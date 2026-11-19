@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
@php
    $esAdmin = auth()->user()->role && auth()->user()->role->nombre === 'administrador';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="mb-0">Gestión de Productos</h3>
        <p class="text-muted small mb-0">Práctica 1.4 — Catálogo con filtros combinables, ordenamiento y paginación dinámica</p>
    </div>

    @if ($esAdmin)
        <a href="{{ route('productos.create') }}" class="btn btn-primary">
            Nuevo Producto
        </a>
    @endif
</div>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <form action="{{ route('productos.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="buscar" class="form-label small fw-semibold mb-1">Buscar por nombre</label>
                <input
                    type="text"
                    id="buscar"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    class="form-control form-control-sm"
                    placeholder="Ej. Laptop, Monitor..."
                >
            </div>

            <div class="col-md-3">
                <label for="categoria_id" class="form-label small fw-semibold mb-1">Categoría</label>
                <select id="categoria_id" name="categoria_id" class="form-select form-select-sm">
                    <option value="">Todas las categorías</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" @selected(request('categoria_id') == $categoria->id)>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="precio_min" class="form-label small fw-semibold mb-1">Precio mínimo</label>
                <input
                    type="number"
                    id="precio_min"
                    name="precio_min"
                    value="{{ request('precio_min') }}"
                    step="0.01"
                    min="0"
                    class="form-control form-control-sm"
                    placeholder="0.00"
                >
            </div>

            <div class="col-md-2">
                <label for="precio_max" class="form-label small fw-semibold mb-1">Precio máximo</label>
                <input
                    type="number"
                    id="precio_max"
                    name="precio_max"
                    value="{{ request('precio_max') }}"
                    step="0.01"
                    min="0"
                    class="form-control form-control-sm"
                    placeholder="99999.00"
                >
            </div>

            <div class="col-md-1">
                <label for="por_pagina" class="form-label small fw-semibold mb-1">Mostrar</label>
                <select id="por_pagina" name="por_pagina" class="form-select form-select-sm">
                    @foreach ([10, 15, 25, 50] as $cantidad)
                        <option value="{{ $cantidad }}" @selected($porPagina === $cantidad)>{{ $cantidad }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="estado" class="form-label small fw-semibold mb-1">Estado</label>
                <select id="estado" name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="Disponible" @selected(request('estado') === 'Disponible')>Disponible</option>
                    <option value="Agotado" @selected(request('estado') === 'Agotado')>Agotado</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="ordenar_por" class="form-label small fw-semibold mb-1">Ordenar por</label>
                <select id="ordenar_por" name="ordenar_por" class="form-select form-select-sm">
                    <option value="created_at" @selected($ordenarPor === 'created_at')>Fecha de registro</option>
                    <option value="nombre" @selected($ordenarPor === 'nombre')>Nombre</option>
                    <option value="precio" @selected($ordenarPor === 'precio')>Precio</option>
                    <option value="existencias" @selected($ordenarPor === 'existencias')>Existencias</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="direccion" class="form-label small fw-semibold mb-1">Dirección</label>
                <select id="direccion" name="direccion" class="form-select form-select-sm">
                    <option value="desc" @selected($direccion === 'desc')>Descendente</option>
                    <option value="asc" @selected($direccion === 'asc')>Ascendente</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-dark btn-sm flex-grow-1">Aplicar filtros</button>
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar filtros</a>
            </div>
        </form>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center bg-white border rounded px-3 py-2 mb-3 small text-muted">
    <div>
        Total de registros encontrados: <strong class="text-dark">{{ $productos->total() }}</strong>
    </div>
    <div>
        Página <strong class="text-dark">{{ $productos->currentPage() }}</strong> de <strong class="text-dark">{{ $productos->lastPage() }}</strong>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th class="text-end">Precio</th>
                        <th class="text-center">Existencias</th>
                        <th class="text-center">Estado</th>
                        <th>Fecha de registro</th>
                        @if ($esAdmin)
                            <th class="text-end">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $producto)
                        <tr>
                            <td>{{ $producto->id }}</td>
                            <td class="fw-semibold">{{ $producto->nombre }}</td>
                            <td class="small text-muted">{{ \Illuminate\Support\Str::limit($producto->descripcion, 60) }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold text-nowrap">$ {{ number_format((float) $producto->precio, 2) }}</td>
                            <td class="text-center">{{ $producto->existencias }}</td>
                            <td class="text-center">
                                <x-estado-producto-badge :estado="$producto->estado" />
                            </td>
                            <td class="small text-nowrap">{{ $producto->created_at?->format('d/m/Y H:i') }}</td>
                            @if ($esAdmin)
                                <td class="text-end text-nowrap">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-outline-warning">
                                            Editar
                                        </a>

                                        <form
                                            action="{{ route('productos.destroy', $producto) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Deseas eliminar el producto {{ $producto->nombre }}?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $esAdmin ? 9 : 8 }}" class="text-center text-muted py-4">
                                No se encontraron productos con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $productos->links() }}
</div>
@endsection
