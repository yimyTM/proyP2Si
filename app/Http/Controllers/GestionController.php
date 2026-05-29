<?php

namespace App\Http\Controllers;

use App\Http\Requests\GestionRequest;
use App\Models\Gestion;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GestionController extends Controller
{
    public function index(): View
    {
        $gestiones = Gestion::withCount(['inscripciones', 'carreras'])
            ->orderByDesc('fecha_ini')
            ->paginate(15);

        return view('admin.gestiones.index', compact('gestiones'));
    }

    public function create(): View
    {
        return view('admin.gestiones.create');
    }

    public function store(GestionRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = $datos['estado'] ?? 'Cerrada';

        if ($datos['estado'] === 'Abierta') {
            Gestion::where('estado', 'Abierta')->update(['estado' => 'Cerrada']);
        }

        $gestion = Gestion::create($datos);

        BitacoraService::registrar("Gestión #{$gestion->idGestion} creada ({$gestion->estado}).");

        return redirect()
            ->route('admin.gestiones.carreras.index', $gestion)
            ->with('success', 'Gestión creada. Configure los cupos por carrera.');
    }

    public function edit(Gestion $gestion): View
    {
        return view('admin.gestiones.edit', compact('gestion'));
    }

    public function update(GestionRequest $request, Gestion $gestion): RedirectResponse
    {
        $datos = $request->validated();

        if (($datos['estado'] ?? $gestion->estado) === 'Abierta') {
            Gestion::where('estado', 'Abierta')
                ->where('idGestion', '!=', $gestion->idGestion)
                ->update(['estado' => 'Cerrada']);
        }

        $gestion->update($datos);

        BitacoraService::registrar("Gestión #{$gestion->idGestion} actualizada.");

        return redirect()
            ->route('admin.gestiones.index')
            ->with('success', 'Gestión actualizada correctamente.');
    }

    public function abrir(Gestion $gestion): RedirectResponse
    {
        if ($gestion->estaAbierta()) {
            return back()->with('error', 'Esta gestión ya está abierta.');
        }

        Gestion::where('estado', 'Abierta')->update(['estado' => 'Cerrada']);
        $gestion->update(['estado' => 'Abierta']);

        BitacoraService::registrar("Gestión #{$gestion->idGestion} abierta.");

        return back()->with('success', "Gestión #{$gestion->idGestion} abierta. Las demás gestiones fueron cerradas.");
    }

    public function cerrar(Gestion $gestion): RedirectResponse
    {
        if (! $gestion->estaAbierta()) {
            return back()->with('error', 'Esta gestión ya está cerrada.');
        }

        $gestion->update(['estado' => 'Cerrada']);

        BitacoraService::registrar("Gestión #{$gestion->idGestion} cerrada.");

        return back()->with('success', "Gestión #{$gestion->idGestion} cerrada correctamente.");
    }

    public function destroy(Gestion $gestion): RedirectResponse
    {
        if ($gestion->inscripciones()->exists()) {
            return back()->with('error', 'No se puede eliminar una gestión con inscripciones registradas.');
        }

        $id = $gestion->idGestion;
        $gestion->delete();

        BitacoraService::registrar("Gestión #{$id} eliminada.");

        return redirect()
            ->route('admin.gestiones.index')
            ->with('success', 'Gestión eliminada correctamente.');
    }
}
