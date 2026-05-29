<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre_Rol' => 'Administrador'],
            ['nombre_Rol' => 'Docente'],
            ['nombre_Rol' => 'Postulante'],
        ];

        foreach ($roles as $rol) {
            Rol::firstOrCreate(['nombre_Rol' => $rol['nombre_Rol']], $rol);
        }
    }
}
