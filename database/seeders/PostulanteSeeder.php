<?php

namespace Database\Seeders;

use App\Models\Postulante;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PostulanteSeeder extends Seeder
{
    public function run(): void
    {
        $postulantes = [
            [
                'nombre'              => 'Carlos Andrés',
                'apellidos'           => 'Mamani Quispe',
                'ci'                  => '8523641',
                'nroTelefono'         => '72345678',
                'direccion'           => 'Av. Montes 234',
                'sexo'                => 'M',
                'estado'              => 'activo',
                'fecha_nacimiento'    => '2005-03-15',
                'ciudad'              => 'La Paz',
                'colegio_procedencia' => 'Colegio Nacional Bolívar',
                'correo'              => 'carlos.mamani@example.com',
                'contrasena'          => Hash::make('password'),
                'idUsuario'           => null,
            ],
            [
                'nombre'              => 'Lucía Fernanda',
                'apellidos'           => 'Condori Flores',
                'ci'                  => '9134782',
                'nroTelefono'         => '71234567',
                'direccion'           => 'Calle Murillo 115',
                'sexo'                => 'F',
                'estado'              => 'activo',
                'fecha_nacimiento'    => '2004-07-22',
                'ciudad'              => 'Oruro',
                'colegio_procedencia' => 'Colegio Técnico Humanístico Oruro',
                'correo'              => 'lucia.condori@example.com',
                'contrasena'          => Hash::make('password'),
                'idUsuario'           => null,
            ],
            [
                'nombre'              => 'Diego Alejandro',
                'apellidos'           => 'Ticona Vargas',
                'ci'                  => '7841236',
                'nroTelefono'         => '69871234',
                'direccion'           => 'Av. Heroínas 450',
                'sexo'                => 'M',
                'estado'              => 'activo',
                'fecha_nacimiento'    => '2005-11-05',
                'ciudad'              => 'Cochabamba',
                'colegio_procedencia' => 'Colegio Don Bosco',
                'correo'              => 'diego.ticona@example.com',
                'contrasena'          => Hash::make('password'),
                'idUsuario'           => null,
            ],
            [
                'nombre'              => 'Valeria',
                'apellidos'           => 'Gutierrez Salinas',
                'ci'                  => '6325874',
                'nroTelefono'         => '78561234',
                'direccion'           => 'Calle España 89',
                'sexo'                => 'F',
                'estado'              => 'activo',
                'fecha_nacimiento'    => '2004-02-18',
                'ciudad'              => 'Sucre',
                'colegio_procedencia' => 'Colegio Nacional Florida',
                'correo'              => 'valeria.gutierrez@example.com',
                'contrasena'          => Hash::make('password'),
                'idUsuario'           => null,
            ],
            [
                'nombre'              => 'Rodrigo',
                'apellidos'           => 'Apaza Limachi',
                'ci'                  => '5219634',
                'nroTelefono'         => '76543210',
                'direccion'           => 'Av. Del Ejército 320',
                'sexo'                => 'M',
                'estado'              => 'activo',
                'fecha_nacimiento'    => '2006-09-30',
                'ciudad'              => 'El Alto',
                'colegio_procedencia' => 'Colegio Técnico Bolivia',
                'correo'              => 'rodrigo.apaza@example.com',
                'contrasena'          => Hash::make('password'),
                'idUsuario'           => null,
            ],
        ];

        foreach ($postulantes as $data) {
            Postulante::firstOrCreate(['ci' => $data['ci']], $data);
        }
    }
}
