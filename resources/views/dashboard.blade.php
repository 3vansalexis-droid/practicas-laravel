@extends('layouts.app')

@section('title', 'Panel Principal')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <h3 class="mb-1">Bienvenido, {{ auth()->user()->name }}</h3>
        <p class="text-muted mb-0">
            Has iniciado sesión con el rol:
            <span class="badge bg-primary text-uppercase">{{ auth()->user()->role->nombre ?? 'Sin rol' }}</span>
        </p>
    </div>
</div>

<h5 class="mb-3">Módulos del Sistema</h5>

@php
    $rolActual = auth()->user()->role->nombre ?? '';
    $puedeVerUsuarios = in_array($rolActual, ['administrador', 'empleado'], true);
    $puedeVerAcademico = in_array($rolActual, ['profesor', 'alumno'], true);
    $puedeVerEquipos = in_array($rolActual, ['administrador', 'estudiante'], true);
    $puedeVerGato = in_array($rolActual, ['administrador', 'jugador'], true);
@endphp

<div class="row g-3">
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold">Práctica 1.1</span>
                    <span class="badge bg-success">Activo</span>
                </div>
                <h5 class="card-title">Autenticación y Roles</h5>
                <p class="card-text text-muted small flex-grow-1">
                    Acceso con verificación en 3 etapas (OTP) para Administrador y control de acceso por roles.
                </p>
            </div>
        </div>
    </div>

    @if ($puedeVerUsuarios)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">Práctica 1.2</span>
                        <span class="badge bg-success">Activo</span>
                    </div>
                    <h5 class="card-title">1.2 Usuarios</h5>
                    <p class="card-text text-muted small flex-grow-1">
                        CRUD de usuarios con control por roles, eliminación lógica (Soft Deletes), papelera, restauración y borrado permanente.
                    </p>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-primary btn-sm mt-2">
                        Ir a 1.2 Usuarios
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if ($puedeVerAcademico)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">Práctica 1.3</span>
                        <span class="badge bg-success">Activo</span>
                    </div>
                    <h5 class="card-title">1.3 Académico</h5>
                    <p class="card-text text-muted small flex-grow-1">
                        Sistema académico con ordenamiento dinámico, cálculo automático de equivalencias (NA, SA, DE, AU) y acceso por roles.
                    </p>
                    <a href="{{ route('academico.index') }}" class="btn btn-primary btn-sm mt-2">
                        Ir a 1.3 Académico
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if ($puedeVerUsuarios)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">Práctica 1.4</span>
                        <span class="badge bg-success">Activo</span>
                    </div>
                    <h5 class="card-title">1.4 Productos</h5>
                    <p class="card-text text-muted small flex-grow-1">
                        Gestión de productos con paginación dinámica (10, 15, 25, 50), filtros combinables, ordenamiento y Form Requests.
                    </p>
                    <a href="{{ route('productos.index') }}" class="btn btn-primary btn-sm mt-2">
                        Ir a 1.4 Productos
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if ($puedeVerEquipos)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">Práctica 1.5</span>
                        <span class="badge bg-success">Activo</span>
                    </div>
                    <h5 class="card-title">1.5 Equipos y Reservaciones</h5>
                    <p class="card-text text-muted small flex-grow-1">
                        Reservación de equipos de cómputo con privacidad estricta por usuario y bloqueo contra reservas simultáneas.
                    </p>
                    <div class="d-flex gap-2 mt-2">
                        <a href="{{ route('equipos.index') }}" class="btn btn-primary btn-sm flex-grow-1">
                            Equipos
                        </a>
                        <a href="{{ route('reservaciones.index') }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                            Reservaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($puedeVerGato)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">Práctica 1.6</span>
                        <span class="badge bg-success">Activo</span>
                    </div>
                    <h5 class="card-title">1.6 Juego del Gato</h5>
                    <p class="card-text text-muted small flex-grow-1">
                        Tic-Tac-Toe multijugador con persistencia de partidas y movimientos, bloqueos de concurrencia y actualización por Polling.
                    </p>
                    <div class="d-flex gap-2 mt-2">
                        <a href="{{ route('gato.index') }}" class="btn btn-primary btn-sm flex-grow-1">
                            Lobby del Gato
                        </a>
                        <a href="{{ route('gato.historial') }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                            Historial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
