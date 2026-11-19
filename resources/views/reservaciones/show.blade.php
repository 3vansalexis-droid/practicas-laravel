@extends('layouts.app')

@section('title', 'Detalle de Reservación')

@section('content')
@php
    $badgeEstado = match ($reservacion->estado) {
        'Activa' => 'bg-success',
        'Cancelada' => 'bg-danger',
        default => 'bg-secondary',
    };
@endphp

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Detalle de Reservación #{{ $reservacion->id }}</h5>
                <a href="{{ route('reservaciones.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Estado</dt>
                    <dd class="col-sm-8">
                        <span class="badge {{ $badgeEstado }}">{{ $reservacion->estado }}</span>
                    </dd>

                    <dt class="col-sm-4 text-muted">Usuario</dt>
                    <dd class="col-sm-8">
                        <strong>{{ $reservacion->user->name ?? 'N/A' }}</strong>
                        <span class="text-muted small">({{ $reservacion->user->email ?? '' }})</span>
                    </dd>

                    <dt class="col-sm-4 text-muted">Equipo asignado</dt>
                    <dd class="col-sm-8">
                        <strong>{{ $reservacion->equipo->codigo ?? 'N/A' }}</strong> — {{ $reservacion->equipo->nombre ?? '' }}
                    </dd>

                    <dt class="col-sm-4 text-muted">Ubicación</dt>
                    <dd class="col-sm-8">{{ $reservacion->equipo->ubicacion ?? 'N/A' }}</dd>

                    <dt class="col-sm-4 text-muted">Especificaciones</dt>
                    <dd class="col-sm-8">{{ $reservacion->equipo->especificaciones ?? 'Sin especificar' }}</dd>

                    <dt class="col-sm-4 text-muted">Fecha de reserva</dt>
                    <dd class="col-sm-8">{{ $reservacion->fecha_reserva?->format('d/m/Y') }}</dd>

                    <dt class="col-sm-4 text-muted">Horario</dt>
                    <dd class="col-sm-8">
                        {{ substr((string) $reservacion->hora_inicio, 0, 5) }} - {{ substr((string) $reservacion->hora_fin, 0, 5) }}
                    </dd>

                    <dt class="col-sm-4 text-muted">Notas</dt>
                    <dd class="col-sm-8">{{ $reservacion->notas ?: 'Sin notas adicionales' }}</dd>

                    <dt class="col-sm-4 text-muted">Registrada el</dt>
                    <dd class="col-sm-8 mb-0">{{ $reservacion->created_at?->format('d/m/Y H:i:s') }}</dd>
                </dl>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                <a href="{{ route('reservaciones.index') }}" class="btn btn-secondary">Volver a Reservaciones</a>

                @if ($reservacion->estado === 'Activa')
                    <form
                        action="{{ route('reservaciones.cancelar', $reservacion) }}"
                        method="POST"
                        class="m-0"
                        onsubmit="return confirm('¿Deseas cancelar esta reservación? El equipo quedará disponible nuevamente.');"
                    >
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger">Cancelar Reservación</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
