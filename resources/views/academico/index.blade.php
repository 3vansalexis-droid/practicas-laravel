@extends('layouts.app')

@section('title', 'Sistema Académico')

@section('content')
@php
    $esProfesor = auth()->user()->role && auth()->user()->role->nombre === 'profesor';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="mb-0">Sistema Académico</h3>
        <p class="text-muted small mb-0">
            {{ $esProfesor ? 'Práctica 1.3 — Gestión de calificaciones, faltas y equivalencias' : 'Mi historial académico' }}
        </p>
    </div>

    @if ($esProfesor)
        <a href="{{ route('academico.create') }}" class="btn btn-primary">
            Registrar Alumno
        </a>
    @endif
</div>

@if ($esProfesor)
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-3">
            <form action="{{ route('academico.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label for="ordenar_por" class="form-label small fw-semibold mb-1">Ordenar por</label>
                    <select id="ordenar_por" name="ordenar_por" class="form-select form-select-sm">
                        <option value="nombre_alumno" @selected($ordenarPor === 'nombre_alumno')>Nombre del alumno</option>
                        <option value="calificacion" @selected($ordenarPor === 'calificacion')>Calificación</option>
                        <option value="faltas" @selected($ordenarPor === 'faltas')>Número de faltas</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="direccion" class="form-label small fw-semibold mb-1">Dirección</label>
                    <select id="direccion" name="direccion" class="form-select form-select-sm">
                        <option value="asc" @selected($direccion === 'asc')>Ascendente (A-Z / Menor a mayor)</option>
                        <option value="desc" @selected($direccion === 'desc')>Descendente (Z-A / Mayor a menor)</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-dark btn-sm flex-grow-1">Aplicar orden</button>
                    <a href="{{ route('academico.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                </div>
            </form>
        </div>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre del alumno</th>
                        <th class="text-center">Número de faltas</th>
                        <th class="text-center">Calificación numérica</th>
                        <th class="text-center">Equivalencia alfabética</th>
                        @if ($esProfesor)
                            <th class="text-end">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registros as $registro)
                        <tr>
                            <td class="fw-semibold">{{ $registro->nombre_alumno }}</td>
                            <td class="text-center">{{ $registro->faltas }}</td>
                            <td class="text-center fw-bold">{{ number_format((float) $registro->calificacion, 2) }}</td>
                            <td class="text-center">
                                <x-equivalencia-badge :equivalencia="$registro->equivalencia" />
                            </td>
                            @if ($esProfesor)
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('academico.edit', $registro) }}" class="btn btn-sm btn-outline-warning">
                                            Editar
                                        </a>

                                        <form
                                            action="{{ route('academico.destroy', $registro) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Deseas eliminar el registro académico de {{ $registro->nombre_alumno }}?');"
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
                            <td colspan="{{ $esProfesor ? 5 : 4 }}" class="text-center text-muted py-4">
                                No se encontraron registros académicos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
