<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use App\Models\Permiso;
use App\Models\Rol;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolController extends Controller
{
    public function index(): View
    {
        $roles = Rol::with('permisos')
            ->withCount('users')
            ->get();

        $modulos = Modulo::with(['permisos' => fn ($q) => $q->orderBy('idPermiso')])
            ->orderBy('idModulo')
            ->get();

        $totalPermisos = Permiso::count();

        return view('admin.roles.index', compact('roles', 'totalPermisos', 'modulos'));
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
