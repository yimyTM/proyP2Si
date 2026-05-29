<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostulanteRequest;
use App\Models\Carrera;
use App\Models\Postulante;
use App\Services\BitacoraService;
use App\Services\CuentaProvisionaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostulanteController extends Controller
{
    // ── CU01: Dashboard del Postulante ────────────────────────────────────────

    public function dashboard(): View
    {
        return view('postulante.dashboard');
    }

    // ── CU05: Búsqueda avanzada de estudiantes (Admin) ────────────────────────

    public function buscar(Request $request): View
    {
        $query = Postulante::with([
            'pagos',
            'inscripciones.carrerasInscritas.carrera',
            'requisitos.requisito',
        ]);

        if ($request->filled('ci'))       { $query->where('ci', $request->ci); }
        if ($request->filled('apellido')) { $query->where('apellidos', 'like', '%'.$request->apellido.'%'); }
        if ($request->filled('carrera'))  {
            $query->whereHas('inscripciones.carrerasInscritas', fn($q) => $q->where('codCarrera', $request->carrera));
        }
        if ($request->filled('estado'))   { $query->where('estado', $request->estado); }

        $postulantes = $query->orderBy('apellidos')->paginate(15)->withQueryString();
        $carreras    = Carrera::orderBy('nombre')->get();

        if ($request->anyFilled(['ci', 'apellido', 'carrera', 'estado'])) {
            BitacoraService::registrar('Búsqueda de estudiantes: '.json_encode($request->only('ci','apellido','carrera','estado')));
        }

        return view('admin.estudiantes', compact('postulantes', 'carreras'));
    }

    // ── CRUD Admin ────────────────────────────────────────────────────────────

    public function create(): View
    {
        return view('admin.postulantes.create');
    }

    public function store(PostulanteRequest $request): RedirectResponse
    {
        $postulante = Postulante::create($request->validated());

        $passwordPlano = CuentaProvisionaService::sincronizarCuentaPostulante($postulante);

        BitacoraService::registrar("Postulante creado: {$postulante->nombre_completo} (CI: {$postulante->ci})");

        $mensaje = 'Postulante registrado correctamente.';
        if ($passwordPlano) {
            $mensaje .= " Contraseña provisional: {$passwordPlano}";
        }

        return redirect()
            ->route('admin.postulantes.show', $postulante)
            ->with('success', $mensaje);
    }

    public function show(Postulante $postulante): View
    {
        $postulante->load([
            'usuario',
            'pagos',
            'inscripciones.carrerasInscritas.carrera',
            'requisitos.requisito',
        ]);

        return view('admin.postulantes.show', compact('postulante'));
    }

    public function edit(Postulante $postulante): View
    {
        return view('admin.postulantes.edit', compact('postulante'));
    }

    public function update(PostulanteRequest $request, Postulante $postulante): RedirectResponse
    {
        $postulante->update($request->validated());

        $passwordPlano = CuentaProvisionaService::sincronizarCuentaPostulante($postulante);

        BitacoraService::registrar("Postulante actualizado: {$postulante->nombre_completo} (CI: {$postulante->ci})");

        $mensaje = 'Postulante actualizado correctamente.';
        if ($passwordPlano) {
            $mensaje .= " Cuenta creada. Contraseña provisional: {$passwordPlano}";
        }

        return redirect()
            ->route('admin.postulantes.show', $postulante)
            ->with('success', $mensaje);
    }

    public function destroy(Postulante $postulante): RedirectResponse
    {
        $nombre = $postulante->nombre_completo;
        $user   = $postulante->usuario;

        $postulante->delete();

        if ($user) {
            $user->delete();
        }

        BitacoraService::registrar("Postulante eliminado: {$nombre}");

        return redirect()
            ->route('admin.estudiantes')
            ->with('success', 'Postulante eliminado correctamente.');
    }
}
