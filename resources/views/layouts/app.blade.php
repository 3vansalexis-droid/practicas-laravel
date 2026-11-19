<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema Modular de Prácticas')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100 d-flex flex-column">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
                Sistema de Prácticas
            </a>

            @auth
                <ul class="navbar-nav me-auto ms-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            Inicio
                        </a>
                    </li>
                    @if (in_array(auth()->user()->role->nombre ?? '', ['administrador', 'empleado'], true))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                1.2 Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" href="{{ route('productos.index') }}">
                                1.4 Productos
                            </a>
                        </li>
                    @endif
                    @if (in_array(auth()->user()->role->nombre ?? '', ['profesor', 'alumno'], true))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('academico.*') ? 'active' : '' }}" href="{{ route('academico.index') }}">
                                1.3 Académico
                            </a>
                        </li>
                    @endif
                    @if (in_array(auth()->user()->role->nombre ?? '', ['administrador', 'estudiante'], true))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('equipos.*') ? 'active' : '' }}" href="{{ route('equipos.index') }}">
                                1.5 Equipos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reservaciones.*') ? 'active' : '' }}" href="{{ route('reservaciones.index') }}">
                                1.5 Reservaciones
                            </a>
                        </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center gap-3 ms-auto">
                    <div class="text-white d-flex align-items-center gap-2">
                        <span>{{ auth()->user()->name }}</span>
                        <span class="badge bg-primary text-uppercase">
                            {{ auth()->user()->role->nombre ?? 'Sin rol' }}
                        </span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
