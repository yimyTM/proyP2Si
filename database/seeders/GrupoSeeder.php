<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Modalidad;
use App\Models\Turno;
use Illuminate\Database\Seeder;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        if (Grupo::count() > 0) return;

        $presencial = Modalidad::where('nombModalidad', 'Presencial')->first();
        $manana     = Turno::where('nombTurno', 'Mañana')->first();
        $tarde      = Turno::where('nombTurno', 'Tarde')->first();
        $noche      = Turno::where('nombTurno', 'Noche')->first();

        if (! $presencial || ! $manana) return;

        $grupos = [
            ['capacidad' => 40, 'codeModalidad' => $presencial->codeModalidad, 'idTurno' => $manana->idTurno],
            ['capacidad' => 38, 'codeModalidad' => $presencial->codeModalidad, 'idTurno' => $manana->idTurno],
            ['capacidad' => 38, 'codeModalidad' => $presencial->codeModalidad, 'idTurno' => $manana->idTurno],
            ['capacidad' => 40, 'codeModalidad' => $presencial->codeModalidad, 'idTurno' => $tarde?->idTurno ?? $manana->idTurno],
            ['capacidad' => 35, 'codeModalidad' => $presencial->codeModalidad, 'idTurno' => $tarde?->idTurno ?? $manana->idTurno],
            ['capacidad' => 30, 'codeModalidad' => $presencial->codeModalidad, 'idTurno' => $noche?->idTurno ?? $manana->idTurno],
        ];

        foreach ($grupos as $g) {
            Grupo::create($g);
        }
    }
}
