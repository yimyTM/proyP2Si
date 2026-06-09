<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class poblacionCompleta extends Seeder
{
    public function run(): void
    {
        // =========================================================
        // 1. MÓDULOS
        // =========================================================
        $modulos = [
            ['nombreModulo' => 'Modulo de Permisos y Gestion de Accesos', 'created_at' => now(), 'updated_at' => now()],
            ['nombreModulo' => 'Modulo de Postulantes y Resgistro digital',        'created_at' => now(), 'updated_at' => now()],
            ['nombreModulo' => 'Modulo Academico y Gestion de Notas',     'created_at' => now(), 'updated_at' => now()],
            ['nombreModulo' => 'Modulo de Planeacion de Logistica y Grupos',   'created_at' => now(), 'updated_at' => now()],
            ['nombreModulo' => 'Modulo de Reportes, Analitica y Tablero de Control', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('modulos')->insert($modulos);

        // =========================================================
        // 2. PERMISOS
        // =========================================================
        $permisos = [
            ['nombrePermiso' => 'ver',     'created_at' => now(), 'updated_at' => now()],
            ['nombrePermiso' => 'crear',   'created_at' => now(), 'updated_at' => now()],
            ['nombrePermiso' => 'editar',  'created_at' => now(), 'updated_at' => now()],
            ['nombrePermiso' => 'eliminar','created_at' => now(), 'updated_at' => now()],
            ['nombrePermiso' => 'exportar','created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('permisos')->insert($permisos);

        // =========================================================
        // 3. ROLES
        // =========================================================
        $roles = [
            ['nombre_Rol' => 'Administrador', 'descripcion' => 'Acceso total al sistema',                          'created_at' => now(), 'updated_at' => now()],
            ['nombre_Rol' => 'Autoridades',    'descripcion' => 'Gestión de inscripciones y postulantes', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_Rol' => 'Coordinador',        'descripcion' => 'Gestión académica y de docentes',              'created_at' => now(), 'updated_at' => now()],
            ['nombre_Rol' => 'Docente',       'descripcion' => 'Registro de asistencias, notas y ver carga horaria', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_Rol' => 'Postulante',    'descripcion' => 'Acceso a su ficha personal e inscripción',         'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('rols')->insert($roles);

        $rolPermisos = [];
        // Admin (idRol=1) → todos los permisos (1-5)
        for ($p = 1; $p <= 5; $p++) {
            $rolPermisos[] = ['idRol' => 1, 'idPermiso' => $p, 'created_at' => now(), 'updated_at' => now()];
        }
        // Secretaria (idRol=2) → ver, crear, editar
        foreach ([1, 2, 3] as $p) {
            $rolPermisos[] = ['idRol' => 2, 'idPermiso' => $p, 'created_at' => now(), 'updated_at' => now()];
        }
        // Docente (idRol=3) → ver, editar
        foreach ([1, 3] as $p) {
            $rolPermisos[] = ['idRol' => 3, 'idPermiso' => $p, 'created_at' => now(), 'updated_at' => now()];
        }
        // Postulante (idRol=4) → ver
        $rolPermisos[] = ['idRol' => 4, 'idPermiso' => 1, 'created_at' => now(), 'updated_at' => now()];

        DB::table('rol_permisos')->insert($rolPermisos);

        // =========================================================
        // 5. USERS
        // =========================================================
        $users = [
            // Administrador
            [
                'nombreCompleto'   => 'Carlos Alberto Mendoza Vaca',
                'ci'               => '1234567',
                'telefono'         => '77712345',
                'correo'           => 'yimyt771p@gmail.com',
                'password'         => Hash::make('tarqui231A@'),
                'idRol'            => 1,
                'estado'           => true,
                'ultimo_acceso'    => null,
                'intentos_fallidos'=> 0,
                'bloqueado_hasta'  => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            // Secretaria
            [
                'nombreCompleto'   => 'María Fernanda Suárez Ortiz',
                'ci'               => '2345678',
                'telefono'         => '77823456',
                'correo'           => 'secretaria@ficct.edu.bo',
                'password'         => Hash::make('Secretaria@2025'),
                'idRol'            => 2,
                'estado'           => true,
                'ultimo_acceso'    => null,
                'intentos_fallidos'=> 0,
                'bloqueado_hasta'  => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            // Docentes (usuarios)
            [
                'nombreCompleto'   => 'Jorge Luis Peña Roca',
                'ci'               => '3456789',
                'telefono'         => '76534567',
                'correo'           => 'jpeña@ficct.edu.bo',
                'password'         => Hash::make('Docente@2025'),
                'idRol'            => 3,
                'estado'           => true,
                'ultimo_acceso'    => null,
                'intentos_fallidos'=> 0,
                'bloqueado_hasta'  => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'nombreCompleto'   => 'Ana Lucía Torrez Blanco',
                'ci'               => '4567890',
                'telefono'         => '76645678',
                'correo'           => 'atorrez@ficct.edu.bo',
                'password'         => Hash::make('Docente@2025'),
                'idRol'            => 3,
                'estado'           => true,
                'ultimo_acceso'    => null,
                'intentos_fallidos'=> 0,
                'bloqueado_hasta'  => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            // Postulantes (usuarios)
            [
                'nombreCompleto'   => 'Diego Ramiro Flores Aguilar',
                'ci'               => '5678901',
                'telefono'         => '78956789',
                'correo'           => 'dflores@estudiante.bo',
                'password'         => Hash::make('Post@2025'),
                'idRol'            => 4,
                'estado'           => true,
                'ultimo_acceso'    => null,
                'intentos_fallidos'=> 0,
                'bloqueado_hasta'  => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'nombreCompleto'   => 'Valeria Judith Choque Mamani',
                'ci'               => '6789012',
                'telefono'         => '79067890',
                'correo'           => 'vchoque@estudiante.bo',
                'password'         => Hash::make('Post@2025'),
                'idRol'            => 4,
                'estado'           => true,
                'ultimo_acceso'    => null,
                'intentos_fallidos'=> 0,
                'bloqueado_hasta'  => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ];
        DB::table('users')->insert($users);

        // =========================================================
        // 6. REQUISITOS
        // =========================================================
        $requisitos = [
            ['nombre' => 'Fotocopia de CI',           'tipo' => 'P', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Certificado de nacimiento', 'tipo' => 'P', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Libreta escolar',           'tipo' => 'P', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Foto carnet 2x2',           'tipo' => 'P',    'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Certificado médico',        'tipo' => 'P', 'obligatorio' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Titulo Bachiller',       'tipo' => 'P', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()],
            // Requisitos específicos de docentes
            ['nombre' => 'Licenciado en Educación Superior','tipo' => 'D','obligatorio' => true,  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Licenciatura en el Area', 'tipo' => 'D', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Maestria', 'tipo' => 'D', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()],
            ];
        DB::table('requisitos')->insert($requisitos);

        // =========================================================
        // 7. FORMACIONES ACADÉMICAS
        // =========================================================
        $formAcademicas = [
            ['nroProfesion' => 'PRF-001', 'nombProfesion' => 'Ingeniería de Sistemas',      'created_at' => now(), 'updated_at' => now()],
            ['nroProfesion' => 'PRF-002', 'nombProfesion' => 'Licenciatura en Matemáticas', 'created_at' => now(), 'updated_at' => now()],
            ['nroProfesion' => 'PRF-003', 'nombProfesion' => 'Ingeniería Civil',             'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('form_academicas')->insert($formAcademicas);

        $docentes = [
            [
                'nombre'        => 'Jorge Luis',
                'apellido'      => 'Peña Roca',
                'ci'            => '3456789',
                'nroTelefono'   => '76534567',
                'direccion'     => 'Av. Cañoto N° 234, Santa Cruz',
                'carga_horaria' => 20,
                'idUsuario'     => 3,  // usuario docente 1
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nombre'        => 'Ana Lucía',
                'apellido'      => 'Torrez Blanco',
                'ci'            => '4567890',
                'nroTelefono'   => '76645678',
                'direccion'     => 'Radial 27 Mz. 5 Casa 3, Santa Cruz',
                'carga_horaria' => 16,
                'idUsuario'     => 4,  // usuario docente 2
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];
        DB::table('docentes')->insert($docentes);

        // =========================================================
        // 9. FORM_DOCENTE (formaciones por docente)
        // =========================================================
        DB::table('form_docente')->insert([
            ['codigoDoc' => 1, 'idForm' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 1, 'idForm' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 2, 'idForm' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 2, 'idForm' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 10. REQUISITO_DOCENTE
        // =========================================================
        DB::table('requisito_docente')->insert([
            ['idReq' => 6, 'codigoDoc' => 1, 'fecha_entrega' => '2025-01-10', 'entregado' => true,  'validado' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 7, 'codigoDoc' => 1, 'fecha_entrega' => '2025-01-10', 'entregado' => true,  'validado' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 6, 'codigoDoc' => 2, 'fecha_entrega' => '2025-01-12', 'entregado' => true,  'validado' => false, 'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 7, 'codigoDoc' => 2, 'fecha_entrega' => null,         'entregado' => false, 'validado' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 11. MODALIDADES
        // =========================================================
        DB::table('modalidads')->insert([
            ['nombModalidad' => 'Presencial', 'created_at' => now(), 'updated_at' => now()],
            ['nombModalidad' => 'Virtual',    'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 12. CARRERAS
        // =========================================================
        DB::table('carreras')->insert([
            ['nombre' => 'Ingeniería de Sistemas',          'codeModalidad' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingeniería Informática',          'codeModalidad' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingeniería en Redes y Telecomunicaciones', 'codeModalidad' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingeniería en Robótica', 'codeModalidad' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingeniería de Sistemas', 'codeModalidad' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingeniería Informática', 'codeModalidad' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingeniería en Redes y Telecomunicaciones', 'codeModalidad' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ingeniería en Robótica', 'codeModalidad' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 13. AULAS
        // =========================================================
        DB::table('aulas')->insert([
            ['capacidad' => 70, 'created_at' => now(), 'updated_at' => now()],
            ['capacidad' => 70, 'created_at' => now(), 'updated_at' => now()],
            ['capacidad' => 70, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 14. TURNOS
        // =========================================================
        DB::table('turnos')->insert([
            ['nombTurno' => 'Mañana', 'created_at' => now(), 'updated_at' => now()],
            ['nombTurno' => 'Tarde',  'created_at' => now(), 'updated_at' => now()],
            ['nombTurno' => 'Noche',  'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 15. HORARIOS
        // =========================================================
        DB::table('horarios')->insert([
            ['hora_ini' => '07:00:00', 'hora_fin' => '08:30:00', 'dia' => 'Lunes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '08:30:00', 'hora_fin' => '10:00:00', 'dia' => 'Lunes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '10:00:00', 'hora_fin' => '11:30:00', 'dia' => 'Lunes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '14:00:00', 'hora_fin' => '15:30:00', 'dia' => 'Lunes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '15:30:00', 'hora_fin' => '17:00:00', 'dia' => 'Lunes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '17:00:00', 'hora_fin' => '18:30:00', 'dia' => 'Lunes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '18:30:00', 'hora_fin' => '20:00:00', 'dia' => 'Lunes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '20:00:00', 'hora_fin' => '21:30:00', 'dia' => 'Lunes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '21:30:00', 'hora_fin' => '23:00:00', 'dia' => 'Lunes',     'created_at' => now(), 'updated_at' => now()],        
            
            ['hora_ini' => '07:00:00', 'hora_fin' => '08:30:00', 'dia' => 'Martes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '08:30:00', 'hora_fin' => '10:00:00', 'dia' => 'Martes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '10:00:00', 'hora_fin' => '11:30:00', 'dia' => 'Martes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '14:00:00', 'hora_fin' => '15:30:00', 'dia' => 'Martes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '15:30:00', 'hora_fin' => '17:00:00', 'dia' => 'Martes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '17:00:00', 'hora_fin' => '18:30:00', 'dia' => 'Martes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '18:30:00', 'hora_fin' => '20:00:00', 'dia' => 'Martes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '20:00:00', 'hora_fin' => '21:30:00', 'dia' => 'Martes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '21:30:00', 'hora_fin' => '23:00:00', 'dia' => 'Martes',     'created_at' => now(), 'updated_at' => now()],

            ['hora_ini' => '07:00:00', 'hora_fin' => '08:30:00', 'dia' => 'Miercoles',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '08:30:00', 'hora_fin' => '10:00:00', 'dia' => 'Miercoles',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '10:00:00', 'hora_fin' => '11:30:00', 'dia' => 'Miercoles',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '14:00:00', 'hora_fin' => '15:30:00', 'dia' => 'Miercoles',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '15:30:00', 'hora_fin' => '17:00:00', 'dia' => 'Miercoles',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '17:00:00', 'hora_fin' => '18:30:00', 'dia' => 'Miercoles',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '18:30:00', 'hora_fin' => '20:00:00', 'dia' => 'Miercoles',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '20:00:00', 'hora_fin' => '21:30:00', 'dia' => 'Miercoles',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '21:30:00', 'hora_fin' => '23:00:00', 'dia' => 'Miercoles',     'created_at' => now(), 'updated_at' => now()],
            
            ['hora_ini' => '07:00:00', 'hora_fin' => '08:30:00', 'dia' => 'Jueves',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '08:30:00', 'hora_fin' => '10:00:00', 'dia' => 'Jueves',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '10:00:00', 'hora_fin' => '11:30:00', 'dia' => 'Jueves',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '14:00:00', 'hora_fin' => '15:30:00', 'dia' => 'Jueves',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '15:30:00', 'hora_fin' => '17:00:00', 'dia' => 'Jueves',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '17:00:00', 'hora_fin' => '18:30:00', 'dia' => 'Jueves',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '18:30:00', 'hora_fin' => '20:00:00', 'dia' => 'Jueves',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '20:00:00', 'hora_fin' => '21:30:00', 'dia' => 'Jueves',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '21:30:00', 'hora_fin' => '23:00:00', 'dia' => 'Jueves',     'created_at' => now(), 'updated_at' => now()],
            
            ['hora_ini' => '07:00:00', 'hora_fin' => '08:30:00', 'dia' => 'Viernes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '08:30:00', 'hora_fin' => '10:00:00', 'dia' => 'Viernes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '10:00:00', 'hora_fin' => '11:30:00', 'dia' => 'Viernes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '14:00:00', 'hora_fin' => '15:30:00', 'dia' => 'Viernes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '15:30:00', 'hora_fin' => '17:00:00', 'dia' => 'Viernes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '17:00:00', 'hora_fin' => '18:30:00', 'dia' => 'Viernes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '18:30:00', 'hora_fin' => '20:00:00', 'dia' => 'Viernes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '20:00:00', 'hora_fin' => '21:30:00', 'dia' => 'Viernes',     'created_at' => now(), 'updated_at' => now()],
            ['hora_ini' => '21:30:00', 'hora_fin' => '23:00:00', 'dia' => 'Viernes',     'created_at' => now(), 'updated_at' => now()],
            ]);

        // =========================================================
        // 16. MATERIAS
        // =========================================================
        DB::table('materias')->insert([
            ['nombMateria' => 'Matemáticas',    'created_at' => now(), 'updated_at' => now()],
            ['nombMateria' => 'Física',         'created_at' => now(), 'updated_at' => now()],
            ['nombMateria' => 'Computación',    'created_at' => now(), 'updated_at' => now()],
            ['nombMateria' => 'Inglés',    'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 17. GESTIÓN
        // =========================================================
        DB::table('gestions')->insert([
            [
                'nombre'           => 'Gestión 2025-I',
                'fecha_ini'        => '2025-02-01',
                'fecha_fin'        => '2025-05-31',
                'capacidad_maxima' => 120,
                'estado'           => 'Abierta',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);

        // =========================================================
        // 18. GESTION_CARRERAS (cupos por carrera en la gestión)
        // =========================================================
        DB::table('gestion_carreras')->insert([
            ['idGestion' => 1, 'codCarrera' => 1, 'cupos' => 40, 'created_at' => now(), 'updated_at' => now()],
            ['idGestion' => 1, 'codCarrera' => 2, 'cupos' => 35, 'created_at' => now(), 'updated_at' => now()],
            ['idGestion' => 1, 'codCarrera' => 3, 'cupos' => 25, 'created_at' => now(), 'updated_at' => now()],
            ['idGestion' => 1, 'codCarrera' => 4, 'cupos' => 20, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 19. GRUPOS
        // =========================================================
        DB::table('grupos')->insert([
            ['capacidad' => 70, 'numero_grupo' => 'A', 'codeModalidad' => 1, 'idTurno' => 1, 'idGestion' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['capacidad' => 70, 'numero_grupo' => 'B', 'codeModalidad' => 1, 'idTurno' => 2, 'idGestion' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['capacidad' => 70, 'numero_grupo' => 'C', 'codeModalidad' => 1, 'idTurno' => 3, 'idGestion' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 20. MATERI_GRUPOS (docente + aula + horario por grupo)
        // =========================================================
        DB::table('materi_grupos')->insert([
            ['codigoG' => 1, 'idMateria' => 1, 'idHorario' => 1, 'idAula' => 1, 'codigoDoc' => 1],
            ['codigoG' => 1, 'idMateria' => 2, 'idHorario' => 2, 'idAula' => 1, 'codigoDoc' => 1],
            ['codigoG' => 2, 'idMateria' => 3, 'idHorario' => 3, 'idAula' => 2, 'codigoDoc' => 2],
            ['codigoG' => 2, 'idMateria' => 4, 'idHorario' => 4, 'idAula' => 2, 'codigoDoc' => 2],
        ]);

        // =========================================================
        // 21. DOCENTE_GESTION
        // =========================================================
        DB::table('docente_gestion')->insert([
            ['codigoDoc' => 1, 'idGestion' => 1, 'fecha_contrato' => '2025-01-20', 'estado' => 'Contratado',    'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 2, 'idGestion' => 1, 'fecha_contrato' => '2025-01-22', 'estado' => 'No Contratado', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 22. EXÁMENES
        // =========================================================
        DB::table('examens')->insert([
            ['descripcion' => 'Primer parcial',  'fecha' => '2025-03-15 08:00:00', 'ponderacion' => 20.00, 'nroParcial' => 1, 'idGestion' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Segundo parcial', 'fecha' => '2025-04-26 08:00:00', 'ponderacion' => 20.00, 'nroParcial' => 2, 'idGestion' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Tecer Parcial',    'fecha' => '2025-05-24 08:00:00', 'ponderacion' => 40.00, 'nroParcial' => 3, 'idGestion' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 23. EXAM_MATERIAS
        // =========================================================
        DB::table('exam_materias')->insert([
            ['idExamen' => 1, 'idMateria' => 1, 'puntaje' => 30.00, 'created_at' => now(), 'updated_at' => now()],
            ['idExamen' => 1, 'idMateria' => 2, 'puntaje' => 20.00, 'created_at' => now(), 'updated_at' => now()],
            ['idExamen' => 1, 'idMateria' => 3, 'puntaje' => 30.00, 'created_at' => now(), 'updated_at' => now()],
            ['idExamen' => 1, 'idMateria' => 4, 'puntaje' => 20.00, 'created_at' => now(), 'updated_at' => now()],
    
            ['idExamen' => 2, 'idMateria' => 1, 'puntaje' => 30.00, 'created_at' => now(), 'updated_at' => now()],
            ['idExamen' => 2, 'idMateria' => 2, 'puntaje' => 20.00, 'created_at' => now(), 'updated_at' => now()],
            ['idExamen' => 2, 'idMateria' => 3, 'puntaje' => 30.00, 'created_at' => now(), 'updated_at' => now()],
            ['idExamen' => 2, 'idMateria' => 4, 'puntaje' => 20.00, 'created_at' => now(), 'updated_at' => now()],

            ['idExamen' => 3, 'idMateria' => 1, 'puntaje' => 30.00, 'created_at' => now(), 'updated_at' => now()],
            ['idExamen' => 3, 'idMateria' => 2, 'puntaje' => 20.00, 'created_at' => now(), 'updated_at' => now()],
            ['idExamen' => 3, 'idMateria' => 3, 'puntaje' => 30.00, 'created_at' => now(), 'updated_at' => now()],
            ['idExamen' => 3, 'idMateria' => 4, 'puntaje' => 20.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 24. POSTULANTES
        // =========================================================
        DB::table('postulantes')->insert([
            [
                'nombre'              => 'Diego Ramiro',
                'apellidos'           => 'Flores Aguilar',
                'ci'                  => '5678901',
                'nroTelefono'         => '78956789',
                'direccion'           => 'Barrio Equipetrol, Calle 3 N° 45',
                'sexo'                => 'M',
                'estado'              => 'activo',
                'fecha_nacimiento'    => '2006-03-12',
                'ciudad'              => 'Santa Cruz',
                'colegio_procedencia' => 'U.E. Santa Cruz de la Sierra',
                'idUsuario'           => 5,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'nombre'              => 'Valeria Judith',
                'apellidos'           => 'Choque Mamani',
                'ci'                  => '6789012',
                'nroTelefono'         => '79067890',
                'direccion'           => 'Villa 1° de Mayo, Mz. 12 Casa 7',
                'sexo'                => 'F',
                'estado'              => 'activo',
                'fecha_nacimiento'    => '2006-07-25',
                'ciudad'              => 'Santa Cruz',
                'colegio_procedencia' => 'U.E. Rene Moreno',
                'idUsuario'           => 6,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ]);

        // =========================================================
        // 25. REQUISITO_POSTULANTES
        // =========================================================
        DB::table('requisito__postulantes')->insert([
            ['idReq' => 1, 'idPost' => 1, 'fecha_entrega' => '2025-01-25', 'entregado' => true,  'validado' => true,  'ruta_archivo' => 'requisitos/post1/ci.pdf',          'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 2, 'idPost' => 1, 'fecha_entrega' => '2025-01-25', 'entregado' => true,  'validado' => true,  'ruta_archivo' => 'requisitos/post1/nacimiento.pdf',   'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 3, 'idPost' => 1, 'fecha_entrega' => '2025-01-26', 'entregado' => true,  'validado' => false, 'ruta_archivo' => 'requisitos/post1/libreta.pdf',      'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 4, 'idPost' => 1, 'fecha_entrega' => null,         'entregado' => false, 'validado' => false, 'ruta_archivo' => null,                                'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 1, 'idPost' => 2, 'fecha_entrega' => '2025-01-28', 'entregado' => true,  'validado' => true,  'ruta_archivo' => 'requisitos/post2/ci.pdf',          'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 2, 'idPost' => 2, 'fecha_entrega' => '2025-01-28', 'entregado' => true,  'validado' => true,  'ruta_archivo' => 'requisitos/post2/nacimiento.pdf',   'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 3, 'idPost' => 2, 'fecha_entrega' => null,         'entregado' => false, 'validado' => false, 'ruta_archivo' => null,                                'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 26. PAGOS
        // =========================================================
        DB::table('pagos')->insert([
            ['monto' => 150.00, 'fecha' => '2025-01-28', 'estado' => 'pagado',   'idPost' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['monto' => 150.00, 'fecha' => '2025-01-30', 'estado' => 'pendiente','idPost' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 27. COMPROBANTES
        // =========================================================
        DB::table('comprobantes')->insert([
            [
                'codigo'         => 'COMP-2025-0001',
                'nroComprobante' => '00001',
                'concepto'       => 'Inscripción curso preuniversitario 2025-I',
                'fecha'          => '2025-01-28',
                'nroPago'        => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);

        // =========================================================
        // 28. INSCRIPCIONES
        // =========================================================
        DB::table('inscripcions')->insert([
            ['fecha' => '2025-01-29', 'estado' => 'Pendiente',    'idPost' => 1, 'idGestion' => 1, 'codigoG' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['fecha' => '2025-01-31', 'estado' => 'Pendiente', 'idPost' => 2, 'idGestion' => 1, 'codigoG' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 29. CARRERA_INSCRITOS (prioridades de carrera por inscripción)
        // =========================================================
        DB::table('carrera__inscritos')->insert([
            ['idInscripcion' => 1, 'codCarrera' => 1, 'prioridad' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['idInscripcion' => 1, 'codCarrera' => 2, 'prioridad' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['idInscripcion' => 2, 'codCarrera' => 3, 'prioridad' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['idInscripcion' => 2, 'codCarrera' => 1, 'prioridad' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 30. NOTAS
        // =========================================================
        DB::table('notas')->insert([
            // Postulante 1 — Parcial 1
            ['idInscripcion' => 1, 'idEx_materia' => 1, 'calificacion' => 42.50, 'created_at' => now(), 'updated_at' => now()],
            ['idInscripcion' => 1, 'idEx_materia' => 2, 'calificacion' => 38.00, 'created_at' => now(), 'updated_at' => now()],
            ['idInscripcion' => 1, 'idEx_materia' => 3, 'calificacion' => 55.00, 'created_at' => now(), 'updated_at' => now()],
            ['idInscripcion' => 1, 'idEx_materia' => 4, 'calificacion' => 15.00, 'created_at' => now(), 'updated_at' => now()],
            // Postulante 1 — Parcial 2
            ['idInscripcion' => 2, 'idEx_materia' => 3, 'calificacion' => 45.00, 'created_at' => now(), 'updated_at' => now()],
            ['idInscripcion' => 2, 'idEx_materia' => 4, 'calificacion' => 40.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 31. ASISTENCIAS
        // =========================================================
        DB::table('asistencias')->insert([
            ['fecha' => '2025-02-03', 'observacion' => null,              'codigoG' => 1, 'codigoDoc' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['fecha' => '2025-02-05', 'observacion' => null,              'codigoG' => 1, 'codigoDoc' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['fecha' => '2025-02-04', 'observacion' => 'Grupo reducido',  'codigoG' => 2, 'codigoDoc' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}