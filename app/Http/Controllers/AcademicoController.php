<?php

namespace App\Http\Controllers;

use App\Models\RegistroAcademico;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicoController extends Controller
{
    public function index(Request $request): View
    {
        $columnasPermitidas = ['nombre_alumno', 'calificacion', 'faltas'];

        $ordenarPor = $request->get('ordenar_por', 'nombre_alumno');
        $direccion = $request->get('direccion', 'asc');

        if (! in_array($ordenarPor, $columnasPermitidas, true)) {
            $ordenarPor = 'nombre_alumno';
        }

        if (! in_array($direccion, ['asc', 'desc'], true)) {
            $direccion = 'asc';
        }

        $query = RegistroAcademico::query();

        if (auth()->user()->role && auth()->user()->role->nombre === 'alumno') {
            $query->where('user_id', auth()->id());
        }

        $registros = $query->orderBy($ordenarPor, $direccion)->get();

        return view('academico.index', compact('registros', 'ordenarPor', 'direccion'));
    }

    public function create(): View
    {
        $alumnos = User::whereHas('role', function ($query) {
            $query->where('nombre', 'alumno');
        })->orderBy('name')->get();

        return view('academico.create', compact('alumnos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'nombre_alumno' => ['required', 'string', 'max:255'],
            'faltas' => ['required', 'integer', 'min:0', 'max:100'],
            'calificacion' => ['required', 'numeric', 'min:0', 'max:10'],
        ], [
            'user_id.exists' => 'La cuenta de alumno seleccionada no es válida.',
            'nombre_alumno.required' => 'El nombre del alumno es obligatorio.',
            'faltas.required' => 'El número de faltas es obligatorio.',
            'faltas.integer' => 'Las faltas deben ser un número entero.',
            'faltas.min' => 'Las faltas no pueden ser menores a 0.',
            'faltas.max' => 'Las faltas no pueden superar 100.',
            'calificacion.required' => 'La calificación es obligatoria.',
            'calificacion.numeric' => 'La calificación debe ser un valor numérico.',
            'calificacion.min' => 'La calificación mínima es 0.',
            'calificacion.max' => 'La calificación máxima es 10.',
        ]);

        $datos['equivalencia'] = RegistroAcademico::calcularEquivalencia((float) $datos['calificacion']);

        RegistroAcademico::create($datos);

        return redirect()->route('academico.index')
            ->with('success', 'Registro académico guardado correctamente.');
    }

    public function edit(RegistroAcademico $registro): View
    {
        $alumnos = User::whereHas('role', function ($query) {
            $query->where('nombre', 'alumno');
        })->orderBy('name')->get();

        return view('academico.edit', compact('registro', 'alumnos'));
    }

    public function update(Request $request, RegistroAcademico $registro): RedirectResponse
    {
        $datos = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'nombre_alumno' => ['required', 'string', 'max:255'],
            'faltas' => ['required', 'integer', 'min:0', 'max:100'],
            'calificacion' => ['required', 'numeric', 'min:0', 'max:10'],
        ], [
            'user_id.exists' => 'La cuenta de alumno seleccionada no es válida.',
            'nombre_alumno.required' => 'El nombre del alumno es obligatorio.',
            'faltas.required' => 'El número de faltas es obligatorio.',
            'faltas.integer' => 'Las faltas deben ser un número entero.',
            'faltas.min' => 'Las faltas no pueden ser menores a 0.',
            'faltas.max' => 'Las faltas no pueden superar 100.',
            'calificacion.required' => 'La calificación es obligatoria.',
            'calificacion.numeric' => 'La calificación debe ser un valor numérico.',
            'calificacion.min' => 'La calificación mínima es 0.',
            'calificacion.max' => 'La calificación máxima es 10.',
        ]);

        $datos['equivalencia'] = RegistroAcademico::calcularEquivalencia((float) $datos['calificacion']);

        $registro->update($datos);

        return redirect()->route('academico.index')
            ->with('success', 'Registro académico actualizado correctamente.');
    }

    public function destroy(RegistroAcademico $registro): RedirectResponse
    {
        $registro->delete();

        return redirect()->route('academico.index')
            ->with('success', 'Registro académico eliminado correctamente.');
    }
}
