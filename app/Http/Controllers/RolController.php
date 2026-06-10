<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Rol;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolController extends Controller
{
    /**
     * Mapa nombre de permiso → categoría.
     *
     * La tabla `permisos` no tiene columna `categoria`, así que la
     * categorización para la vista se deriva aquí (refleja las
     * agrupaciones definidas en el seeder poblacionCompleta).
     */
    private const CATEGORIAS = [
        'iniciar_sesion'           => 'Autenticación',
        'cerrar_sesion'            => 'Autenticación',
        'recuperar_contrasena'     => 'Autenticación',

        'ver_postulantes'          => 'Postulantes',
        'registrar_postulante'     => 'Postulantes',
        'editar_postulante'        => 'Postulantes',
        'eliminar_postulante'      => 'Postulantes',
        'buscar_postulante'        => 'Postulantes',

        'realizar_pago'            => 'Inscripción / Pago',
        'ver_inscripcion'          => 'Inscripción / Pago',
        'gestionar_inscripciones'  => 'Inscripción / Pago',

        'registrar_notas'          => 'Exámenes / Notas',
        'editar_notas'             => 'Exámenes / Notas',
        'ver_notas'                => 'Exámenes / Notas',
        'ver_resultado_propio'     => 'Exámenes / Notas',

        'ver_grupos'               => 'Grupos',
        'gestionar_grupos'         => 'Grupos',
        'asignar_estudiante_grupo' => 'Grupos',

        'ver_docentes'             => 'Docentes',
        'gestionar_docentes'       => 'Docentes',
        'asignar_docente_grupo'    => 'Docentes',
        'ver_carga_horaria'        => 'Docentes',
        'registrar_asistencia'     => 'Docentes',

        'ver_reportes'             => 'Reportes',
        'generar_reportes'         => 'Reportes',
        'exportar_reportes'        => 'Reportes',

        'ver_dashboard'            => 'Dashboard',

        'gestionar_usuarios'       => 'Usuarios',
        'importar_usuarios_csv'    => 'Usuarios',
        'ver_perfil'               => 'Usuarios',
    ];

    /**
     * Muestra todos los roles con sus permisos asignados y el listado
     * completo de permisos disponibles agrupados por categoría.
     *
     * NO incluye lógica para crear ni eliminar permisos — son predefinidos.
     */
    public function index(): View
    {
        $roles = Rol::with('permisos')
            ->withCount('users')
            ->get();

        $todosLosPermisos = Permiso::orderBy('idPermiso')->get();

        // Agrupar por categoría derivada (la BD no almacena la categoría).
        $permisosPorCategoria = $todosLosPermisos->groupBy(
            fn ($permiso) => self::CATEGORIAS[$permiso->nombrePermiso] ?? 'Otros'
        );

        // Total de permisos para la barra de progreso visual
        $totalPermisos = $todosLosPermisos->count();

        return view('admin.roles.index', compact('roles', 'totalPermisos', 'permisosPorCategoria'));
    }

    public function actualizarPermisos(Request $request, Rol $rol): RedirectResponse
    {
        $request->validate([
            'permisos'   => ['nullable', 'array'],
            'permisos.*' => ['integer', 'exists:permisos,idPermiso'],
        ], [
            'permisos.*.exists' => 'Uno o más permisos seleccionados no son válidos.',
        ]);

        $idsSeleccionados = $request->input('permisos', []);

        $rol->permisos()->sync($idsSeleccionados);

        BitacoraService::registrar(
            "Permisos del rol '{$rol->nombre_Rol}' actualizados. " .
            "Permisos asignados: " . count($idsSeleccionados) . "."
        );

        return back()
            ->with('success', "Permisos del rol «{$rol->nombre_Rol}» actualizados correctamente.")
            ->with('rolActivo', $rol->idRol);
    }

    // ── Métodos del resource no usados (mantenidos para compatibilidad) ───────
    public function create()  {}
    public function store(Request $request) {}
    public function show(Rol $rol)  {}
    public function edit(Rol $rol)  {}
    public function update(Request $request, Rol $rol) {}
    public function destroy(Rol $rol) {}
}
