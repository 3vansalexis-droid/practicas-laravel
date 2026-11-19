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

<div class="row g-3">
    @php
        $modulos = [
            ['numero' => 'Práctica 1', 'titulo' => 'Autenticación y Roles', 'descripcion' => 'Acceso con verificación en 3 etapas para Administrador y control de acceso por roles.', 'estado' => 'Activo'],
            ['numero' => 'Práctica 2', 'titulo' => 'Módulo 2', 'descripcion' => 'Espacio reservado para la siguiente práctica académica.', 'estado' => 'Próximamente'],
            ['numero' => 'Práctica 3', 'titulo' => 'Módulo 3', 'descripcion' => 'Espacio reservado para la siguiente práctica académica.', 'estado' => 'Próximamente'],
            ['numero' => 'Práctica 4', 'titulo' => 'Módulo 4', 'descripcion' => 'Espacio reservado para la siguiente práctica académica.', 'estado' => 'Próximamente'],
            ['numero' => 'Práctica 5', 'titulo' => 'Módulo 5', 'descripcion' => 'Espacio reservado para la siguiente práctica académica.', 'estado' => 'Próximamente'],
            ['numero' => 'Práctica 6', 'titulo' => 'Módulo 6', 'descripcion' => 'Espacio reservado para la siguiente práctica académica.', 'estado' => 'Próximamente'],
        ];
    @endphp

    @foreach ($modulos as $modulo)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">{{ $modulo['numero'] }}</span>
                        <span class="badge {{ $modulo['estado'] === 'Activo' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $modulo['estado'] }}
                        </span>
                    </div>
                    <h5 class="card-title">{{ $modulo['titulo'] }}</h5>
                    <p class="card-text text-muted small flex-grow-1">{{ $modulo['descripcion'] }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
