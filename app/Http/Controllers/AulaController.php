<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AulaController extends Controller
{
    public function index(): View
    {
        $aulas = Aula::withCount('materiGrupos as asignaciones')
            ->orderBy('idAula')
            ->paginate(20);

        return view('admin.aulas.index', compact('aulas'));
    }

    public function create(): View
    {
        return view('admin.aulas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'capacidad' => ['required', 'integer', 'min:1', 'max:999'],
        ], [
            'capacidad.required' => 'El campo capacidad es obligatorio.',
            'capacidad.integer'  => 'La capacidad ingresada es inválida.',
            'capacidad.min'      => 'La capacidad debe ser al menos 1.',
            'capacidad.max'      => 'La capacidad no puede superar 999.',
        ]);

        $aula = Aula::create($data);
        BitacoraService::registrar("CU03: Aula #{$aula->idAula} creada (cap. {$aula->capacidad}).");

        return redirect()->route('admin.aulas.index')
            ->with('success', "Aula #{$aula->idAula} registrada correctamente.");
    }

    public function edit(Aula $aula): View
    {
        $asignaciones = DB::table('materi_grupos')->where('idAula', $aula->idAula)->count();
        return view('admin.aulas.edit', compact('aula', 'asignaciones'));
    }

    public function update(Request $request, Aula $aula): RedirectResponse
    {
        $data = $request->validate([
            'capacidad' => ['required', 'integer', 'min:1', 'max:999'],
        ], [
            'capacidad.required' => 'El campo capacidad es obligatorio.',
            'capacidad.integer'  => 'La capacidad ingresada es inválida.',
            'capacidad.min'      => 'La capacidad debe ser al menos 1.',
            'capacidad.max'      => 'La capacidad no puede superar 999.',
        ]);

        $aula->update($data);
        BitacoraService::registrar("CU03: Aula #{$aula->idAula} actualizada (cap. {$aula->capacidad}).");

        return redirect()->route('admin.aulas.index')
            ->with('success', "Aula #{$aula->idAula} actualizada correctamente.");
    }

    public function destroy(Aula $aula): RedirectResponse
    {
        if (DB::table('materi_grupos')->where('idAula', $aula->idAula)->exists()) {
            return back()->with('error',
                "No es posible eliminar el Aula #{$aula->idAula} porque tiene asignaciones activas en grupos."
            );
        }

        $id = $aula->idAula;
        $aula->delete();
        BitacoraService::registrar("CU03: Aula #{$id} eliminada.");

        return redirect()->route('admin.aulas.index')
            ->with('success', "Aula #{$id} eliminada correctamente.");
    }

    public function show(Aula $aula): RedirectResponse
    {
        return redirect()->route('admin.aulas.edit', $aula->idAula);
    }
}
