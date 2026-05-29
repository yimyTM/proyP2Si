<?php

namespace Database\Seeders;

use App\Models\Modalidad;
use Illuminate\Database\Seeder;

class ModalidadSeeder extends Seeder
{
    public function run(): void
    {
        $modalidades = ['Presencial', 'Virtual', 'Semipresencial'];

        foreach ($modalidades as $nombre) {
            Modalidad::firstOrCreate(['nombModalidad' => $nombre]);
        }
    }
}
