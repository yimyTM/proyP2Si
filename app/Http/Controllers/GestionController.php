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
        if (Gestion::where('estado', 'Abierta')->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe una gestión activa. Cierre la gestión actual antes de crear una nueva.');
        }

        $datos            = $request->validated();
        $datos['estado']  = 'Abierta';

        $gestion = Gestion::create($datos);

        BitacoraService::registrar("Gestión #{$gestion->idGestion} '{$gestion->nombre}' creada y abierta.");

        return redirect()
            ->route('admin.gestiones.carreras.index', $gestion)
            ->with('success', 'Gestión creada y abierta. Configure los cupos por carrera.');
    }

    public function edit(Gestion $gestion): View
    {
        return view('admin.gestiones.edit', compact('gestion'));
    }

    public function update(GestionRequest $request, Gestion $gestion): RedirectResponse
    {
        $datos       = $request->validated();
        $nuevoEstado = $datos['estado'] ?? $gestion->estado;

        if ($nuevoEstado === 'Abierta' && ! $gestion->estaAbierta()) {
            if (Gestion::where('estado', 'Abierta')->exists()) {
                return back()
                    ->withInput()
                    ->with('error', 'Ya existe una gestión activa. Cierre la gestión actual antes de abrir otra.');
            }
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

        if (Gestion::where('estado', 'Abierta')->exists()) {
            return back()->with('error', 'Ya existe una gestión activa. Cierre la gestión actual antes de abrir otra.');
        }

        $gestion->update(['estado' => 'Abierta']);

        BitacoraService::registrar("Gestión #{$gestion->idGestion} reabierta.");

        return back()->with('success', "Gestión #{$gestion->idGestion} reabierta correctamente.");
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
