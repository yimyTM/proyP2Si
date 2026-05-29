<?php

namespace Database\Seeders;

use App\Models\Aula;
use Illuminate\Database\Seeder;

class AulaSeeder extends Seeder
{
    public function run(): void
    {
        $aulas = [
            ['capacidad' => 40, 'cantSillas' => 40, 'cantMesas' => 20],
            ['capacidad' => 40, 'cantSillas' => 40, 'cantMesas' => 20],
            ['capacidad' => 50, 'cantSillas' => 50, 'cantMesas' => 25],
            ['capacidad' => 35, 'cantSillas' => 35, 'cantMesas' => 18],
            ['capacidad' => 30, 'cantSillas' => 30, 'cantMesas' => 30], // Laboratorio
        ];

        // firstOrCreate no aplica bien aquí (varios registros iguales),
        // solo insertamos si la tabla está vacía.
        if (Aula::count() === 0) {
            foreach ($aulas as $aula) {
                Aula::create($aula);
            }
        }
    }
}
