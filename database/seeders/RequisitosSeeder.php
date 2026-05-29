<?php

namespace Database\Seeders;

use App\Models\Requisito;
use Illuminate\Database\Seeder;

class RequisitoSeeder extends Seeder
{
    public function run(): void
    {
        $requisitos = [
            ['nombre' => 'Título de bachiller', 'tipo' => 'P', 'obligatorio' => false],
            ['nombre' => 'Cédula de identidad', 'tipo' => 'P', 'obligatorio' => false],
            ['nombre' => 'Certificado de notas', 'tipo' => 'P', 'obligatorio' => false],
            ['nombre' => 'Titulo en Educacion Superior', 'tipo'  => 'D', 'obligatorio' => false],
            ['nombre' => 'Titulo a Nivel Licenciatura', 'tipo' => 'D', 'obligatorio' => false],
            ['nombre' => 'Titulo a Nivel Posgrado', 'tipo' => 'D', 'obligatorio' => false],
            ['nombre' => 'Titulo a Nivel Especializacion', 'tipo' => 'D', 'obligatorio' => false],
        ];

        foreach ($requisitos as $requisito) {
            Requisito::firstOrCreate(['nombre' => $requisito['nombre']], $requisito);
        }
    }
}