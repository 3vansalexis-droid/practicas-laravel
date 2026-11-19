@extends('layouts.app')

@section('title', 'Registrar Alumno')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Registrar Alumno</h5>
                <a href="{{ route('academico.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('academico.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="user_id" class="form-label">Vincular cuenta de alumno (opcional)</label>
                        <select
                            id="user_id"
                            name="user_id"
                            class="form-select @error('user_id') is-invalid @enderror"
                        >
                            <option value="">-- Sin cuenta vinculada --</option>
                            @foreach ($alumnos as $alumno)
                                <option value="{{ $alumno->id }}" @selected(old('user_id') == $alumno->id)>
                                    {{ $alumno->name }} ({{ $alumno->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nombre_alumno" class="form-label">Nombre del alumno</label>
                        <input
                            type="text"
                            id="nombre_alumno"
                            name="nombre_alumno"
                            value="{{ old('nombre_alumno') }}"
                            class="form-control @error('nombre_alumno') is-invalid @enderror"
                            required
                            autofocus
                        >
                        @error('nombre_alumno')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="faltas" class="form-label">Número de faltas</label>
                            <input
                                type="number"
                                id="faltas"
                                name="faltas"
                                value="{{ old('faltas', 0) }}"
                                min="0"
                                max="100"
                                class="form-control @error('faltas') is-invalid @enderror"
                                required
                            >
                            @error('faltas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="calificacion" class="form-label">Calificación (0 a 10)</label>
                            <input
                                type="number"
                                id="calificacion"
                                name="calificacion"
                                value="{{ old('calificacion') }}"
                                step="0.1"
                                min="0"
                                max="10"
                                class="form-control @error('calificacion') is-invalid @enderror"
                                required
                            >
                            @error('calificacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info small mb-4">
                        La <strong>equivalencia alfabética</strong> (NA, SA, DE, AU) se calcula automáticamente en el servidor al guardar el registro.
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('academico.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar Registro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
