<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;

/**
 * Crea los permisos predefinidos del sistema y los asigna a cada rol por defecto.
 * El administrador puede reasignarlos desde la UI, pero NO puede crear nuevos.
 */
class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Permisos predefinidos del sistema ──────────────────────────────
        $definiciones = [
            ['nombrePermiso' => 'Acceso al Dashboard',             'categoria' => 'Acceso General'],
            ['nombrePermiso' => 'Ver Bitácora del Sistema',         'categoria' => 'Acceso General'],
            ['nombrePermiso' => 'Carga Masiva de Personal (CSV)',   'categoria' => 'Gestión de Personal'],
            ['nombrePermiso' => 'Ver Lista de Docentes',            'categoria' => 'Gestión de Personal'],
            ['nombrePermiso' => 'Editar Datos de Docentes',         'categoria' => 'Gestión de Personal'],
            ['nombrePermiso' => 'Eliminar Docentes',                'categoria' => 'Gestión de Personal'],
            ['nombrePermiso' => 'Buscar y Filtrar Estudiantes',     'categoria' => 'Gestión de Postulantes'],
            ['nombrePermiso' => 'Ver Expedientes de Postulantes',   'categoria' => 'Gestión de Postulantes'],
            ['nombrePermiso' => 'Validar Expedientes',              'categoria' => 'Gestión de Postulantes'],
            ['nombrePermiso' => 'Apertura Automática de Grupos',    'categoria' => 'Estructura Académica'],
            ['nombrePermiso' => 'Asignación Docente y Horarios',    'categoria' => 'Estructura Académica'],
            ['nombrePermiso' => 'Gestionar Gestiones Académicas',   'categoria' => 'Estructura Académica'],
            ['nombrePermiso' => 'Gestionar Roles y Permisos',       'categoria' => 'Seguridad'],
            ['nombrePermiso' => 'Verificar Estado de Pago',         'categoria' => 'Acceso Postulante'],
            ['nombrePermiso' => 'Gestionar Mi Expediente',          'categoria' => 'Acceso Postulante'],
            ['nombrePermiso' => 'Ver Mis Grupos Asignados',         'categoria' => 'Acceso Docente'],
            ['nombrePermiso' => 'Tomar Asistencia de Estudiantes', 'categoria' => 'Acceso Docente'],
        ];

        foreach ($definiciones as $def) {
            Permiso::firstOrCreate(['nombrePermiso' => $def['nombrePermiso']], $def);
        }

        // ── 2. Asignaciones por defecto ───────────────────────────────────────
        $this->asignar('Administrador', [
            'Acceso al Dashboard', 'Ver Bitácora del Sistema',
            'Carga Masiva de Personal (CSV)', 'Ver Lista de Docentes',
            'Editar Datos de Docentes', 'Eliminar Docentes',
            'Buscar y Filtrar Estudiantes', 'Ver Expedientes de Postulantes',
            'Validar Expedientes', 'Apertura Automática de Grupos',
            'Asignación Docente y Horarios', 'Gestionar Gestiones Académicas',
            'Gestionar Roles y Permisos',
        ]);

        $this->asignar('Docente', [
            'Acceso al Dashboard',
            'Ver Mis Grupos Asignados',
            'Tomar Asistencia de Estudiantes',
        ]);

        $this->asignar('Postulante', [
            'Verificar Estado de Pago',
            'Gestionar Mi Expediente',
        ]);
    }

    private function asignar(string $nombreRol, array $permisos): void
    {
        $rol = Rol::where('nombre_Rol', $nombreRol)->first();
        if (! $rol) return;

        $ids = Permiso::whereIn('nombrePermiso', $permisos)->pluck('idPermiso')->toArray();
        $rol->permisos()->syncWithoutDetaching($ids);
    }
}
