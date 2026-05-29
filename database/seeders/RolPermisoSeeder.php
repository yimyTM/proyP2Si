<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Permiso;
use App\Models\RolPermiso;
use Illuminate\Database\Seeder;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Rol::where('nombre_Rol', 'Administrador')->first();
        $docente = Rol::where('nombre_Rol', 'Docente')->first();
        $postulante = Rol::where('nombre_Rol', 'Postulante')->first();

        $permisos = Permiso::all();

        // Asignar todos los permisos al admin
        foreach ($permisos as $permiso) {
            RolPermiso::firstOrCreate([
                'idRol' => $admin->idRol,
                'idPermiso' => $permiso->idPermiso,
            ]);
        }

        $permisosDocente = ['gestionar_grupos', 'gestionar_docentes']; // nombres reales
        foreach ($permisosDocente as $nombre) {
            $permiso = Permiso::where('nombrePermiso', $nombre)->first();
            if ($permiso) {
                RolPermiso::firstOrCreate([
                    'idRol' => $docente->idRol,
                    'idPermiso' => $permiso->idPermiso,
                ]);
            }
        }
    }
}