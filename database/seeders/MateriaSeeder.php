<?php

namespace Database\Seeders;

use App\Models\Materia;
use Illuminate\Database\Seeder;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        $materias = ['Matemática', 'Física', 'Computación', 'Inglés'];

        foreach ($materias as $nombre) {
            Materia::firstOrCreate(['nombMateria' => $nombre]);
        }
    }
}
