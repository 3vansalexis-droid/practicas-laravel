<?php

namespace Database\Seeders;

use App\Models\Equipo;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EquipoSeeder extends Seeder
{
    /**
     * Seed de segundo estudiante y 8 equipos de cómputo para la Práctica 1.5.
     */
    public function run(): void
    {
        $rolEstudiante = Role::firstOrCreate(['nombre' => 'estudiante']);

        User::firstOrCreate(
            ['email' => 'estudiante2@test.com'],
            [
                'name' => 'Estudiante Dos',
                'password' => Hash::make('password123'),
                'role_id' => $rolEstudiante->id,
            ]
        );

        $equipos = [
            [
                'codigo' => 'PC-01',
                'nombre' => 'Computadora Lab 1 - Estación 01',
                'ubicacion' => 'Laboratorio de Redes',
                'especificaciones' => 'Intel Core i5, 16 GB RAM, SSD 512 GB',
                'estado' => 'Disponible',
            ],
            [
                'codigo' => 'PC-02',
                'nombre' => 'Computadora Lab 1 - Estación 02',
                'ubicacion' => 'Laboratorio de Redes',
                'especificaciones' => 'Intel Core i5, 16 GB RAM, SSD 512 GB',
                'estado' => 'Disponible',
            ],
            [
                'codigo' => 'PC-03',
                'nombre' => 'Computadora Lab 1 - Estación 03',
                'ubicacion' => 'Laboratorio de Redes',
                'especificaciones' => 'Intel Core i7, 16 GB RAM, SSD 1 TB',
                'estado' => 'Disponible',
            ],
            [
                'codigo' => 'PC-04',
                'nombre' => 'Computadora Lab 2 - Estación 04',
                'ubicacion' => 'Laboratorio de Desarrollo Web',
                'especificaciones' => 'AMD Ryzen 5, 16 GB RAM, SSD 512 GB',
                'estado' => 'Disponible',
            ],
            [
                'codigo' => 'PC-05',
                'nombre' => 'Computadora Lab 2 - Estación 05',
                'ubicacion' => 'Laboratorio de Desarrollo Web',
                'especificaciones' => 'AMD Ryzen 5, 16 GB RAM, SSD 512 GB',
                'estado' => 'Disponible',
            ],
            [
                'codigo' => 'PC-06',
                'nombre' => 'Computadora Lab 2 - Estación 06',
                'ubicacion' => 'Laboratorio de Desarrollo Web',
                'especificaciones' => 'Intel Core i7, 32 GB RAM, SSD 1 TB',
                'estado' => 'Disponible',
            ],
            [
                'codigo' => 'PC-07',
                'nombre' => 'Workstation Lab 3 - Estación 07',
                'ubicacion' => 'Laboratorio de Base de Datos',
                'especificaciones' => 'AMD Ryzen 7, 32 GB RAM, NVMe 1 TB',
                'estado' => 'Disponible',
            ],
            [
                'codigo' => 'PC-08',
                'nombre' => 'Workstation Lab 3 - Estación 08',
                'ubicacion' => 'Laboratorio de Base de Datos',
                'especificaciones' => 'Intel Core i5, 8 GB RAM, SSD 256 GB (en mantenimiento preventivo)',
                'estado' => 'Mantenimiento',
            ],
        ];

        foreach ($equipos as $datos) {
            Equipo::firstOrCreate(
                ['codigo' => $datos['codigo']],
                $datos
            );
        }
    }
}
