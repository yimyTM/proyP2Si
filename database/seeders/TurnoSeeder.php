<?php

namespace Database\Seeders;

use App\Models\Turno;
use Illuminate\Database\Seeder;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        $turnos = ['Mañana', 'Tarde', 'Noche'];

        foreach ($turnos as $nombre) {
            Turno::firstOrCreate(['nombTurno' => $nombre]);
        }
    }
}
