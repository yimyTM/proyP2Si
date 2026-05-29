<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TurnoController extends Controller
{
    public function index(): View
    {
        $turnos = Turno::withCount('grupos')->orderBy('nombTurno')->get();
        return view('admin.turnos.index', compact('turnos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombTurno' => ['required', 'string', 'max:50', 'unique:turnos,nombTurno'],
        ], [
            'nombTurno.required' => 'El nombre del turno es obligatorio.',
            'nombTurno.unique'   => 'Ya existe un turno con ese nombre.',
        ]);

        Turno::create(['nombTurno' => $request->nombTurno]);

        BitacoraService::registrar("Turno creado: {$request->nombTurno}.");

        return back()->with('success', "Turno «{$request->nombTurno}» creado correctamente.");
    }

    public function update(Request $request, Turno $turno): RedirectResponse
    {
        $request->validate([
            'nombTurno' => [
                'required', 'string', 'max:50',
                "unique:turnos,nombTurno,{$turno->idTurno},idTurno",
            ],
        ], [
            'nombTurno.required' => 'El nombre del turno es obligatorio.',
            'nombTurno.unique'   => 'Ya existe otro turno con ese nombre.',
        ]);

        $anterior = $turno->nombTurno;
        $turno->update(['nombTurno' => $request->nombTurno]);

        BitacoraService::registrar("Turno actualizado: «{$anterior}» → «{$request->nombTurno}».");

        return back()->with('success', "Turno actualizado correctamente.");
    }

    public function destroy(Turno $turno): RedirectResponse
    {
        if ($turno->grupos()->exists()) {
            return back()->with('error',
                "No se puede eliminar «{$turno->nombTurno}» porque tiene {$turno->grupos_count} grupo(s) asignado(s)."
            );
        }

        $nombre = $turno->nombTurno;
        $turno->delete();

        BitacoraService::registrar("Turno eliminado: «{$nombre}».");

        return back()->with('success', "Turno «{$nombre}» eliminado.");
    }

    // Sin uso en este módulo
    public function create() {}
    public function show(Turno $turno) {}
    public function edit(Turno $turno) {}
}
