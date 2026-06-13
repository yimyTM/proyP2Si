<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class poblacionCompleta extends Seeder
{
    public function run(): void
    {
        //==============
        // Modulos
        //==============
        DB::table('modulos')->insert([
            ['nombreModulo' => 'P1: Seguridad y Usuarios',  'created_at' => now(), 'updated_at' => now()],
            ['nombreModulo' => 'P2: Inscripcion y Expedientes',  'created_at' => now(), 'updated_at' => now()],
            ['nombreModulo' => 'P3: Planificacion y Grupos',  'created_at' => now(), 'updated_at' => now()],
            ['nombreModulo' => 'P4: Evaluacion y Notas',  'created_at' => now(), 'updated_at' => now()],
            ['nombreModulo' => 'P5: Reportes y Analiticas',  'created_at' => now(), 'updated_at' => now()],
        ]);


        // =========================================================
        // 1. PERMISOS (31 total)
        // =========================================================
        DB::table('permisos')->insert([
            ['nombrePermiso' => 'iniciar_sesion', 'idModulo' => 1,  'created_at' => now(), 'updated_at' => now()], // 1
            ['nombrePermiso' => 'cerrar_sesion', 'idModulo' => 1,    'created_at' => now(), 'updated_at' => now()], // 2
            ['nombrePermiso' => 'recuperar_contrasena', 'idModulo' => 1,'created_at' => now(), 'updated_at' => now()], // 3
            ['nombrePermiso' => 'ver_postulantes',         'idModulo' => 2, 'created_at' => now(), 'updated_at' => now()], // 4
            ['nombrePermiso' => 'registrar_postulante',    'idModulo' => 2, 'created_at' => now(), 'updated_at' => now()], // 5
            ['nombrePermiso' => 'editar_postulante',       'idModulo' => 2, 'created_at' => now(), 'updated_at' => now()], // 6
            ['nombrePermiso' => 'eliminar_postulante',     'idModulo' => 2, 'created_at' => now(), 'updated_at' => now()], // 7
            ['nombrePermiso' => 'buscar_postulante',       'idModulo' => 2, 'created_at' => now(), 'updated_at' => now()], // 8
            ['nombrePermiso' => 'realizar_pago',           'idModulo' => 2, 'created_at' => now(), 'updated_at' => now()], // 9
            ['nombrePermiso' => 'ver_inscripcion',         'idModulo' => 2, 'created_at' => now(), 'updated_at' => now()], // 10
            ['nombrePermiso' => 'gestionar_inscripciones', 'idModulo' => 2, 'created_at' => now(), 'updated_at' => now()], // 11
            ['nombrePermiso' => 'registrar_notas',      'idModulo' => 4,    'created_at' => now(), 'updated_at' => now()], // 12
            ['nombrePermiso' => 'editar_notas',         'idModulo' => 4,    'created_at' => now(), 'updated_at' => now()], // 13
            ['nombrePermiso' => 'ver_notas',            'idModulo' => 4,    'created_at' => now(), 'updated_at' => now()], // 14
            ['nombrePermiso' => 'ver_resultado_propio', 'idModulo' => 2, 'created_at' => now(), 'updated_at' => now()], // 15
            ['nombrePermiso' => 'ver_grupos',              'idModulo' => 3, 'created_at' => now(), 'updated_at' => now()], // 16
            ['nombrePermiso' => 'gestionar_grupos',        'idModulo' => 3, 'created_at' => now(), 'updated_at' => now()], // 17
            ['nombrePermiso' => 'asignar_estudiante_grupo','idModulo' => 3, 'created_at' => now(), 'updated_at' => now()], // 18
            ['nombrePermiso' => 'ver_docentes',            'idModulo' => 3, 'created_at' => now(), 'updated_at' => now()], // 19
            ['nombrePermiso' => 'gestionar_docentes',      'idModulo' => 3, 'created_at' => now(), 'updated_at' => now()], // 20
            ['nombrePermiso' => 'asignar_docente_grupo',   'idModulo' => 3, 'created_at' => now(), 'updated_at' => now()], // 21
            ['nombrePermiso' => 'ver_carga_horaria',       'idModulo' => 3, 'created_at' => now(), 'updated_at' => now()], // 22
            ['nombrePermiso' => 'registrar_asistencia', 'idModulo' => 4, 'created_at' => now(), 'updated_at' => now()], // 23
            ['nombrePermiso' => 'ver_reportes',     'idModulo' => 5,    'created_at' => now(), 'updated_at' => now()], // 24
            ['nombrePermiso' => 'generar_reportes', 'idModulo' => 5,    'created_at' => now(), 'updated_at' => now()], // 25
            ['nombrePermiso' => 'exportar_reportes','idModulo' => 5,    'created_at' => now(), 'updated_at' => now()], // 26
            ['nombrePermiso' => 'ver_dashboard',    'idModulo' => 5,    'created_at' => now(), 'updated_at' => now()], // 27
            ['nombrePermiso' => 'gestionar_usuarios','idModulo' => 1, 'created_at' => now(), 'updated_at' => now()], // 28
            ['nombrePermiso' => 'importar_usuarios_csv', 'idModulo' => 1,'created_at' => now(), 'updated_at' => now()], // 29
            ['nombrePermiso' => 'ver_perfil', 'idModulo' => 1, 'created_at' => now(), 'updated_at' => now()], // 30
            ['nombrePermiso' => 'ver_solicitudes', 'idModulo' => 3, 'created_at' => now(), 'updated_at' => now()], // 31
        ]);

        // =========================================================
        // 2. ROLES
        // =========================================================
        DB::table('rols')->insert([
            ['nombre_Rol' => 'Administrador', 'descripcion' => 'Acceso total al sistema',                                'created_at' => now(), 'updated_at' => now()], // 1
            ['nombre_Rol' => 'Autoridades',   'descripcion' => 'Visualización de reportes y estadísticas generales',     'created_at' => now(), 'updated_at' => now()], // 2
            ['nombre_Rol' => 'Coordinador',   'descripcion' => 'Gestión académica: grupos, docentes y postulantes',      'created_at' => now(), 'updated_at' => now()], // 3
            ['nombre_Rol' => 'Docente',       'descripcion' => 'Registro de notas, asistencia y carga horaria propia',   'created_at' => now(), 'updated_at' => now()], // 4
            ['nombre_Rol' => 'Postulante',    'descripcion' => 'Consulta de su ficha, inscripción y resultados propios', 'created_at' => now(), 'updated_at' => now()], // 5
        ]);

        // =========================================================
        // 3. ROL_PERMISOS
        // =========================================================
        $rolPermisos = [];
        // Administrador — acceso total
        foreach (range(1, 31) as $p) {
            $rolPermisos[] = ['idRol' => 1, 'idPermiso' => $p, 'created_at' => now(), 'updated_at' => now()];
        }
        // Autoridades — solo lectura
        foreach ([1, 2, 4, 8, 14, 16, 19, 22, 24, 25, 26, 27, 30] as $p) {
            $rolPermisos[] = ['idRol' => 2, 'idPermiso' => $p, 'created_at' => now(), 'updated_at' => now()];
        }
        // Coordinador — gestión académica completa + ver_solicitudes
        foreach ([1, 2, 4, 5, 6, 7, 8, 10, 11, 12, 13, 14, 16, 17, 18, 19, 20, 21, 22, 24, 25, 26, 27, 30, 31] as $p) {
            $rolPermisos[] = ['idRol' => 3, 'idPermiso' => $p, 'created_at' => now(), 'updated_at' => now()];
        }
        // Docente — carga horaria, notas, asistencia, perfil, solicitudes
        foreach ([1, 2, 12, 13, 14, 22, 23, 27, 30, 31] as $p) {
            $rolPermisos[] = ['idRol' => 4, 'idPermiso' => $p, 'created_at' => now(), 'updated_at' => now()];
        }
        // Postulante — pago, inscripción, resultado propio, perfil
        foreach ([1, 2, 3, 9, 10, 15, 27, 30] as $p) {
            $rolPermisos[] = ['idRol' => 5, 'idPermiso' => $p, 'created_at' => now(), 'updated_at' => now()];
        }
        DB::table('rol_permisos')->insert($rolPermisos);

        DB::table('users')->insert([
            // Administrador
            [
                'nombreCompleto'    => 'Yimy Tarqui Mamani',
                'ci'                => '1234567',
                'telefono'          => '77712345',
                'correo'            => 'yimyt771@gmail.com',
                'password'          => Hash::make('tarqui231A@'),
                'idRol'             => 1,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Autoridades
            [
                'nombreCompleto'    => 'María Fernanda Suárez Ortiz',
                'ci'                => '2345678',
                'telefono'          => '77823456',
                'correo'            => 'secretaria@ficct.edu.bo',
                'password'          => Hash::make('Secretaria@2025'),
                'idRol'             => 2,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Coordinador
            [
                'nombreCompleto'    => 'Carlos Alberto Reyes Montaño',
                'ci'                => '9923344',
                'telefono'          => '76099234',
                'correo'            => 'coordinador@ficct.edu.bo',
                'password'          => Hash::make('Coordinador@2025'),
                'idRol'             => 3,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Docente 1
            [
                'nombreCompleto'    => 'Jorge Luis Peña Roca',
                'ci'                => '3456789',
                'telefono'          => '76534567',
                'correo'            => 'jlpena@ficct.edu.bo',
                'password'          => Hash::make('Docente@2025'),
                'idRol'             => 4,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Docente 2
            [
                'nombreCompleto'    => 'Ana Lucía Torrez Blanco',
                'ci'                => '4567890',
                'telefono'          => '76645678',
                'correo'            => 'atorrez@ficct.edu.bo',
                'password'          => Hash::make('Docente@2025'),
                'idRol'             => 4,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Docente 3
            [
                'nombreCompleto'    => 'Roberto Carlos Mendoza Paz',
                'ci'                => '7890123',
                'telefono'          => '76789012',
                'correo'            => 'rmendoza@ficct.edu.bo',
                'password'          => Hash::make('Docente@2025'),
                'idRol'             => 4,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Docente 4
            [
                'nombreCompleto'    => 'Silvia Elena Vargas Castro',
                'ci'                => '8901234',
                'telefono'          => '76890123',
                'correo'            => 'svargas@ficct.edu.bo',
                'password'          => Hash::make('Docente@2025'),
                'idRol'             => 4,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Docente 5
            [
                'nombreCompleto'    => 'Marco Antonio Quispe Lima',
                'ci'                => '9012345',
                'telefono'          => '76901234',
                'correo'            => 'mquispe@ficct.edu.bo',
                'password'          => Hash::make('Docente@2025'),
                'idRol'             => 4,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Postulante 1
            [
                'nombreCompleto'    => 'Diego Ramiro Flores Aguilar',
                'ci'                => '5678901',
                'telefono'          => '78956789',
                'correo'            => 'dflores@estudiante.bo',
                'password'          => Hash::make('Post@2025'),
                'idRol'             => 5,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Postulante 2
            [
                'nombreCompleto'    => 'Valeria Judith Choque Mamani',
                'ci'                => '6789012',
                'telefono'          => '79067890',
                'correo'            => 'vchoque@estudiante.bo',
                'password'          => Hash::make('Post@2025'),
                'idRol'             => 5,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Postulante 3
            [
                'nombreCompleto'    => 'Raúl Alejandro Gutiérrez Vargas',
                'ci'                => '1122334',
                'telefono'          => '79112233',
                'correo'            => 'ragutierrez@estudiante.bo',
                'password'          => Hash::make('Post@2025'),
                'idRol'             => 5,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Postulante 4
            [
                'nombreCompleto'    => 'Claudia Patricia Molina Torres',
                'ci'                => '2233445',
                'telefono'          => '79223344',
                'correo'            => 'cpmolina@estudiante.bo',
                'password'          => Hash::make('Post@2025'),
                'idRol'             => 5,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Postulante 5
            [
                'nombreCompleto'    => 'Fernando Manuel Herrera Cáceres',
                'ci'                => '3344556',
                'telefono'          => '79334455',
                'correo'            => 'fmherrera@estudiante.bo',
                'password'          => Hash::make('Post@2025'),
                'idRol'             => 5,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Postulante 6
            [
                'nombreCompleto'    => 'Lucía Fernanda Rojas Medina',
                'ci'                => '4455667',
                'telefono'          => '79445566',
                'correo'            => 'lfrojas@estudiante.bo',
                'password'          => Hash::make('Post@2025'),
                'idRol'             => 5,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Postulante 7
            [
                'nombreCompleto'    => 'José Antonio Baldiviezo Cruz',
                'ci'                => '5566778',
                'telefono'          => '79556677',
                'correo'            => 'jabaldiviezo@estudiante.bo',
                'password'          => Hash::make('Post@2025'),
                'idRol'             => 5,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Postulante 8
            [
                'nombreCompleto'    => 'Carolina Isabel Vaca Suárez',
                'ci'                => '6677889',
                'telefono'          => '79667788',
                'correo'            => 'civaca@estudiante.bo',
                'password'          => Hash::make('Post@2025'),
                'idRol'             => 5,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Postulante 9
            [
                'nombreCompleto'    => 'Miguel Ángel Salinas Peredo',
                'ci'                => '7788990',
                'telefono'          => '79778899',
                'correo'            => 'masalinas@estudiante.bo',
                'password'          => Hash::make('Post@2025'),
                'idRol'             => 5,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Postulante 10
            [
                'nombreCompleto'    => 'Patricia Roxana Ibáñez Cuellar',
                'ci'                => '8899001',
                'telefono'          => '79889900',
                'correo'            => 'pribañez@estudiante.bo',
                'password'          => Hash::make('Post@2025'),
                'idRol'             => 5,
                'estado'            => true,
                'ultimo_acceso'     => null,
                'intentos_fallidos' => 0,
                'bloqueado_hasta'   => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);

        // =========================================================
        // 5. REQUISITOS
        // =========================================================
        DB::table('requisitos')->insert([
            ['nombre' => 'Fotocopia de CI',                    'tipo' => 'P', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()], // 1
            ['nombre' => 'Certificado de nacimiento',          'tipo' => 'P', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()], // 2
            ['nombre' => 'Libreta escolar',                    'tipo' => 'P', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()], // 3
            ['nombre' => 'Título Bachiller',                   'tipo' => 'P', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()], // 4
            ['nombre' => 'Licenciado en Educación Superior',   'tipo' => 'D', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()], // 5
            ['nombre' => 'Licenciatura en el Área',            'tipo' => 'D', 'obligatorio' => true,  'created_at' => now(), 'updated_at' => now()], // 6
            ['nombre' => 'Maestría',                           'tipo' => 'D', 'obligatorio' => false, 'created_at' => now(), 'updated_at' => now()], // 7
        ]);

        // =========================================================
        // 6. FORMACIONES ACADÉMICAS
        // =========================================================
        DB::table('form_academicas')->insert([
            ['nroProfesion' => 'PRF-001', 'nombProfesion' => 'Ingeniería de Sistemas',      'created_at' => now(), 'updated_at' => now()],
            ['nroProfesion' => 'PRF-002', 'nombProfesion' => 'Licenciatura en Matemáticas', 'created_at' => now(), 'updated_at' => now()],
            ['nroProfesion' => 'PRF-003', 'nombProfesion' => 'Ingeniería Civil',             'created_at' => now(), 'updated_at' => now()],
            ['nroProfesion' => 'PRF-004', 'nombProfesion' => 'Licenciatura en Física',       'created_at' => now(), 'updated_at' => now()],
            ['nroProfesion' => 'PRF-005', 'nombProfesion' => 'Ingeniería en Redes',          'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 7. DOCENTES (5 docentes, idUsuario 4-8)
        // =========================================================
        DB::table('docentes')->insert([
            [
                'nombre'        => 'Jorge Luis',
                'apellido'      => 'Peña Roca',
                'ci'            => '3456789',
                'nroTelefono'   => '76534567',
                'direccion'     => 'Av. Cañoto N° 234, Santa Cruz',
                'carga_horaria' => 20,
                'idUsuario'     => 4,
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
                'idUsuario'     => 5,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nombre'        => 'Roberto Carlos',
                'apellido'      => 'Mendoza Paz',
                'ci'            => '7890123',
                'nroTelefono'   => '76789012',
                'direccion'     => 'Barrio Universitario, Calle 2 N° 12, Santa Cruz',
                'carga_horaria' => 18,
                'idUsuario'     => 6,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nombre'        => 'Silvia Elena',
                'apellido'      => 'Vargas Castro',
                'ci'            => '8901234',
                'nroTelefono'   => '76890123',
                'direccion'     => 'Av. Banzer Km. 4 N° 567, Santa Cruz',
                'carga_horaria' => 14,
                'idUsuario'     => 7,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nombre'        => 'Marco Antonio',
                'apellido'      => 'Quispe Lima',
                'ci'            => '9012345',
                'nroTelefono'   => '76901234',
                'direccion'     => 'Villa Primero de Mayo Mz. 8 Casa 15, Santa Cruz',
                'carga_horaria' => 12,
                'idUsuario'     => 8,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);

        // =========================================================
        // 8. FORM_DOCENTE
        // =========================================================
        DB::table('form_docente')->insert([
            ['codigoDoc' => 1, 'idForm' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 1, 'idForm' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 2, 'idForm' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 2, 'idForm' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 3, 'idForm' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 3, 'idForm' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 4, 'idForm' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 4, 'idForm' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 5, 'idForm' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 5, 'idForm' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 9. REQUISITO_DOCENTE
        // =========================================================
        DB::table('requisito_docente')->insert([
            ['idReq' => 6, 'codigoDoc' => 1, 'fecha_entrega' => '2024-01-10', 'entregado' => true,  'validado' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 7, 'codigoDoc' => 1, 'fecha_entrega' => '2024-01-10', 'entregado' => true,  'validado' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 6, 'codigoDoc' => 2, 'fecha_entrega' => '2024-01-12', 'entregado' => true,  'validado' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 7, 'codigoDoc' => 2, 'fecha_entrega' => '2024-01-12', 'entregado' => true,  'validado' => false, 'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 6, 'codigoDoc' => 3, 'fecha_entrega' => '2024-01-15', 'entregado' => true,  'validado' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 7, 'codigoDoc' => 3, 'fecha_entrega' => null,         'entregado' => false, 'validado' => false, 'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 6, 'codigoDoc' => 4, 'fecha_entrega' => '2024-01-18', 'entregado' => true,  'validado' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 7, 'codigoDoc' => 4, 'fecha_entrega' => '2024-01-18', 'entregado' => true,  'validado' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 6, 'codigoDoc' => 5, 'fecha_entrega' => '2024-01-20', 'entregado' => true,  'validado' => true,  'created_at' => now(), 'updated_at' => now()],
            ['idReq' => 7, 'codigoDoc' => 5, 'fecha_entrega' => null,         'entregado' => false, 'validado' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 10. MODALIDADES
        // =========================================================
        DB::table('modalidads')->insert([
            ['nombModalidad' => 'Presencial', 'created_at' => now(), 'updated_at' => now()],
            ['nombModalidad' => 'Virtual',    'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 11. CARRERAS (4 presencial + 4 virtual)
        // =========================================================
        DB::table('carreras')->insert([
            ['nombre' => 'Ingeniería de Sistemas',                   'codeModalidad' => 1, 'created_at' => now(), 'updated_at' => now()], // 1
            ['nombre' => 'Ingeniería Informática',                   'codeModalidad' => 1, 'created_at' => now(), 'updated_at' => now()], // 2
            ['nombre' => 'Ingeniería en Redes y Telecomunicaciones', 'codeModalidad' => 1, 'created_at' => now(), 'updated_at' => now()], // 3
            ['nombre' => 'Ingeniería en Robótica',                   'codeModalidad' => 1, 'created_at' => now(), 'updated_at' => now()], // 4
            ['nombre' => 'Ingeniería de Sistemas',                   'codeModalidad' => 2, 'created_at' => now(), 'updated_at' => now()], // 5
            ['nombre' => 'Ingeniería Informática',                   'codeModalidad' => 2, 'created_at' => now(), 'updated_at' => now()], // 6
            ['nombre' => 'Ingeniería en Redes y Telecomunicaciones', 'codeModalidad' => 2, 'created_at' => now(), 'updated_at' => now()], // 7
            ['nombre' => 'Ingeniería en Robótica',                   'codeModalidad' => 2, 'created_at' => now(), 'updated_at' => now()], // 8
        ]);

        // =========================================================
        // 12. AULAS
        // =========================================================
        DB::table('aulas')->insert([
            ['capacidad' => 70, 'created_at' => now(), 'updated_at' => now()],
            ['capacidad' => 70, 'created_at' => now(), 'updated_at' => now()],
            ['capacidad' => 70, 'created_at' => now(), 'updated_at' => now()],
            ['capacidad' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['capacidad' => 50, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 13. TURNOS
        // =========================================================
        DB::table('turnos')->insert([
            ['nombTurno' => 'Mañana', 'created_at' => now(), 'updated_at' => now()],
            ['nombTurno' => 'Tarde',  'created_at' => now(), 'updated_at' => now()],
            ['nombTurno' => 'Noche',  'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 14. HORARIOS (40 slots: 8 por día × 5 días)
        //     Lunes 1-8 | Martes 9-16 | Miércoles 17-24
        //     Jueves 25-32 | Viernes 33-40
        // =========================================================
        $diasHorarios = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'];
        $slots = [
            ['hora_ini' => '07:00:00', 'hora_fin' => '08:30:00'],
            ['hora_ini' => '08:30:00', 'hora_fin' => '10:00:00'],
            ['hora_ini' => '10:00:00', 'hora_fin' => '11:30:00'],
            ['hora_ini' => '14:00:00', 'hora_fin' => '15:30:00'],
            ['hora_ini' => '15:30:00', 'hora_fin' => '17:00:00'],
            ['hora_ini' => '17:00:00', 'hora_fin' => '18:30:00'],
            ['hora_ini' => '18:30:00', 'hora_fin' => '20:00:00'],
            ['hora_ini' => '20:00:00', 'hora_fin' => '21:30:00'],
        ];
        $horarios = [];
        foreach ($diasHorarios as $dia) {
            foreach ($slots as $slot) {
                $horarios[] = ['hora_ini' => $slot['hora_ini'], 'hora_fin' => $slot['hora_fin'], 'dia' => $dia, 'created_at' => now(), 'updated_at' => now()];
            }
        }
        DB::table('horarios')->insert($horarios);

        // =========================================================
        // 15. MATERIAS (5 materias)
        // =========================================================
        DB::table('materias')->insert([
            ['nombMateria' => 'Matemáticas', 'created_at' => now(), 'updated_at' => now()], // 1
            ['nombMateria' => 'Física',      'created_at' => now(), 'updated_at' => now()], // 2
            ['nombMateria' => 'Computación', 'created_at' => now(), 'updated_at' => now()], // 3
            ['nombMateria' => 'Inglés',      'created_at' => now(), 'updated_at' => now()], // 4
            ['nombMateria' => 'Química',     'created_at' => now(), 'updated_at' => now()], // 5
        ]);

        // =========================================================
        // 16. GESTIONES
        //     gestión 1: 2024-II (Cerrada)
        //     gestión 2: 2026-I  (Abierta)
        // =========================================================
        DB::table('gestions')->insert([
            [
                'nombre'           => 'Gestión 2024-II',
                'fecha_ini'        => '2024-02-01',
                'fecha_fin'        => '2024-05-31',
                'capacidad_maxima' => 140,
                'estado'           => 'Cerrada',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'nombre'           => 'Gestión 2026-I',
                'fecha_ini'        => '2026-02-01',
                'fecha_fin'        => '2026-06-30',
                'capacidad_maxima' => 210,
                'estado'           => 'Abierta',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);

        // =========================================================
        // 17. GESTION_CARRERAS
        // =========================================================
        DB::table('gestion_carreras')->insert([
            // Gestión 1 — cupos presencial
            ['idGestion' => 1, 'codCarrera' => 1, 'cupos' => 40, 'created_at' => now(), 'updated_at' => now()],
            ['idGestion' => 1, 'codCarrera' => 2, 'cupos' => 35, 'created_at' => now(), 'updated_at' => now()],
            ['idGestion' => 1, 'codCarrera' => 3, 'cupos' => 25, 'created_at' => now(), 'updated_at' => now()],
            ['idGestion' => 1, 'codCarrera' => 4, 'cupos' => 20, 'created_at' => now(), 'updated_at' => now()],
            // Gestión 2 — cupos presencial
            ['idGestion' => 2, 'codCarrera' => 1, 'cupos' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['idGestion' => 2, 'codCarrera' => 2, 'cupos' => 45, 'created_at' => now(), 'updated_at' => now()],
            ['idGestion' => 2, 'codCarrera' => 3, 'cupos' => 35, 'created_at' => now(), 'updated_at' => now()],
            ['idGestion' => 2, 'codCarrera' => 4, 'cupos' => 30, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 18. GRUPOS
        //     codigoG 1-2: gestión 1 (Grupo A Mañana, B Tarde)
        //     codigoG 3-5: gestión 2 (Grupo A Mañana, B Tarde, C Noche)
        // =========================================================
        DB::table('grupos')->insert([
            ['capacidad' => 70, 'numero_grupo' => 'A', 'codeModalidad' => 1, 'idTurno' => 1, 'idGestion' => 1, 'created_at' => now(), 'updated_at' => now()], // 1
            ['capacidad' => 70, 'numero_grupo' => 'B', 'codeModalidad' => 1, 'idTurno' => 2, 'idGestion' => 1, 'created_at' => now(), 'updated_at' => now()], // 2
            ['capacidad' => 70, 'numero_grupo' => 'A', 'codeModalidad' => 1, 'idTurno' => 1, 'idGestion' => 2, 'created_at' => now(), 'updated_at' => now()], // 3
            ['capacidad' => 70, 'numero_grupo' => 'B', 'codeModalidad' => 1, 'idTurno' => 2, 'idGestion' => 2, 'created_at' => now(), 'updated_at' => now()], // 4
            ['capacidad' => 70, 'numero_grupo' => 'C', 'codeModalidad' => 1, 'idTurno' => 3, 'idGestion' => 2, 'created_at' => now(), 'updated_at' => now()], // 5
        ]);

        // =========================================================
        // 19. MATERI_GRUPOS (sin timestamps)
        //     Cada docente enseña una materia, un horario fijo por grupo
        //     Horarios: G1→1,9,17,25,33  G2→4,12,20,28,36
        //               G3→2,10,18,26,34 G4→5,13,21,29,37  G5→7,15,23,31,39
        //     Aulas: G1→1, G2→2, G3→1, G4→2, G5→3
        // =========================================================
        DB::table('materi_grupos')->insert([
            // G1 (gestión 1, Mañana)
            ['codigoG' => 1, 'idMateria' => 1, 'idHorario' =>  1, 'idAula' => 1, 'codigoDoc' => 1],
            ['codigoG' => 1, 'idMateria' => 2, 'idHorario' =>  9, 'idAula' => 1, 'codigoDoc' => 2],
            ['codigoG' => 1, 'idMateria' => 3, 'idHorario' => 17, 'idAula' => 1, 'codigoDoc' => 3],
            ['codigoG' => 1, 'idMateria' => 4, 'idHorario' => 25, 'idAula' => 1, 'codigoDoc' => 4],
            ['codigoG' => 1, 'idMateria' => 5, 'idHorario' => 33, 'idAula' => 1, 'codigoDoc' => 5],
            // G2 (gestión 1, Tarde)
            ['codigoG' => 2, 'idMateria' => 1, 'idHorario' =>  4, 'idAula' => 2, 'codigoDoc' => 1],
            ['codigoG' => 2, 'idMateria' => 2, 'idHorario' => 12, 'idAula' => 2, 'codigoDoc' => 2],
            ['codigoG' => 2, 'idMateria' => 3, 'idHorario' => 20, 'idAula' => 2, 'codigoDoc' => 3],
            ['codigoG' => 2, 'idMateria' => 4, 'idHorario' => 28, 'idAula' => 2, 'codigoDoc' => 4],
            ['codigoG' => 2, 'idMateria' => 5, 'idHorario' => 36, 'idAula' => 2, 'codigoDoc' => 5],
            // G3 (gestión 2, Mañana)
            ['codigoG' => 3, 'idMateria' => 1, 'idHorario' =>  2, 'idAula' => 1, 'codigoDoc' => 1],
            ['codigoG' => 3, 'idMateria' => 2, 'idHorario' => 10, 'idAula' => 1, 'codigoDoc' => 2],
            ['codigoG' => 3, 'idMateria' => 3, 'idHorario' => 18, 'idAula' => 1, 'codigoDoc' => 3],
            ['codigoG' => 3, 'idMateria' => 4, 'idHorario' => 26, 'idAula' => 1, 'codigoDoc' => 4],
            ['codigoG' => 3, 'idMateria' => 5, 'idHorario' => 34, 'idAula' => 1, 'codigoDoc' => 5],
            // G4 (gestión 2, Tarde)
            ['codigoG' => 4, 'idMateria' => 1, 'idHorario' =>  5, 'idAula' => 2, 'codigoDoc' => 1],
            ['codigoG' => 4, 'idMateria' => 2, 'idHorario' => 13, 'idAula' => 2, 'codigoDoc' => 2],
            ['codigoG' => 4, 'idMateria' => 3, 'idHorario' => 21, 'idAula' => 2, 'codigoDoc' => 3],
            ['codigoG' => 4, 'idMateria' => 4, 'idHorario' => 29, 'idAula' => 2, 'codigoDoc' => 4],
            ['codigoG' => 4, 'idMateria' => 5, 'idHorario' => 37, 'idAula' => 2, 'codigoDoc' => 5],
            // G5 (gestión 2, Noche)
            ['codigoG' => 5, 'idMateria' => 1, 'idHorario' =>  7, 'idAula' => 3, 'codigoDoc' => 1],
            ['codigoG' => 5, 'idMateria' => 2, 'idHorario' => 15, 'idAula' => 3, 'codigoDoc' => 2],
            ['codigoG' => 5, 'idMateria' => 3, 'idHorario' => 23, 'idAula' => 3, 'codigoDoc' => 3],
            ['codigoG' => 5, 'idMateria' => 4, 'idHorario' => 31, 'idAula' => 3, 'codigoDoc' => 4],
            ['codigoG' => 5, 'idMateria' => 5, 'idHorario' => 39, 'idAula' => 3, 'codigoDoc' => 5],
        ]);

        // =========================================================
        // 20. DOCENTE_GESTION (5 docentes × 2 gestiones = 10)
        // =========================================================
        DB::table('docente_gestion')->insert([
            ['codigoDoc' => 1, 'idGestion' => 1, 'fecha_contrato' => '2024-01-20', 'estado' => 'Contratado', 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 2, 'idGestion' => 1, 'fecha_contrato' => '2024-01-20', 'estado' => 'Contratado', 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 3, 'idGestion' => 1, 'fecha_contrato' => '2024-01-22', 'estado' => 'Contratado', 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 4, 'idGestion' => 1, 'fecha_contrato' => '2024-01-22', 'estado' => 'Contratado', 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 5, 'idGestion' => 1, 'fecha_contrato' => '2024-01-25', 'estado' => 'Contratado', 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 1, 'idGestion' => 2, 'fecha_contrato' => '2026-01-15', 'estado' => 'Contratado', 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 2, 'idGestion' => 2, 'fecha_contrato' => '2026-01-15', 'estado' => 'Contratado', 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 3, 'idGestion' => 2, 'fecha_contrato' => '2026-01-18', 'estado' => 'Contratado', 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 4, 'idGestion' => 2, 'fecha_contrato' => '2026-01-18', 'estado' => 'Contratado', 'created_at' => now(), 'updated_at' => now()],
            ['codigoDoc' => 5, 'idGestion' => 2, 'fecha_contrato' => '2026-01-20', 'estado' => 'Contratado', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 21. EXÁMENES
        //     Gestión 1: E1(20%), E2(20%), E3(60%) → suma 100%
        //     Gestión 2: E4(20%), E5(20%) → gestión aún abierta
        // =========================================================
        DB::table('examens')->insert([
            ['descripcion' => 'Primer parcial',   'fecha' => '2024-03-15 08:00:00', 'ponderacion' => 20.00, 'nroParcial' => 1, 'idGestion' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Segundo parcial',  'fecha' => '2024-04-15 08:00:00', 'ponderacion' => 20.00, 'nroParcial' => 2, 'idGestion' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Tercer parcial',   'fecha' => '2024-05-20 08:00:00', 'ponderacion' => 60.00, 'nroParcial' => 3, 'idGestion' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Primer parcial',   'fecha' => '2026-03-15 08:00:00', 'ponderacion' => 20.00, 'nroParcial' => 1, 'idGestion' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['descripcion' => 'Segundo parcial',  'fecha' => '2026-04-15 08:00:00', 'ponderacion' => 20.00, 'nroParcial' => 2, 'idGestion' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 22. EXAM_MATERIAS (5 exámenes × 5 materias = 25)
        //     Puntajes: Mat=30, Fis=20, Comp=25, Ing=15, Qui=10 → suma 100
        //     idEx_materia asignados en orden de inserción: 1..25
        // =========================================================
        $examMaterias = [];
        $puntajes = [1 => 30.00, 2 => 20.00, 3 => 25.00, 4 => 15.00, 5 => 10.00];
        for ($ex = 1; $ex <= 5; $ex++) {
            for ($mat = 1; $mat <= 5; $mat++) {
                $examMaterias[] = [
                    'idExamen'   => $ex,
                    'idMateria'  => $mat,
                    'puntaje'    => $puntajes[$mat],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('exam_materias')->insert($examMaterias);

        // =========================================================
        // 23. POSTULANTES (10 total, idUsuario 9-18)
        // =========================================================
        DB::table('postulantes')->insert([
            ['nombre' => 'Diego Ramiro',      'apellidos' => 'Flores Aguilar',     'ci' => '5678901', 'nroTelefono' => '78956789', 'direccion' => 'Barrio Equipetrol, Calle 3 N° 45',      'sexo' => 'M', 'estado' => 'activo', 'fecha_nacimiento' => '2006-03-12', 'ciudad' => 'Santa Cruz', 'colegio_procedencia' => 'U.E. Santa Cruz de la Sierra',     'idUsuario' => 9,  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Valeria Judith',    'apellidos' => 'Choque Mamani',      'ci' => '6789012', 'nroTelefono' => '79067890', 'direccion' => 'Villa 1° de Mayo, Mz. 12 Casa 7',       'sexo' => 'F', 'estado' => 'activo', 'fecha_nacimiento' => '2006-07-25', 'ciudad' => 'Santa Cruz', 'colegio_procedencia' => 'U.E. René Moreno',                  'idUsuario' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Raúl Alejandro',    'apellidos' => 'Gutiérrez Vargas',   'ci' => '1122334', 'nroTelefono' => '79112233', 'direccion' => 'Av. Monseñor Rivero N° 890',            'sexo' => 'M', 'estado' => 'activo', 'fecha_nacimiento' => '2005-11-08', 'ciudad' => 'Santa Cruz', 'colegio_procedencia' => 'U.E. Alemán',                       'idUsuario' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Claudia Patricia',  'apellidos' => 'Molina Torres',      'ci' => '2233445', 'nroTelefono' => '79223344', 'direccion' => 'Urb. Las Palmas, Calle 5 N° 12',        'sexo' => 'F', 'estado' => 'activo', 'fecha_nacimiento' => '2006-05-14', 'ciudad' => 'Santa Cruz', 'colegio_procedencia' => 'U.E. Don Bosco',                    'idUsuario' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Fernando Manuel',   'apellidos' => 'Herrera Cáceres',    'ci' => '3344556', 'nroTelefono' => '79334455', 'direccion' => 'Radial 13 Mz. 3 Casa 9',                'sexo' => 'M', 'estado' => 'activo', 'fecha_nacimiento' => '2005-09-20', 'ciudad' => 'Santa Cruz', 'colegio_procedencia' => 'U.E. Cosmos 79',                    'idUsuario' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Lucía Fernanda',    'apellidos' => 'Rojas Medina',       'ci' => '4455667', 'nroTelefono' => '79445566', 'direccion' => 'Av. Beni N° 345, Santa Cruz',           'sexo' => 'F', 'estado' => 'activo', 'fecha_nacimiento' => '2005-02-28', 'ciudad' => 'Santa Cruz', 'colegio_procedencia' => 'U.E. San Ignacio de Loyola',        'idUsuario' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'José Antonio',      'apellidos' => 'Baldiviezo Cruz',    'ci' => '5566778', 'nroTelefono' => '79556677', 'direccion' => 'Plan 3000, Mz. 22 Casa 4',              'sexo' => 'M', 'estado' => 'activo', 'fecha_nacimiento' => '2006-08-05', 'ciudad' => 'Santa Cruz', 'colegio_procedencia' => 'U.E. Mariscal Sucre',               'idUsuario' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Carolina Isabel',   'apellidos' => 'Vaca Suárez',        'ci' => '6677889', 'nroTelefono' => '79667788', 'direccion' => 'Urb. Los Mangales, Calle 2 N° 7',       'sexo' => 'F', 'estado' => 'activo', 'fecha_nacimiento' => '2005-12-17', 'ciudad' => 'Santa Cruz', 'colegio_procedencia' => 'U.E. Adventista',                   'idUsuario' => 16, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Miguel Ángel',      'apellidos' => 'Salinas Peredo',     'ci' => '7788990', 'nroTelefono' => '79778899', 'direccion' => 'Av. Paurito Km. 2, Santa Cruz',         'sexo' => 'M', 'estado' => 'activo', 'fecha_nacimiento' => '2006-04-10', 'ciudad' => 'Santa Cruz', 'colegio_procedencia' => 'U.E. Eduardo Avaroa',               'idUsuario' => 17, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Patricia Roxana',   'apellidos' => 'Ibáñez Cuellar',     'ci' => '8899001', 'nroTelefono' => '79889900', 'direccion' => 'Calle Junín N° 123, Montero',           'sexo' => 'F', 'estado' => 'activo', 'fecha_nacimiento' => '2005-06-22', 'ciudad' => 'Montero',    'colegio_procedencia' => 'U.E. Gran Mariscal de Ayacucho',    'idUsuario' => 18, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 24. REQUISITO_POSTULANTES
        // =========================================================
        $reqPost = [];
        // Post 1-8: expedientes en distintos estados
        $postReqData = [
            1 => [true,true,true,true],
            2 => [true,true,false,false],
            3 => [true,true,true,true],
            4 => [true,true,true,false],
            5 => [true,true,true,true],
            6 => [true,true,true,true],
            7 => [true,false,false,false],
            8 => [true,true,true,true],
            9 => [true,true,false,false],
            10=> [true,true,true,false],
        ];
        $fechasPost = [
            1=>'2024-01-25', 2=>'2024-01-26', 3=>'2024-01-20', 4=>'2024-01-22',
            5=>'2024-01-18', 6=>'2024-01-19', 7=>'2024-01-27', 8=>'2024-01-21',
            9=>'2026-01-20', 10=>'2026-01-21',
        ];
        foreach ($postReqData as $idPost => $reqs) {
            foreach ($reqs as $idx => $entregado) {
                $idReq = $idx + 1;
                $reqPost[] = [
                    'idReq'         => $idReq,
                    'idPost'        => $idPost,
                    'fecha_entrega' => $entregado ? $fechasPost[$idPost] : null,
                    'entregado'     => $entregado,
                    'validado'      => $entregado && in_array($idPost, [1,3,5,6,8]),
                    'ruta_archivo'  => $entregado ? "requisitos/post{$idPost}/req{$idReq}.pdf" : null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }
        DB::table('requisito__postulantes')->insert($reqPost);

        // =========================================================
        // 25. PAGOS (10 pagos, uno por postulante)
        // =========================================================
        DB::table('pagos')->insert([
            ['monto' => 150.00, 'fecha' => '2024-01-28', 'estado' => 'pagado', 'idPost' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['monto' => 150.00, 'fecha' => '2024-01-28', 'estado' => 'pagado', 'idPost' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['monto' => 150.00, 'fecha' => '2024-01-22', 'estado' => 'pagado', 'idPost' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['monto' => 150.00, 'fecha' => '2024-01-23', 'estado' => 'pagado', 'idPost' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['monto' => 150.00, 'fecha' => '2024-01-20', 'estado' => 'pagado', 'idPost' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['monto' => 150.00, 'fecha' => '2024-01-21', 'estado' => 'pagado', 'idPost' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['monto' => 150.00, 'fecha' => '2024-01-29', 'estado' => 'pagado', 'idPost' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['monto' => 150.00, 'fecha' => '2024-01-24', 'estado' => 'pagado', 'idPost' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['monto' => 150.00, 'fecha' => '2026-01-25', 'estado' => 'pagado', 'idPost' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['monto' => 150.00, 'fecha' => '2026-01-26', 'estado' => 'pagado', 'idPost' => 10,'created_at' => now(), 'updated_at' => now()],
        ]);

        // =========================================================
        // 26. COMPROBANTES (uno por pago)
        // =========================================================
        $comprobantes = [];
        for ($i = 1; $i <= 10; $i++) {
            $gestion = $i <= 8 ? '2024' : '2026';
            $comprobantes[] = [
                'codigo'         => "COMP-{$gestion}-" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nroComprobante' => str_pad($i, 5, '0', STR_PAD_LEFT),
                'concepto'       => "Inscripción curso preuniversitario {$gestion}",
                'fecha'          => $i <= 8 ? "2024-01-" . str_pad(20 + $i, 2, '0', STR_PAD_LEFT) : "2026-01-" . str_pad(24 + ($i - 8), 2, '0', STR_PAD_LEFT),
                'nroPago'        => $i,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }
        DB::table('comprobantes')->insert($comprobantes);

        // =========================================================
        // 27. INSCRIPCIONES (13 total)
        //     insc 1-4  → G1 (gestión 1, Mañana): post 1,2,3,4 → Validado, con promedio
        //     insc 5-8  → G2 (gestión 1, Tarde):  post 5,6,7,8 → Validado, con promedio
        //     insc 9-13 → G3 (gestión 2):          post 2,4,7,9,10 → Pendiente
        //
        //     Umbral de ingreso: promedio ≥ 60
        //     Promedios G1/G2 (P1×20 + P2×20 + P3×60)/100:
        //       insc1(Diego)   = (78×20+76×20+72×60)/100 = 74.0  → Ingresa → IS
        //       insc2(Valeria) = (47×20+47×20+42×60)/100 = 44.0  → No ingresa
        //       insc3(Raúl)    = (89×20+87×20+86×60)/100 = 86.8  → Ingresa → II
        //       insc4(Claudia) = (62×20+62×20+55×60)/100 = 57.8  → No ingresa
        //       insc5(Fernando)= (72×20+71×20+68×60)/100 = 69.4  → Ingresa → IS
        //       insc6(Lucía)   = (92×20+90×20+89×60)/100 = 89.8  → Ingresa → IS
        //       insc7(José)    = (39×20+39×20+37×60)/100 = 37.8  → No ingresa
        //       insc8(Carolina)= (66×20+68×20+63×60)/100 = 64.6  → Ingresa → IRT
        // =========================================================
        DB::table('inscripcions')->insert([
            // G1 — gestión 1 — CERRADA con resultados
            ['fecha'=>'2024-01-29','estado'=>'Validado','idPost'=>1,'idGestion'=>1,'codigoG'=>1,'motivo_rechazo'=>null,'promedio'=>74.0, 'resultado'=>'Ingresa',    'codCarreraAsignada'=>1,'estado_admision'=>'Admitido',  'created_at'=>now(),'updated_at'=>now()],
            ['fecha'=>'2024-01-29','estado'=>'Validado','idPost'=>2,'idGestion'=>1,'codigoG'=>1,'motivo_rechazo'=>null,'promedio'=>44.0, 'resultado'=>'No ingresa', 'codCarreraAsignada'=>null,'estado_admision'=>'Reprobado','created_at'=>now(),'updated_at'=>now()],
            ['fecha'=>'2024-01-22','estado'=>'Validado','idPost'=>3,'idGestion'=>1,'codigoG'=>1,'motivo_rechazo'=>null,'promedio'=>86.8, 'resultado'=>'Ingresa',    'codCarreraAsignada'=>2,'estado_admision'=>'Admitido',  'created_at'=>now(),'updated_at'=>now()],
            ['fecha'=>'2024-01-23','estado'=>'Validado','idPost'=>4,'idGestion'=>1,'codigoG'=>1,'motivo_rechazo'=>null,'promedio'=>57.8, 'resultado'=>'No ingresa', 'codCarreraAsignada'=>null,'estado_admision'=>'Reprobado','created_at'=>now(),'updated_at'=>now()],
            // G2 — gestión 1 — CERRADA con resultados
            ['fecha'=>'2024-01-20','estado'=>'Validado','idPost'=>5,'idGestion'=>1,'codigoG'=>2,'motivo_rechazo'=>null,'promedio'=>69.4, 'resultado'=>'Ingresa',    'codCarreraAsignada'=>1,'estado_admision'=>'Admitido',  'created_at'=>now(),'updated_at'=>now()],
            ['fecha'=>'2024-01-21','estado'=>'Validado','idPost'=>6,'idGestion'=>1,'codigoG'=>2,'motivo_rechazo'=>null,'promedio'=>89.8, 'resultado'=>'Ingresa',    'codCarreraAsignada'=>1,'estado_admision'=>'Admitido',  'created_at'=>now(),'updated_at'=>now()],
            ['fecha'=>'2024-01-29','estado'=>'Validado','idPost'=>7,'idGestion'=>1,'codigoG'=>2,'motivo_rechazo'=>null,'promedio'=>37.8, 'resultado'=>'No ingresa', 'codCarreraAsignada'=>null,'estado_admision'=>'Reprobado','created_at'=>now(),'updated_at'=>now()],
            ['fecha'=>'2024-01-24','estado'=>'Validado','idPost'=>8,'idGestion'=>1,'codigoG'=>2,'motivo_rechazo'=>null,'promedio'=>64.6, 'resultado'=>'Ingresa',    'codCarreraAsignada'=>3,'estado_admision'=>'Admitido',  'created_at'=>now(),'updated_at'=>now()],
            // G3 — gestión 2 — ABIERTA (en proceso)
            ['fecha'=>'2026-01-29','estado'=>'Pendiente','idPost'=>2,'idGestion'=>2,'codigoG'=>3,'motivo_rechazo'=>null,'promedio'=>null,'resultado'=>null,'codCarreraAsignada'=>null,'estado_admision'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['fecha'=>'2026-01-29','estado'=>'Pendiente','idPost'=>4,'idGestion'=>2,'codigoG'=>3,'motivo_rechazo'=>null,'promedio'=>null,'resultado'=>null,'codCarreraAsignada'=>null,'estado_admision'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['fecha'=>'2026-01-30','estado'=>'Pendiente','idPost'=>7,'idGestion'=>2,'codigoG'=>3,'motivo_rechazo'=>null,'promedio'=>null,'resultado'=>null,'codCarreraAsignada'=>null,'estado_admision'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['fecha'=>'2026-01-25','estado'=>'Pendiente','idPost'=>9,'idGestion'=>2,'codigoG'=>3,'motivo_rechazo'=>null,'promedio'=>null,'resultado'=>null,'codCarreraAsignada'=>null,'estado_admision'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['fecha'=>'2026-01-26','estado'=>'Pendiente','idPost'=>10,'idGestion'=>2,'codigoG'=>3,'motivo_rechazo'=>null,'promedio'=>null,'resultado'=>null,'codCarreraAsignada'=>null,'estado_admision'=>null,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // =========================================================
        // 28. CARRERA_INSCRITOS (2 prioridades por inscripción)
        // =========================================================
        DB::table('carrera__inscritos')->insert([
            // insc 1 (Diego)
            ['idInscripcion'=>1,'codCarrera'=>1,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>1,'codCarrera'=>2,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            // insc 2 (Valeria)
            ['idInscripcion'=>2,'codCarrera'=>2,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>2,'codCarrera'=>1,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            // insc 3 (Raúl)
            ['idInscripcion'=>3,'codCarrera'=>2,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>3,'codCarrera'=>1,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            // insc 4 (Claudia)
            ['idInscripcion'=>4,'codCarrera'=>1,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>4,'codCarrera'=>3,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            // insc 5 (Fernando)
            ['idInscripcion'=>5,'codCarrera'=>1,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>5,'codCarrera'=>2,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            // insc 6 (Lucía)
            ['idInscripcion'=>6,'codCarrera'=>1,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>6,'codCarrera'=>3,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            // insc 7 (José)
            ['idInscripcion'=>7,'codCarrera'=>3,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>7,'codCarrera'=>4,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            // insc 8 (Carolina)
            ['idInscripcion'=>8,'codCarrera'=>3,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>8,'codCarrera'=>2,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            // insc 9-13 (gestión 2)
            ['idInscripcion'=>9, 'codCarrera'=>1,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>9, 'codCarrera'=>2,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>10,'codCarrera'=>2,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>10,'codCarrera'=>1,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>11,'codCarrera'=>1,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>11,'codCarrera'=>3,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>12,'codCarrera'=>2,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>12,'codCarrera'=>1,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>13,'codCarrera'=>1,'prioridad'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['idInscripcion'=>13,'codCarrera'=>4,'prioridad'=>2,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // =========================================================
        // 29. NOTAS (calificaciones)
        //     idEx_materia mapping (5 materias por examen, en orden):
        //       E1: 1=Mat, 2=Fis, 3=Comp, 4=Ing, 5=Qui
        //       E2: 6=Mat, 7=Fis, 8=Comp, 9=Ing, 10=Qui
        //       E3:11=Mat,12=Fis,13=Comp,14=Ing,15=Qui
        //       E4:16=Mat,17=Fis,18=Comp,19=Ing,20=Qui
        //       E5:21=Mat,22=Fis,23=Comp,24=Ing,25=Qui
        //
        //     Puntajes máximos: Mat/30, Fis/20, Comp/25, Ing/15, Qui/10
        //     Todas las calificaciones respetan el puntaje máximo
        // =========================================================
        $notas = [
            // ── insc1 (Diego, G1) ──────────────────────────────
            // E1: 23+16+18+13+8=78
            [1,1,23.00],[1,2,16.00],[1,3,18.00],[1,4,13.00],[1,5,8.00],
            // E2: 24+15+18+12+7=76
            [1,6,24.00],[1,7,15.00],[1,8,18.00],[1,9,12.00],[1,10,7.00],
            // E3: 21+13+19+11+8=72
            [1,11,21.00],[1,12,13.00],[1,13,19.00],[1,14,11.00],[1,15,8.00],

            // ── insc2 (Valeria, G1) ────────────────────────────
            // E1: 15+9+10+8+5=47
            [2,1,15.00],[2,2,9.00],[2,3,10.00],[2,4,8.00],[2,5,5.00],
            // E2: 14+10+11+8+4=47
            [2,6,14.00],[2,7,10.00],[2,8,11.00],[2,9,8.00],[2,10,4.00],
            // E3: 12+7+11+7+5=42
            [2,11,12.00],[2,12,7.00],[2,13,11.00],[2,14,7.00],[2,15,5.00],

            // ── insc3 (Raúl, G1) ──────────────────────────────
            // E1: 27+17+22+14+9=89
            [3,1,27.00],[3,2,17.00],[3,3,22.00],[3,4,14.00],[3,5,9.00],
            // E2: 26+18+21+13+9=87
            [3,6,26.00],[3,7,18.00],[3,8,21.00],[3,9,13.00],[3,10,9.00],
            // E3: 25+17+22+13+9=86
            [3,11,25.00],[3,12,17.00],[3,13,22.00],[3,14,13.00],[3,15,9.00],

            // ── insc4 (Claudia, G1) ────────────────────────────
            // E1: 18+13+15+9+7=62
            [4,1,18.00],[4,2,13.00],[4,3,15.00],[4,4,9.00],[4,5,7.00],
            // E2: 19+12+16+9+6=62
            [4,6,19.00],[4,7,12.00],[4,8,16.00],[4,9,9.00],[4,10,6.00],
            // E3: 15+11+14+9+6=55
            [4,11,15.00],[4,12,11.00],[4,13,14.00],[4,14,9.00],[4,15,6.00],

            // ── insc5 (Fernando, G2) ───────────────────────────
            // E1: 22+14+18+11+7=72
            [5,1,22.00],[5,2,14.00],[5,3,18.00],[5,4,11.00],[5,5,7.00],
            // E2: 21+14+18+11+7=71
            [5,6,21.00],[5,7,14.00],[5,8,18.00],[5,9,11.00],[5,10,7.00],
            // E3: 20+14+17+10+7=68
            [5,11,20.00],[5,12,14.00],[5,13,17.00],[5,14,10.00],[5,15,7.00],

            // ── insc6 (Lucía, G2) ──────────────────────────────
            // E1: 28+18+23+14+9=92
            [6,1,28.00],[6,2,18.00],[6,3,23.00],[6,4,14.00],[6,5,9.00],
            // E2: 26+19+22+14+9=90
            [6,6,26.00],[6,7,19.00],[6,8,22.00],[6,9,14.00],[6,10,9.00],
            // E3: 27+17+23+13+9=89
            [6,11,27.00],[6,12,17.00],[6,13,23.00],[6,14,13.00],[6,15,9.00],

            // ── insc7 (José, G2) ───────────────────────────────
            // E1: 11+8+10+6+4=39
            [7,1,11.00],[7,2,8.00],[7,3,10.00],[7,4,6.00],[7,5,4.00],
            // E2: 12+7+10+6+4=39
            [7,6,12.00],[7,7,7.00],[7,8,10.00],[7,9,6.00],[7,10,4.00],
            // E3: 11+8+9+5+4=37
            [7,11,11.00],[7,12,8.00],[7,13,9.00],[7,14,5.00],[7,15,4.00],

            // ── insc8 (Carolina, G2) ───────────────────────────
            // E1: 20+13+16+10+7=66
            [8,1,20.00],[8,2,13.00],[8,3,16.00],[8,4,10.00],[8,5,7.00],
            // E2: 21+13+17+10+7=68
            [8,6,21.00],[8,7,13.00],[8,8,17.00],[8,9,10.00],[8,10,7.00],
            // E3: 19+12+16+9+7=63
            [8,11,19.00],[8,12,12.00],[8,13,16.00],[8,14,9.00],[8,15,7.00],

            // ── insc9-13 (G3, gestión 2) — solo E4 rendido ────
            // insc9  (Valeria): 16+10+12+8+5=51
            [9,16,16.00],[9,17,10.00],[9,18,12.00],[9,19,8.00],[9,20,5.00],
            // insc10 (Claudia): 20+13+16+9+7=65
            [10,16,20.00],[10,17,13.00],[10,18,16.00],[10,19,9.00],[10,20,7.00],
            // insc11 (José): 14+9+12+7+5=47
            [11,16,14.00],[11,17,9.00],[11,18,12.00],[11,19,7.00],[11,20,5.00],
            // insc12 (Miguel): 22+15+18+11+7=73
            [12,16,22.00],[12,17,15.00],[12,18,18.00],[12,19,11.00],[12,20,7.00],
            // insc13 (Patricia): 18+12+16+10+6=62
            [13,16,18.00],[13,17,12.00],[13,18,16.00],[13,19,10.00],[13,20,6.00],
        ];

        $notasRows = [];
        foreach ($notas as [$idInscripcion, $idExMateria, $calificacion]) {
            $notasRows[] = [
                'idInscripcion' => $idInscripcion,
                'idEx_materia'  => $idExMateria,
                'calificacion'  => $calificacion,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }
        DB::table('notas')->insert($notasRows);

        // =========================================================
        // 30. ASISTENCIAS + DETALLE_ASISTENCIAS
        // =========================================================
        DB::table('asistencias')->insert([
            ['fecha' => '2024-02-05', 'observacion' => null,             'codigoG' => 1, 'codigoDoc' => 1, 'created_at' => now(), 'updated_at' => now()], // id=1
            ['fecha' => '2024-02-07', 'observacion' => null,             'codigoG' => 1, 'codigoDoc' => 2, 'created_at' => now(), 'updated_at' => now()], // id=2
            ['fecha' => '2024-02-05', 'observacion' => 'Grupo reducido', 'codigoG' => 2, 'codigoDoc' => 1, 'created_at' => now(), 'updated_at' => now()], // id=3
            ['fecha' => '2024-02-06', 'observacion' => null,             'codigoG' => 2, 'codigoDoc' => 2, 'created_at' => now(), 'updated_at' => now()], // id=4
            ['fecha' => '2026-02-03', 'observacion' => null,             'codigoG' => 3, 'codigoDoc' => 1, 'created_at' => now(), 'updated_at' => now()], // id=5
            ['fecha' => '2026-02-05', 'observacion' => null,             'codigoG' => 3, 'codigoDoc' => 3, 'created_at' => now(), 'updated_at' => now()], // id=6
        ]);

        // Detalle de asistencias
        // Asistencia 1: G1 (post 1-4)
        $detalles = [
            // idAsistencia, idPost, estado
            [1, 1, 'presente'], [1, 2, 'presente'], [1, 3, 'presente'], [1, 4, 'tardanza'],
            // Asistencia 2: G1 (post 1-4)
            [2, 1, 'presente'], [2, 2, 'ausente'],  [2, 3, 'presente'], [2, 4, 'presente'],
            // Asistencia 3: G2 (post 5-8)
            [3, 5, 'presente'], [3, 6, 'presente'], [3, 7, 'ausente'],  [3, 8, 'presente'],
            // Asistencia 4: G2 (post 5-8)
            [4, 5, 'tardanza'], [4, 6, 'presente'], [4, 7, 'ausente'],  [4, 8, 'presente'],
            // Asistencia 5: G3 (post 2,4,7,9,10)
            [5, 2, 'presente'], [5, 4, 'presente'], [5, 7, 'tardanza'], [5, 9, 'presente'], [5, 10, 'presente'],
            // Asistencia 6: G3 (post 2,4,7,9,10)
            [6, 2, 'ausente'],  [6, 4, 'presente'], [6, 7, 'ausente'],  [6, 9, 'presente'], [6, 10, 'tardanza'],
        ];

        $detalleRows = [];
        foreach ($detalles as [$idAsistencia, $idPost, $estado]) {
            $detalleRows[] = [
                'idAsistencia' => $idAsistencia,
                'idPost'       => $idPost,
                'estado'       => $estado,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }
        DB::table('detalle_asistencias')->insert($detalleRows);
    }
}
