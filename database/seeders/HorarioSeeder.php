<?php

namespace Database\Seeders;

use App\Models\Horario;
use Illuminate\Database\Seeder;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        if (Horario::count() > 0) return;

        // Bloques horarios (hora_ini / hora_fin almacenados como datetime con fecha base 2000-01-01)
        $bloques = [
            ['ini' => '07:00', 'fin' => '09:00'],
            ['ini' => '09:00', 'fin' => '11:00'],
            ['ini' => '11:00', 'fin' => '13:00'],
            ['ini' => '14:00', 'fin' => '16:00'],
            ['ini' => '16:00', 'fin' => '18:00'],
            ['ini' => '18:00', 'fin' => '20:00'],
        ];

        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        foreach ($dias as $dia) {
            foreach ($bloques as $b) {
                Horario::create([
                    'hora_ini' => "2000-01-01 {$b['ini']}:00",
                    'hora_fin' => "2000-01-01 {$b['fin']}:00",
                    'dia'      => $dia,
                ]);
            }
        }
    }
}
