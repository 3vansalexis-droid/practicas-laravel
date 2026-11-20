<?php

namespace App\Http\Controllers;

use App\Mail\CodigoVerificacionMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        $rolJugadorId = Role::where('nombre', 'jugador')->firstOrFail()->id;

        $user = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'role_id' => $rolJugadorId,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('gato.index')
            ->with('success', 'Registro completado. ¡Bienvenido al Juego del Gato!');
    }

    public function login(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $user = User::where('email', $credenciales['email'])->with('role')->first();

        if (! $user || ! Hash::check($credenciales['password'], $user->password)) {
            return back()
                ->withErrors(['email' => 'Las credenciales ingresadas no son correctas.'])
                ->onlyInput('email');
        }

        // Etapa 1 -> Etapa 2: si es administrador, enviamos código OTP y redirigimos a ruta firmada
        if ($user->role && $user->role->nombre === 'administrador') {
            $codigo = (string) random_int(100000, 999999);

            $user->update([
                'codigo_verificacion' => $codigo,
                'codigo_expira_en' => now()->addMinutes(5),
            ]);

            Mail::to($user->email)->send(new CodigoVerificacionMail($codigo, $user->name));

            $request->session()->put('otp_user_id', $user->id);

            $urlFirmada = URL::temporarySignedRoute('otp.form', now()->addMinutes(5));

            return redirect($urlFirmada)
                ->with('success', 'Se envió un código de verificación de 6 dígitos a tu correo.');
        }

        // Otros roles inician sesión directamente
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function showOtpForm(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('otp_user_id')) {
            return redirect()->route('login')
                ->with('error', 'No hay una verificación pendiente. Inicia sesión nuevamente.');
        }

        return view('auth.verificacion');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'codigo' => ['required', 'digits:6'],
        ], [
            'codigo.required' => 'Debes ingresar el código de verificación.',
            'codigo.digits' => 'El código debe contener exactamente 6 dígitos numéricos.',
        ]);

        $userId = $request->session()->get('otp_user_id');

        if (! $userId) {
            return redirect()->route('login')
                ->with('error', 'Tu sesión de verificación expiró. Inicia sesión nuevamente.');
        }

        $user = User::with('role')->find($userId);

        if (! $user) {
            $request->session()->forget('otp_user_id');

            return redirect()->route('login')
                ->with('error', 'Usuario no encontrado. Inicia sesión nuevamente.');
        }

        if (! $user->codigo_expira_en || ! now()->lessThanOrEqualTo($user->codigo_expira_en)) {
            return back()->withErrors([
                'codigo' => 'El código de verificación ha expirado. Vuelve a iniciar sesión para generar uno nuevo.',
            ]);
        }

        if ($user->codigo_verificacion !== $request->codigo) {
            return back()->withErrors([
                'codigo' => 'El código ingresado es incorrecto.',
            ]);
        }

        // Etapa 3: verificación exitosa, limpiamos OTP e iniciamos sesión
        $user->update([
            'codigo_verificacion' => null,
            'codigo_expira_en' => null,
        ]);

        $request->session()->forget('otp_user_id');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Verificación completada. Bienvenido al sistema.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Has cerrado sesión correctamente.');
    }
}
