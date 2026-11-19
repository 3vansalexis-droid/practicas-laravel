@extends('layouts.app')

@section('title', 'Reservaciones')

@section('content')
@php
    $esAdmin = auth()->user()->role && auth()->user()->role->nombre === 'administrador';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="mb-0">{{ $esAdmin ? 'Todas las Reservaciones' : 'Mis Reservaciones' }}</h3>
        <p class="text-muted small mb-0">
            {{ $esAdmin ? 'Vista global de reservaciones registradas en el sistema' : 'Solo puedes consultar y gestionar tus propias reservaciones' }}
        </p>
    </div>

    <a href="{{ route('equipos.index') }}" class="btn btn-outline-primary">
        Volver a Equipos
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 70px;">ID</th>
                        @if ($esAdmin)
                            <th>Usuario</th>
                        @endif
                        <th>Equipo</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservaciones as $reservacion)
                        @php
                            $badgeEstado = match ($reservacion->estado) {
                                'Activa' => 'bg-success',
                                'Cancelada' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td>#{{ $reservacion->id }}</td>
                            @if ($esAdmin)
                                <td>
                                    <div class="fw-semibold">{{ $reservacion->user->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">{{ $reservacion->user->email ?? '' }}</div>
                                </td>
                            @endif
                            <td>
                                <span class="fw-bold">{{ $reservacion->equipo->codigo ?? 'N/A' }}</span>
                                <span class="text-muted">— {{ $reservacion->equipo->nombre ?? '' }}</span>
                            </td>
                            <td class="text-nowrap">{{ $reservacion->fecha_reserva?->format('d/m/Y') }}</td>
                            <td class="text-nowrap">
                                {{ substr((string) $reservacion->hora_inicio, 0, 5) }} - {{ substr((string) $reservacion->hora_fin, 0, 5) }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badgeEstado }}">{{ $reservacion->estado }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('reservaciones.show', $reservacion) }}" class="btn btn-sm btn-outline-info">
                                        Ver detalle
                                    </a>

                                    @if ($reservacion->estado === 'Activa')
                                        <form
                                            action="{{ route('reservaciones.cancelar', $reservacion) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('¿Deseas cancelar esta reservación? El equipo quedará disponible nuevamente.');"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Cancelar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $esAdmin ? 7 : 6 }}" class="text-center text-muted py-4">
                                No hay reservaciones registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
