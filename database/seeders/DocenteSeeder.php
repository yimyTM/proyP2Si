<?php

namespace Database\Seeders;

use App\Models\Docente;
use App\Models\User;
use App\Services\CuentaProvisionaService;
use Illuminate\Database\Seeder;

class DocenteSeeder extends Seeder
{
    public function run(): void
    {
        $docentes = [
            ['nombre' => 'Juan Carlos',  'apellido' => 'García Pérez',    'ci' => '1234567',  'correo' => 'jgarcia@ficct.edu.bo',   'nroTelefono' => '70011001'],
            ['nombre' => 'María Elena',  'apellido' => 'López Vargas',    'ci' => '2345678',  'correo' => 'mlopez@ficct.edu.bo',    'nroTelefono' => '70022002'],
            ['nombre' => 'Carlos René',  'apellido' => 'Mendoza Suárez',  'ci' => '3456789',  'correo' => 'cmendoza@ficct.edu.bo',  'nroTelefono' => '70033003'],
            ['nombre' => 'Ana Patricia', 'apellido' => 'Flores Quispe',   'ci' => '4567890',  'correo' => 'aflores@ficct.edu.bo',   'nroTelefono' => '70044004'],
            ['nombre' => 'Roberto',      'apellido' => 'Chávez Mamani',   'ci' => '5678901',  'correo' => 'rchavez@ficct.edu.bo',   'nroTelefono' => '70055005'],
        ];

        foreach ($docentes as $d) {
            if (Docente::where('ci', $d['ci'])->exists()) continue;

            $docente = Docente::create([
                'nombre'      => $d['nombre'],
                'apellido'    => $d['apellido'],
                'ci'          => $d['ci'],
                'correo'      => $d['correo'],
                'nroTelefono' => $d['nroTelefono'],
            ]);

            // Crear cuenta de usuario si el correo no está tomado
            if (! User::where('correo', $d['correo'])->exists()) {
                CuentaProvisionaService::crearCuentaDocente($docente);
            }
        }
    }
}
