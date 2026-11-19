<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EquipoController extends Controller
{
    public function index(): View
    {
        if (auth()->user()->role && auth()->user()->role->nombre === 'administrador') {
            $equipos = Equipo::orderBy('codigo')->get();
        } else {
            $equipos = Equipo::where('estado', 'Disponible')
                ->orderBy('codigo')
                ->get();
        }

        return view('equipos.index', compact('equipos'));
    }

    public function create(): View
    {
        return view('equipos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:equipos,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'ubicacion' => ['required', 'string', 'max:255'],
            'especificaciones' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'in:Disponible,Reservado,Mantenimiento'],
        ], [
            'codigo.required' => 'El código del equipo es obligatorio.',
            'codigo.unique' => 'Ya existe un equipo registrado con ese código.',
            'codigo.max' => 'El código no puede exceder los 50 caracteres.',
            'nombre.required' => 'El nombre del equipo es obligatorio.',
            'ubicacion.required' => 'La ubicación es obligatoria.',
            'estado.required' => 'Debes seleccionar un estado.',
            'estado.in' => 'El estado debe ser Disponible, Reservado o Mantenimiento.',
        ]);

        Equipo::create($datos);

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo registrado correctamente.');
    }

    public function edit(Equipo $equipo): View
    {
        return view('equipos.edit', compact('equipo'));
    }

    public function update(Request $request, Equipo $equipo): RedirectResponse
    {
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:50', Rule::unique('equipos', 'codigo')->ignore($equipo->id)],
            'nombre' => ['required', 'string', 'max:255'],
            'ubicacion' => ['required', 'string', 'max:255'],
            'especificaciones' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'in:Disponible,Reservado,Mantenimiento'],
        ], [
            'codigo.required' => 'El código del equipo es obligatorio.',
            'codigo.unique' => 'Ya existe otro equipo registrado con ese código.',
            'codigo.max' => 'El código no puede exceder los 50 caracteres.',
            'nombre.required' => 'El nombre del equipo es obligatorio.',
            'ubicacion.required' => 'La ubicación es obligatoria.',
            'estado.required' => 'Debes seleccionar un estado.',
            'estado.in' => 'El estado debe ser Disponible, Reservado o Mantenimiento.',
        ]);

        $equipo->update($datos);

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Equipo $equipo): RedirectResponse
    {
        if ($equipo->reservaciones()->where('estado', 'Activa')->exists()) {
            return back()->with('error', 'No se puede eliminar este equipo porque tiene una reservación activa.');
        }

        $equipo->delete();

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo eliminado correctamente.');
    }
}
