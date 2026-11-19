@extends('layouts.app')

@section('title', 'Equipos de Cómputo')

@section('content')
@php
    $esAdmin = auth()->user()->role && auth()->user()->role->nombre === 'administrador';
    $esEstudiante = auth()->user()->role && auth()->user()->role->nombre === 'estudiante';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="mb-0">Equipos de Cómputo</h3>
        <p class="text-muted small mb-0">
            {{ $esAdmin ? 'Práctica 1.5 — Administración general de equipos de laboratorio' : 'Equipos disponibles para reservación' }}
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('reservaciones.index') }}" class="btn btn-outline-primary">
            {{ $esAdmin ? 'Todas las Reservaciones' : 'Mis Reservaciones' }}
        </a>

        @if ($esAdmin)
            <a href="{{ route('equipos.create') }}" class="btn btn-primary">
                Nuevo Equipo
            </a>
        @endif
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>No se pudo completar la reservación:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Ubicación</th>
                        <th>Especificaciones</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($equipos as $equipo)
                        @php
                            $badgeEstado = match ($equipo->estado) {
                                'Disponible' => 'bg-success',
                                'Reservado' => 'bg-warning text-dark',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td class="fw-bold text-nowrap">{{ $equipo->codigo }}</td>
                            <td class="fw-semibold">{{ $equipo->nombre }}</td>
                            <td>{{ $equipo->ubicacion }}</td>
                            <td class="small text-muted">{{ $equipo->especificaciones ?? 'Sin especificar' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $badgeEstado }}">{{ $equipo->estado }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                @if ($esAdmin)
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('equipos.edit', $equipo) }}" class="btn btn-sm btn-outline-warning">
                                            Editar
                                        </a>

                                        <form
                                            action="{{ route('equipos.destroy', $equipo) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Deseas eliminar el equipo {{ $equipo->codigo }}?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                @elseif ($esEstudiante && $equipo->estado === 'Disponible')
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalReservar{{ $equipo->id }}"
                                    >
                                        Reservar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                {{ $esAdmin ? 'No hay equipos registrados.' : 'No hay equipos disponibles en este momento.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($esEstudiante)
    @foreach ($equipos as $equipo)
        @if ($equipo->estado === 'Disponible')
            <div class="modal fade" id="modalReservar{{ $equipo->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('reservaciones.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="equipo_id" value="{{ $equipo->id }}">

                            <div class="modal-header bg-dark text-white">
                                <h5 class="modal-title">Reservar {{ $equipo->codigo }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>

                            <div class="modal-body">
                                <p class="small text-muted mb-3">
                                    <strong>{{ $equipo->nombre }}</strong> — {{ $equipo->ubicacion }}
                                </p>

                                <div class="mb-3">
                                    <label for="fecha_reserva_{{ $equipo->id }}" class="form-label">Fecha de reservación</label>
                                    <input
                                        type="date"
                                        id="fecha_reserva_{{ $equipo->id }}"
                                        name="fecha_reserva"
                                        value="{{ old('fecha_reserva', now()->toDateString()) }}"
                                        min="{{ now()->toDateString() }}"
                                        class="form-control"
                                        required
                                    >
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label for="hora_inicio_{{ $equipo->id }}" class="form-label">Hora de inicio</label>
                                        <input
                                            type="time"
                                            id="hora_inicio_{{ $equipo->id }}"
                                            name="hora_inicio"
                                            value="{{ old('hora_inicio', '09:00') }}"
                                            class="form-control"
                                            required
                                        >
                                    </div>
                                    <div class="col-6">
                                        <label for="hora_fin_{{ $equipo->id }}" class="form-label">Hora de fin</label>
                                        <input
                                            type="time"
                                            id="hora_fin_{{ $equipo->id }}"
                                            name="hora_fin"
                                            value="{{ old('hora_fin', '11:00') }}"
                                            class="form-control"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label for="notas_{{ $equipo->id }}" class="form-label">Notas (opcional)</label>
                                    <input
                                        type="text"
                                        id="notas_{{ $equipo->id }}"
                                        name="notas"
                                        value="{{ old('notas') }}"
                                        maxlength="255"
                                        class="form-control"
                                        placeholder="Ej. Práctica de redes o examen"
                                    >
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Confirmar Reservación</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif
@endsection
