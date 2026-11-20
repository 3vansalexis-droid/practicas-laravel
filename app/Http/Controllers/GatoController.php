<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Partida;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GatoController extends Controller
{
    public function index(): View
    {
        $jugadores = User::whereHas('role', function ($query) {
            $query->where('nombre', 'jugador');
        })->orderBy('name')->get();

        $esAdmin = auth()->user()->role && auth()->user()->role->nombre === 'administrador';

        if ($esAdmin) {
            $partidasActivas = Partida::with(['jugadorX', 'jugadorO'])
                ->whereIn('estado', ['esperando', 'en_curso'])
                ->latest()
                ->get();

            return view('gato.index', [
                'jugadores' => $jugadores,
                'partidasActivas' => $partidasActivas,
                'misPartidasActivas' => collect(),
                'partidasDisponibles' => collect(),
            ]);
        }

        $misPartidasActivas = Partida::with(['jugadorX', 'jugadorO'])
            ->whereIn('estado', ['esperando', 'en_curso'])
            ->where(function ($query) {
                $query->where('jugador_x_id', auth()->id())
                    ->orWhere('jugador_o_id', auth()->id());
            })
            ->latest()
            ->get();

        $partidasDisponibles = Partida::with('jugadorX')
            ->where('estado', 'esperando')
            ->whereNull('jugador_o_id')
            ->where('jugador_x_id', '!=', auth()->id())
            ->latest()
            ->get();

        return view('gato.index', [
            'jugadores' => $jugadores,
            'partidasActivas' => collect(),
            'misPartidasActivas' => $misPartidasActivas,
            'partidasDisponibles' => $partidasDisponibles,
        ]);
    }

    public function store(): RedirectResponse
    {
        $partida = Partida::create([
            'jugador_x_id' => auth()->id(),
            'turno_actual' => 'X',
            'tablero' => array_fill(0, 9, null),
            'estado' => 'esperando',
            'es_empate' => false,
        ]);

        return redirect()->route('gato.show', $partida)
            ->with('success', 'Partida creada. Esperando a otro jugador...');
    }

    public function unirse(Partida $partida): RedirectResponse
    {
        $error = DB::transaction(function () use ($partida): ?string {
            $p = Partida::where('id', $partida->id)->lockForUpdate()->firstOrFail();

            if ($p->jugador_x_id === auth()->id()) {
                return 'No puedes unirte a tu propia partida como oponente.';
            }

            if ($p->estado !== 'esperando' || $p->jugador_o_id !== null) {
                return 'Esta partida ya no está disponible para unirse.';
            }

            $p->update([
                'jugador_o_id' => auth()->id(),
                'estado' => 'en_curso',
            ]);

            return null;
        });

        if ($error !== null) {
            return redirect()->route('gato.index')->with('error', $error);
        }

        return redirect()->route('gato.show', $partida)
            ->with('success', 'Te uniste a la partida como Jugador O. ¡Comienza el juego!');
    }

    public function show(Partida $partida): View
    {
        abort_unless(
            $this->puedeAccederPartida($partida),
            403,
            'No tienes permiso para acceder a esta partida.'
        );

        $partida->load(['jugadorX', 'jugadorO', 'ganador', 'movimientos.user']);

        return view('gato.show', compact('partida'));
    }

    public function estado(Partida $partida): JsonResponse
    {
        abort_unless(
            $this->puedeAccederPartida($partida),
            403,
            'No tienes permiso para acceder a esta partida.'
        );

        $partida->load(['jugadorX', 'jugadorO', 'ganador']);

        return response()->json($this->formatearEstado($partida));
    }

    public function mover(Request $request, Partida $partida): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'casilla' => ['required', 'integer', 'min:0', 'max:8'],
        ], [
            'casilla.required' => 'Debes seleccionar una casilla.',
            'casilla.integer' => 'La casilla debe ser un número entero.',
            'casilla.min' => 'La casilla no es válida.',
            'casilla.max' => 'La casilla no es válida.',
        ]);

        $casilla = (int) $datos['casilla'];

        $resultado = DB::transaction(function () use ($partida, $casilla): array {
            $p = Partida::where('id', $partida->id)->lockForUpdate()->firstOrFail();

            if (auth()->id() === $p->jugador_x_id) {
                $simbolo = 'X';
            } elseif (auth()->id() === $p->jugador_o_id) {
                $simbolo = 'O';
            } else {
                abort(403, 'No eres participante de esta partida.');
            }

            if ($p->estado !== 'en_curso') {
                return ['error' => 'La partida no está en curso.', 'status' => 422];
            }

            if ($p->turno_actual !== $simbolo) {
                return ['error' => 'No es tu turno.', 'status' => 422];
            }

            $tablero = $p->tablero ?? array_fill(0, 9, null);

            if ($tablero[$casilla] !== null) {
                return ['error' => 'Esa casilla ya está ocupada.', 'status' => 422];
            }

            $tablero[$casilla] = $simbolo;

            Movimiento::create([
                'partida_id' => $p->id,
                'user_id' => auth()->id(),
                'simbolo' => $simbolo,
                'casilla' => $casilla,
            ]);

            $p->tablero = $tablero;

            $simboloGanador = Partida::detectarGanador($tablero);

            if ($simboloGanador !== null) {
                $p->estado = 'finalizada';
                $p->ganador_id = $simboloGanador === 'X' ? $p->jugador_x_id : $p->jugador_o_id;
            } elseif (! in_array(null, $tablero, true)) {
                $p->estado = 'finalizada';
                $p->es_empate = true;
            } else {
                $p->turno_actual = $simbolo === 'X' ? 'O' : 'X';
            }

            $p->save();

            return ['partida' => $p];
        });

        if (isset($resultado['error'])) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $resultado['error']], $resultado['status']);
            }

            return back()->with('error', $resultado['error']);
        }

        $partidaActualizada = $resultado['partida']->load(['jugadorX', 'jugadorO', 'ganador']);

        if ($request->expectsJson()) {
            return response()->json($this->formatearEstado($partidaActualizada));
        }

        return redirect()->route('gato.show', $partidaActualizada);
    }

    public function abandonar(Partida $partida): RedirectResponse
    {
        $abandonada = DB::transaction(function () use ($partida): bool {
            $p = Partida::where('id', $partida->id)->lockForUpdate()->firstOrFail();

            $esParticipante = auth()->id() === $p->jugador_x_id || auth()->id() === $p->jugador_o_id;
            abort_unless($esParticipante, 403, 'No eres participante de esta partida.');

            if (! in_array($p->estado, ['esperando', 'en_curso'], true)) {
                return false;
            }

            $p->estado = 'abandonada';

            if ($p->jugador_o_id !== null) {
                $p->ganador_id = auth()->id() === $p->jugador_x_id
                    ? $p->jugador_o_id
                    : $p->jugador_x_id;
            }

            $p->save();

            return true;
        });

        if (! $abandonada) {
            return back()->with('error', 'Esta partida ya finalizó y no puede abandonarse.');
        }

        return redirect()->route('gato.index')
            ->with('success', 'Has abandonado la partida.');
    }

    public function cancelar(Partida $partida): RedirectResponse
    {
        $cancelada = DB::transaction(function () use ($partida): bool {
            $p = Partida::where('id', $partida->id)->lockForUpdate()->firstOrFail();

            if (! in_array($p->estado, ['esperando', 'en_curso'], true)) {
                return false;
            }

            $p->update(['estado' => 'cancelada']);

            return true;
        });

        if (! $cancelada) {
            return back()->with('error', 'Solo se pueden cancelar partidas en espera o en curso.');
        }

        return back()->with('success', 'Partida cancelada correctamente por el administrador.');
    }

    public function historial(): View
    {
        $esAdmin = auth()->user()->role && auth()->user()->role->nombre === 'administrador';

        if ($esAdmin) {
            $partidas = Partida::with(['jugadorX', 'jugadorO', 'ganador'])
                ->latest()
                ->get();
        } else {
            $partidas = Partida::with(['jugadorX', 'jugadorO', 'ganador'])
                ->where(function ($query) {
                    $query->where('jugador_x_id', auth()->id())
                        ->orWhere('jugador_o_id', auth()->id());
                })
                ->latest()
                ->get();
        }

        return view('gato.historial', compact('partidas'));
    }

    private function puedeAccederPartida(Partida $partida): bool
    {
        $esAdmin = auth()->user()->role && auth()->user()->role->nombre === 'administrador';

        return $esAdmin
            || auth()->id() === $partida->jugador_x_id
            || auth()->id() === $partida->jugador_o_id;
    }

    /**
     * @return array<string, mixed>
     */
    private function formatearEstado(Partida $partida): array
    {
        $esMiTurno = false;

        if ($partida->estado === 'en_curso') {
            $esMiTurno = ($partida->turno_actual === 'X' && auth()->id() === $partida->jugador_x_id)
                || ($partida->turno_actual === 'O' && auth()->id() === $partida->jugador_o_id);
        }

        return [
            'id' => $partida->id,
            'estado' => $partida->estado,
            'tablero' => $partida->tablero ?? array_fill(0, 9, null),
            'turno_actual' => $partida->turno_actual,
            'es_empate' => (bool) $partida->es_empate,
            'jugador_x' => $partida->jugadorX ? [
                'id' => $partida->jugadorX->id,
                'nombre' => $partida->jugadorX->name,
            ] : null,
            'jugador_o' => $partida->jugadorO ? [
                'id' => $partida->jugadorO->id,
                'nombre' => $partida->jugadorO->name,
            ] : null,
            'ganador' => $partida->ganador ? [
                'id' => $partida->ganador->id,
                'nombre' => $partida->ganador->name,
            ] : null,
            'es_mi_turno' => $esMiTurno,
        ];
    }
}
