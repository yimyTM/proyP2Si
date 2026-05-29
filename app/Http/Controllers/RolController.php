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

        // Todos los permisos agrupados por categoría para las checkboxes
        $permisosPorCategoria = Permiso::orderBy('categoria')
            ->orderBy('nombrePermiso')
            ->get()
            ->groupBy('categoria');

        // Total de permisos para la barra de progreso visual
        $totalPermisos = Permiso::count();

        return view('admin.roles.index', compact('roles', 'permisosPorCategoria', 'totalPermisos'));
    }

    /**
     * Sincroniza los permisos de un rol con los seleccionados en el formulario.
     *
     * sync() → desvincula los que NO estén en la lista y vincula los nuevos.
     * Esto garantiza que el estado final del rol coincide exactamente con
     * lo que el administrador marcó, sin duplicados ni permisos fantasma.
     */
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
