<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioAdminSeeder extends Seeder
{
    public function run(): void
    {
        $rolAdmin = Rol::where('nombre_Rol', 'Administrador')->firstOrFail();

        User::firstOrCreate(
            ['correo' => 'yimyt771@gmail.com'],
            [
                'nombreCompleto' => 'Administrador FICCT',
                'telefono'       => null,
                'correo'         => 'yimyt771@gmail.com',
                'password'       => Hash::make('tarqui231A'),
                'idRol'          => $rolAdmin->idRol,
            ]
        );
    }
}
