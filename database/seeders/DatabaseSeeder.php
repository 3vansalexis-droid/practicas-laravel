<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $nombresRoles = [
            'administrador',
            'empleado',
            'profesor',
            'alumno',
            'estudiante',
            'jugador',
        ];

        $roles = [];
        foreach ($nombresRoles as $nombre) {
            $roles[$nombre] = Role::firstOrCreate(['nombre' => $nombre]);
        }

        $usuarios = [
            [
                'name' => 'Administrador Principal',
                'email' => 'admin@test.com',
                'role' => 'administrador',
            ],
            [
                'name' => 'Empleado de Prueba',
                'email' => 'empleado@test.com',
                'role' => 'empleado',
            ],
            [
                'name' => 'Profesor de Prueba',
                'email' => 'profesor@test.com',
                'role' => 'profesor',
            ],
            [
                'name' => 'Alumno de Prueba',
                'email' => 'alumno@test.com',
                'role' => 'alumno',
            ],
            [
                'name' => 'Estudiante de Prueba',
                'email' => 'estudiante@test.com',
                'role' => 'estudiante',
            ],
            [
                'name' => 'Jugador Uno',
                'email' => 'jugador1@test.com',
                'role' => 'jugador',
            ],
            [
                'name' => 'Jugador Dos',
                'email' => 'jugador2@test.com',
                'role' => 'jugador',
            ],
        ];

        foreach ($usuarios as $datos) {
            User::create([
                'name' => $datos['name'],
                'email' => $datos['email'],
                'password' => Hash::make('password123'),
                'role_id' => $roles[$datos['role']]->id,
            ]);
        }

        $this->call([
            AcademicoSeeder::class,
            ProductoSeeder::class,
        ]);
    }
}
