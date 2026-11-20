@extends('layouts.app')

@section('title', 'Juego del Gato (Lobby)')

@section('content')
@php
    $esAdmin = auth()->user()->role && auth()->user()->role->nombre === 'administrador';
    $esJugador = auth()->user()->role && auth()->user()->role->nombre === 'jugador';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="mb-0">Juego del Gato (Tic-Tac-Toe)</h3>
        <p class="text-muted small mb-0">
            Práctica 1.6 — Partidas multijugador con persistencia en BD, concurrencia segura y actualización por Polling
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        @if ($esJugador)
            <form action="{{ route('gato.store') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-primary">
                    Crear Nueva Partida
                </button>
            </form>
        @endif

        <a href="{{ route('gato.historial') }}" class="btn btn-outline-secondary">
            Historial de Partidas
        </a>

        @if ($esAdmin)
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-dark">
                Gestionar Usuarios
            </a>
        @endif
    </div>
</div>

{{-- Jugadores disponibles --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white fw-semibold">
        Jugadores disponibles ({{ $jugadores->count() }})
    </div>
    <div class="card-body">
        @if ($jugadores->isEmpty())
            <p class="text-muted small mb-0">No hay jugadores registrados.</p>
        @else
            <div class="row g-2">
                @foreach ($jugadores as $jugador)
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="border rounded p-2 d-flex align-items-center justify-content-between bg-light">
                            <div>
                                <div class="fw-semibold small">
                                    {{ $jugador->name }}
                                    @if ($jugador->id === auth()->id())
                                        <span class="badge bg-primary ms-1">Tú</span>
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $jugador->email }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@if ($esJugador)
    {{-- Mis Partidas Activas / En Espera --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-dark text-white fw-semibold">
            Mis Partidas Activas / En Espera
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Jugador X</th>
                            <th>Jugador O</th>
                            <th>Estado</th>
                            <th>Turno</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($misPartidasActivas as $partida)
                            <tr>
                                <td>#{{ $partida->id }}</td>
                                <td class="fw-semibold">{{ $partida->jugadorX->name ?? 'N/A' }}</td>
                                <td>{{ $partida->jugadorO->name ?? 'Esperando oponente...' }}</td>
                                <td>
                                    <span class="badge {{ $partida->estado === 'en_curso' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $partida->estado === 'en_curso' ? 'En curso' : 'Esperando' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($partida->estado === 'en_curso')
                                        <span class="badge bg-primary">Turno {{ $partida->turno_actual }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('gato.show', $partida) }}" class="btn btn-sm btn-primary">
                                        Entrar al tablero
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No tienes partidas activas ni en espera. ¡Crea una nueva o únete a una disponible!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Partidas Disponibles para Unirse --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white fw-semibold">
            Partidas Disponibles para Unirse
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Creada por (Jugador X)</th>
                            <th>Creada el</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($partidasDisponibles as $partida)
                            <tr>
                                <td>#{{ $partida->id }}</td>
                                <td class="fw-semibold">{{ $partida->jugadorX->name ?? 'N/A' }}</td>
                                <td class="small text-muted">{{ $partida->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <form action="{{ route('gato.unirse', $partida) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Unirse como O
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No hay partidas abiertas de otros jugadores en este momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@if ($esAdmin)
    {{-- Todas las Partidas en Curso / En Espera (Admin) --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white fw-semibold">
            Todas las Partidas en Curso / En Espera
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Jugador X</th>
                            <th>Jugador O</th>
                            <th>Estado</th>
                            <th>Turno</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($partidasActivas as $partida)
                            <tr>
                                <td>#{{ $partida->id }}</td>
                                <td class="fw-semibold">{{ $partida->jugadorX->name ?? 'N/A' }}</td>
                                <td>{{ $partida->jugadorO->name ?? 'Esperando oponente...' }}</td>
                                <td>
                                    <span class="badge {{ $partida->estado === 'en_curso' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $partida->estado === 'en_curso' ? 'En curso' : 'Esperando' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($partida->estado === 'en_curso')
                                        <span class="badge bg-primary">Turno {{ $partida->turno_actual }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('gato.show', $partida) }}" class="btn btn-sm btn-outline-info">
                                            Ver tablero
                                        </a>

                                        <form
                                            action="{{ route('gato.cancelar', $partida) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Deseas cancelar la partida #{{ $partida->id }}?');"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Cancelar partida
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No hay partidas activas ni en espera en este momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
