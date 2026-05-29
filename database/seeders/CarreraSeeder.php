<?php

namespace Database\Seeders;

use App\Models\Carrera;
use App\Models\Modalidad;
use Illuminate\Database\Seeder;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        $presencial     = Modalidad::where('nombModalidad', 'Presencial')->first();
        $virtual        = Modalidad::where('nombModalidad', 'Virtual')->first();
        $semipresencial = Modalidad::where('nombModalidad', 'Semipresencial')->first();

        $carreras = [
            ['nombre' => 'Ingenieria Informatica',             'codeModalidad' => $presencial?->codeModalidad],
            ['nombre' => 'Ingeniería de Sistemas',             'codeModalidad' => $presencial?->codeModalidad],
            ['nombre' => 'Ingeniería Robotica',             'codeModalidad' => $presencial?->codeModalidad],
            ['nombre' => 'Ingeniería en Telecomunicaciones',   'codeModalidad' => $presencial?->codeModalidad],
            ['nombre' => 'Ingeniería en Informática (Virtual)',  'codeModalidad' => $virtual?->codeModalidad],
            ['nombre' => 'Ingenieria en Sistemas (Virtual)',             'codeModalidad' => $virtual?->codeModalidad],
            ['nombre' => 'Ingeniería Robotica (Virtual)',             'codeModalidad' => $virtual?->codeModalidad],
            ['nombre' => 'Ingeniería en Redes y Telecomunicaciones (Virtual)',             'codeModalidad' => $virtual?->codeModalidad],
        ];

        foreach ($carreras as $c) {
            if ($c['codeModalidad']) {
                Carrera::firstOrCreate(['nombre' => $c['nombre']], $c);
            }
        }
    }
}
