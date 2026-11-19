@extends('layouts.app')

@section('title', 'Verificación de Seguridad')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white text-center py-3">
                <h5 class="mb-0">Verificación en 2 pasos (OTP)</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small text-center mb-4">
                    Ingresa el código de 6 dígitos que fue enviado a tu correo electrónico. El código es válido por 5 minutos.
                </p>

                <form action="{{ route('otp.verify') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-4">
                        <label for="codigo" class="form-label">Código de verificación</label>
                        <input
                            type="text"
                            id="codigo"
                            name="codigo"
                            value="{{ old('codigo') }}"
                            maxlength="6"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            class="form-control form-control-lg text-center fw-bold @error('codigo') is-invalid @enderror"
                            placeholder="000000"
                            required
                            autofocus
                        >
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            Verificar y acceder
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none small">
                            Volver al inicio de sesión
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
