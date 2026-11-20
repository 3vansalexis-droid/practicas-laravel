@extends('layouts.app')

@section('title', 'Historial de Partidas - Juego del Gato')

@section('content')
@php
    $esAdmin = auth()->user()->role && auth()->user()->role->nombre === 'administrador';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="mb-0">{{ $esAdmin ? 'Historial Global de Partidas' : 'Mi Historial de Partidas' }}</h3>
        <p class="text-muted small mb-0">Registro de partidas jugadas, en curso, abandonadas y canceladas</p>
    </div>

    <a href="{{ route('gato.index') }}" class="btn btn-outline-primary">
        Volver al Lobby
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 75px;">ID</th>
                        <th>Jugador X</th>
                        <th>Jugador O</th>
                        <th class="text-center">Estado</th>
                        <th>Resultado</th>
                        <th>Fecha</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($partidas as $partida)
                        @php
                            $badgeEstado = match ($partida->estado) {
                                'esperando' => ['clase' => 'bg-warning text-dark', 'texto' => 'Esperando'],
                                'en_curso' => ['clase' => 'bg-primary', 'texto' => 'En curso'],
                                'finalizada' => ['clase' => 'bg-success', 'texto' => 'Finalizada'],
                                'abandonada' => ['clase' => 'bg-danger', 'texto' => 'Abandonada'],
                                'cancelada' => ['clase' => 'bg-secondary', 'texto' => 'Cancelada'],
                                default => ['clase' => 'bg-secondary', 'texto' => ucfirst($partida->estado)],
                            };
                        @endphp
                        <tr>
                            <td>#{{ $partida->id }}</td>
                            <td class="fw-semibold">{{ $partida->jugadorX->name ?? 'N/A' }}</td>
                            <td>{{ $partida->jugadorO->name ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $badgeEstado['clase'] }}">
                                    {{ $badgeEstado['texto'] }}
                                </span>
                            </td>
                            <td>
                                @if ($partida->es_empate)
                                    <span class="badge bg-warning text-dark">Empate</span>
                                @elseif ($partida->ganador)
                                    <span class="fw-semibold text-success">Ganó {{ $partida->ganador->name }}</span>
                                @else
                                    <span class="text-muted small">Sin ganador</span>
                                @endif
                            </td>
                            <td class="small text-nowrap">{{ $partida->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('gato.show', $partida) }}" class="btn btn-sm btn-outline-info">
                                    Ver tablero
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Todavía no hay partidas registradas en el historial.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
