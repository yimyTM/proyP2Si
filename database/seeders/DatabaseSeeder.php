<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            UsuarioAdminSeeder::class,
            PermisoSeeder::class,
            ModalidadSeeder::class,
            TurnoSeeder::class,
            MateriaSeeder::class,
            AulaSeeder::class,
            HorarioSeeder::class,
            CarreraSeeder::class,
            DocenteSeeder::class,
            GrupoSeeder::class,
            RequisitoSeeder::class,
            PostulanteSeeder::class,
            RequisitoPostulanteSeeder::class,
        ]);
    }
}
