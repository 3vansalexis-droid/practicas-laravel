<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Reservacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservacionController extends Controller
{
    public function index(): View
    {
        if (auth()->user()->role && auth()->user()->role->nombre === 'administrador') {
            $reservaciones = Reservacion::with(['user', 'equipo'])->latest()->get();
        } else {
            $reservaciones = Reservacion::with('equipo')
                ->where('user_id', auth()->id())
                ->latest()
                ->get();
        }

        return view('reservaciones.index', compact('reservaciones'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if(
            ! auth()->user()->role || auth()->user()->role->nombre !== 'estudiante',
            403,
            'Solo los estudiantes pueden realizar reservaciones.'
        );

        $datos = $request->validate([
            'equipo_id' => ['required', 'exists:equipos,id'],
            'fecha_reserva' => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'notas' => ['nullable', 'string', 'max:255'],
        ], [
            'equipo_id.required' => 'Debes seleccionar un equipo.',
            'equipo_id.exists' => 'El equipo seleccionado no existe.',
            'fecha_reserva.required' => 'La fecha de reservación es obligatoria.',
            'fecha_reserva.date' => 'Ingresa una fecha válida.',
            'fecha_reserva.after_or_equal' => 'La fecha de reservación no puede ser anterior a hoy.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'La hora de fin debe tener el formato HH:MM.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'notas.max' => 'Las notas no pueden superar los 255 caracteres.',
        ]);

        // Bloqueo pesimista dentro de transacción para evitar reservas simultáneas (race condition)
        $reservado = DB::transaction(function () use ($datos): bool {
            $equipo = Equipo::where('id', $datos['equipo_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $tieneReservaActiva = Reservacion::where('equipo_id', $equipo->id)
                ->where('estado', 'Activa')
                ->lockForUpdate()
                ->exists();

            if ($equipo->estado !== 'Disponible' || $tieneReservaActiva) {
                return false;
            }

            Reservacion::create([
                'user_id' => auth()->id(),
                'equipo_id' => $equipo->id,
                'fecha_reserva' => $datos['fecha_reserva'],
                'hora_inicio' => $datos['hora_inicio'],
                'hora_fin' => $datos['hora_fin'],
                'estado' => 'Activa',
                'notas' => $datos['notas'] ?? null,
            ]);

            $equipo->update(['estado' => 'Reservado']);

            return true;
        });

        if (! $reservado) {
            return back()->with('error', 'Este equipo ya no está disponible porque fue reservado por otro usuario.');
        }

        return redirect()->route('reservaciones.index')
            ->with('success', 'Equipo reservado correctamente.');
    }

    public function show(Reservacion $reservacion): View
    {
        $esAdmin = auth()->user()->role && auth()->user()->role->nombre === 'administrador';

        if (! $esAdmin && $reservacion->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver la reservación de otro usuario.');
        }

        $reservacion->load(['user', 'equipo']);

        return view('reservaciones.show', compact('reservacion'));
    }

    public function cancelar(Reservacion $reservacion): RedirectResponse
    {
        $esAdmin = auth()->user()->role && auth()->user()->role->nombre === 'administrador';

        if (! $esAdmin && $reservacion->user_id !== auth()->id()) {
            abort(403, 'No puedes cancelar la reservación de otro usuario.');
        }

        if ($reservacion->estado !== 'Activa') {
            return back()->with('error', 'Solo se pueden cancelar reservaciones activas.');
        }

        $cancelada = DB::transaction(function () use ($reservacion): bool {
            $reservaBloqueada = Reservacion::where('id', $reservacion->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($reservaBloqueada->estado !== 'Activa') {
                return false;
            }

            $equipo = Equipo::where('id', $reservaBloqueada->equipo_id)
                ->lockForUpdate()
                ->first();

            $reservaBloqueada->update(['estado' => 'Cancelada']);

            if ($equipo && $equipo->estado === 'Reservado') {
                $equipo->update(['estado' => 'Disponible']);
            }

            return true;
        });

        if (! $cancelada) {
            return back()->with('error', 'Solo se pueden cancelar reservaciones activas.');
        }

        return redirect()->route('reservaciones.index')
            ->with('success', 'Reservación cancelada y equipo liberado correctamente.');
    }
}
