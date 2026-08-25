<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@footsy.com',
            ],
            [
                'name' => 'Admin',
                'apellido' => 'Footsy',
                'password' => 'FootsyAdmin123!',
                'telefono' => null,
                'fechaNacimiento' => null,
                'rolUsuario' => 'admin',
                'genero' => null,
                'estadoActivo' => true,
            ]
        );
    }
}
