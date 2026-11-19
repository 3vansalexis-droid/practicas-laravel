<?php

namespace Database\Seeders;

use App\Models\RegistroAcademico;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicoSeeder extends Seeder
{
    /**
     * Seed de registros académicos de ejemplo para la Práctica 1.3.
     */
    public function run(): void
    {
        $alumnoCuenta = User::where('email', 'alumno@test.com')->first();

        $registros = [
            [
                'user_id' => $alumnoCuenta?->id,
                'nombre_alumno' => $alumnoCuenta?->name ?? 'Alumno de Prueba',
                'faltas' => 1,
                'calificacion' => 9.6,
            ],
            [
                'user_id' => null,
                'nombre_alumno' => 'Carlos Mendoza Ríos',
                'faltas' => 5,
                'calificacion' => 7.4,
            ],
            [
                'user_id' => null,
                'nombre_alumno' => 'Ana Sofía Torres',
                'faltas' => 2,
                'calificacion' => 8.5,
            ],
            [
                'user_id' => null,
                'nombre_alumno' => 'Luis Fernando Gómez',
                'faltas' => 0,
                'calificacion' => 9.2,
            ],
            [
                'user_id' => null,
                'nombre_alumno' => 'Mariana López Vega',
                'faltas' => 3,
                'calificacion' => 9.8,
            ],
            [
                'user_id' => null,
                'nombre_alumno' => 'Diego Ramírez Cruz',
                'faltas' => 7,
                'calificacion' => 6.8,
            ],
        ];

        foreach ($registros as $datos) {
            RegistroAcademico::create([
                'user_id' => $datos['user_id'],
                'nombre_alumno' => $datos['nombre_alumno'],
                'faltas' => $datos['faltas'],
                'calificacion' => $datos['calificacion'],
                'equivalencia' => RegistroAcademico::calcularEquivalencia((float) $datos['calificacion']),
            ]);
        }
    }
}
