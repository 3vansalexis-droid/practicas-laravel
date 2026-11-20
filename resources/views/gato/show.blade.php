@extends('layouts.app')

@section('title', 'Partida #' . $partida->id . ' - Juego del Gato')

@section('content')
@php
    $esAdmin = auth()->user()->role && auth()->user()->role->nombre === 'administrador';
    $esJugador = auth()->user()->role && auth()->user()->role->nombre === 'jugador';
    $miSimbolo = null;
    if (auth()->id() === $partida->jugador_x_id) {
        $miSimbolo = 'X';
    } elseif (auth()->id() === $partida->jugador_o_id) {
        $miSimbolo = 'O';
    }
    $partidaAbierta = in_array($partida->estado, ['esperando', 'en_curso'], true);
@endphp

<style>
    .tablero-gato {
        display: grid;
        grid-template-columns: repeat(3, 100px);
        gap: 10px;
        justify-content: center;
    }

    .casilla-gato {
        width: 100px;
        height: 100px;
        font-size: 2.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="mb-0">Partida #{{ $partida->id }}</h3>
        <p class="text-muted small mb-0">
            @if ($miSimbolo)
                Juegas con el símbolo <strong class="text-dark">{{ $miSimbolo }}</strong>
            @else
                Modo observador (Administrador)
            @endif
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('gato.index') }}" class="btn btn-outline-secondary btn-sm">Volver al Lobby</a>
        <a href="{{ route('gato.historial') }}" class="btn btn-outline-dark btn-sm">Historial</a>
    </div>
</div>

{{-- Marcador de Jugadores y Estado --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="small text-muted text-uppercase fw-semibold">Jugador X</div>
                <h5 class="mb-0 mt-1 text-primary" id="nombre-jugador-x">
                    {{ $partida->jugadorX->name ?? 'N/A' }}
                </h5>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <div class="small text-muted text-uppercase fw-semibold">Estado / Turno</div>
                <div class="mt-1">
                    <span id="badge-estado" class="badge bg-secondary fs-6">Cargando...</span>
                </div>
                <div id="texto-turno" class="small fw-semibold mt-1 text-muted"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="small text-muted text-uppercase fw-semibold">Jugador O</div>
                <h5 class="mb-0 mt-1 text-danger" id="nombre-jugador-o">
                    {{ $partida->jugadorO->name ?? 'Esperando oponente...' }}
                </h5>
            </div>
        </div>
    </div>
</div>

{{-- Alerta dinámica para errores o resultado final --}}
<div id="alerta-juego" class="alert d-none text-center fw-semibold" role="alert"></div>

{{-- Tablero 3x3 --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-5">
        <div class="tablero-gato" id="tablero-gato">
            @for ($i = 0; $i < 9; $i++)
                <button
                    type="button"
                    class="btn btn-outline-dark casilla-gato"
                    data-casilla="{{ $i }}"
                    disabled
                >
                    {{ $partida->tablero[$i] ?? '' }}
                </button>
            @endfor
        </div>

        {{-- Botones de acción (Abandonar / Cancelar) --}}
        <div class="text-center mt-4" id="acciones-partida">
            @if ($esJugador && $miSimbolo && $partidaAbierta)
                <form
                    action="{{ route('gato.abandonar', $partida) }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('¿Seguro que deseas abandonar la partida? Si ya hay oponente, se le otorgará la victoria.');"
                >
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        Abandonar partida
                    </button>
                </form>
            @endif

            @if ($esAdmin && $partidaAbierta)
                <form
                    action="{{ route('gato.cancelar', $partida) }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('¿Deseas cancelar esta partida como administrador?');"
                >
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger btn-sm">
                        Cancelar partida
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlEstado = @json(route('gato.estado', $partida));
        const urlMover = @json(route('gato.mover', $partida));
        const csrfToken = @json(csrf_token());

        const botones = Array.from(document.querySelectorAll('.casilla-gato'));
        const badgeEstado = document.getElementById('badge-estado');
        const textoTurno = document.getElementById('texto-turno');
        const nombreJugadorX = document.getElementById('nombre-jugador-x');
        const nombreJugadorO = document.getElementById('nombre-jugador-o');
        const alertaJuego = document.getElementById('alerta-juego');
        const accionesPartida = document.getElementById('acciones-partida');

        let enviandoMovimiento = false;
        let intervaloPolling = null;

        function mostrarAlerta(mensaje, tipo) {
            alertaJuego.textContent = mensaje;
            alertaJuego.className = 'alert alert-' + tipo + ' text-center fw-semibold';
        }

        function ocultarAlerta() {
            alertaJuego.className = 'alert d-none text-center fw-semibold';
            alertaJuego.textContent = '';
        }

        function renderizarEstado(data) {
            // Actualizar nombres de jugadores
            nombreJugadorX.textContent = data.jugador_x ? data.jugador_x.nombre : 'N/A';
            nombreJugadorO.textContent = data.jugador_o ? data.jugador_o.nombre : 'Esperando oponente...';

            // Actualizar tablero y habilitar/deshabilitar casillas según turno
            const tablero = Array.isArray(data.tablero) ? data.tablero : [];
            botones.forEach(function (btn, indice) {
                const valor = tablero[indice] || '';
                btn.textContent = valor;

                btn.classList.remove('btn-outline-dark', 'btn-outline-primary', 'btn-outline-danger', 'text-primary', 'text-danger');
                if (valor === 'X') {
                    btn.classList.add('btn-outline-primary', 'text-primary');
                } else if (valor === 'O') {
                    btn.classList.add('btn-outline-danger', 'text-danger');
                } else {
                    btn.classList.add('btn-outline-dark');
                }

                // Solo se habilita si la partida está en curso, es mi turno y la casilla está vacía
                btn.disabled = !(data.estado === 'en_curso' && data.es_mi_turno && valor === '' && !enviandoMovimiento);
            });

            // Actualizar indicadores de estado, turno y mensajes finales
            if (data.estado === 'esperando') {
                badgeEstado.textContent = 'Esperando oponente';
                badgeEstado.className = 'badge bg-warning text-dark fs-6';
                textoTurno.textContent = 'Comparte el Lobby para que otro jugador se una como O.';
                ocultarAlerta();
            } else if (data.estado === 'en_curso') {
                badgeEstado.textContent = 'En curso';
                badgeEstado.className = 'badge bg-success fs-6';
                if (data.es_mi_turno) {
                    textoTurno.textContent = '¡Es tu turno (' + data.turno_actual + ')!';
                    textoTurno.className = 'small fw-bold mt-1 text-success';
                } else {
                    textoTurno.textContent = 'Turno del jugador ' + data.turno_actual + ' (esperando...)';
                    textoTurno.className = 'small fw-semibold mt-1 text-muted';
                }
            } else {
                // Estados terminales: detener polling y ocultar botones de abandonar/cancelar
                if (intervaloPolling) {
                    clearInterval(intervaloPolling);
                    intervaloPolling = null;
                }
                if (accionesPartida) {
                    accionesPartida.classList.add('d-none');
                }

                if (data.estado === 'finalizada') {
                    badgeEstado.textContent = 'Finalizada';
                    badgeEstado.className = 'badge bg-dark fs-6';
                    textoTurno.textContent = '';

                    if (data.es_empate) {
                        mostrarAlerta('¡Empate! Ningún jugador logró completar una línea.', 'warning');
                    } else if (data.ganador) {
                        mostrarAlerta('¡Ganó ' + data.ganador.nombre + '!', 'success');
                    }
                } else if (data.estado === 'abandonada') {
                    badgeEstado.textContent = 'Abandonada';
                    badgeEstado.className = 'badge bg-danger fs-6';
                    textoTurno.textContent = '';

                    if (data.ganador) {
                        mostrarAlerta('Partida abandonada. Ganador por abandono: ' + data.ganador.nombre + '.', 'warning');
                    } else {
                        mostrarAlerta('La partida fue abandonada antes de iniciar.', 'secondary');
                    }
                } else if (data.estado === 'cancelada') {
                    badgeEstado.textContent = 'Cancelada';
                    badgeEstado.className = 'badge bg-secondary fs-6';
                    textoTurno.textContent = '';
                    mostrarAlerta('Esta partida fue cancelada por un administrador.', 'danger');
                }
            }
        }

        // Consulta periódica al servidor (Polling cada 2 segundos)
        async function actualizarEstado() {
            try {
                const respuesta = await fetch(urlEstado, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!respuesta.ok) {
                    return;
                }

                const data = await respuesta.json();
                renderizarEstado(data);
            } catch (error) {
                console.error('Error al consultar estado de la partida:', error);
            }
        }

        // Enviar movimiento al hacer clic en una casilla
        botones.forEach(function (btn) {
            btn.addEventListener('click', async function () {
                if (btn.disabled || enviandoMovimiento) {
                    return;
                }

                const casilla = parseInt(btn.dataset.casilla, 10);
                enviandoMovimiento = true;
                botones.forEach(b => b.disabled = true);
                ocultarAlerta();

                try {
                    const respuesta = await fetch(urlMover, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ casilla: casilla })
                    });

                    const data = await respuesta.json();

                    if (!respuesta.ok) {
                        enviandoMovimiento = false;
                        mostrarAlerta(data.message || 'No se pudo realizar el movimiento.', 'danger');
                        await actualizarEstado();
                    } else {
                        enviandoMovimiento = false;
                        renderizarEstado(data);
                    }
                } catch (error) {
                    enviandoMovimiento = false;
                    mostrarAlerta('Error de conexión al enviar tu movimiento.', 'danger');
                    await actualizarEstado();
                } finally {
                    enviandoMovimiento = false;
                }
            });
        });

        // Carga inicial y activación del polling cada 2 segundos
        actualizarEstado();
        intervaloPolling = setInterval(actualizarEstado, 2000);
    });
</script>
@endsection
