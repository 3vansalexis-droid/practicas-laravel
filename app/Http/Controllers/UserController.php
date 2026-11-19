<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $usuarios = User::with('role')->latest()->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        $roles = Role::all();

        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role_id.required' => 'Debes seleccionar un rol.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
        ]);

        User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'role_id' => $datos['role_id'],
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $usuario): View
    {
        $usuario->load('role');

        return view('usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario): View
    {
        $roles = Role::all();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($usuario->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado por otro usuario.',
            'role_id.required' => 'Debes seleccionar un rol.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $camposActualizar = [
            'name' => $datos['name'],
            'email' => $datos['email'],
            'role_id' => $datos['role_id'],
        ];

        if (! empty($datos['password'])) {
            $camposActualizar['password'] = Hash::make($datos['password']);
        }

        $usuario->update($camposActualizar);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes deshabilitar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario deshabilitado correctamente. Puedes restaurarlo desde la papelera.');
    }

    public function trashed(): View
    {
        $usuarios = User::onlyTrashed()->with('role')->latest('deleted_at')->get();

        return view('usuarios.trashed', compact('usuarios'));
    }

    public function restore(string $id): RedirectResponse
    {
        $usuario = User::onlyTrashed()->findOrFail($id);
        $usuario->restore();

        return redirect()->route('usuarios.trashed')
            ->with('success', 'Usuario restaurado correctamente.');
    }

    public function forceDelete(string $id): RedirectResponse
    {
        $usuario = User::withTrashed()->findOrFail($id);

        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar permanentemente tu propia cuenta.');
        }

        $usuario->forceDelete();

        return back()
            ->with('success', 'Usuario eliminado definitivamente de la base de datos.');
    }
}
