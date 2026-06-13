<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostulanteRequest;
use App\Models\Carrera;
use App\Models\Gestion;
use App\Models\Postulante;
use App\Services\BitacoraService;
use App\Services\CuentaProvisionaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PostulanteController extends Controller
{
    // ── CU01: Dashboard del Postulante ────────────────────────────────────────

    public function dashboard(): View
    {
        $user       = Auth::user();
        $postulante = $user->postulante;

        $pago            = null;
        $inscripcion     = null;
        $documentos      = collect();
        $examenes        = collect();
        $materias        = collect();
        $carreraAsignada = null;
        $opcionAsignada  = null;

        if ($postulante) {
            $pago = $postulante->pagos()->latest()->first();

            $inscripcion = $postulante->inscripciones()
                ->with([
                    'gestion',
                    'grupo',
                    'carrerasInscritas.carrera.modalidad',
                    'carreraAsignada.modalidad',
                    'notas.examMateria.examen',
                    'notas.examMateria.materia',
                ])
                ->latest('idInscripcion')
                ->first();

            $documentos = $postulante->requisitos()
                ->with('requisito')
                ->get();

            if ($inscripcion && $inscripcion->notas->isNotEmpty()) {
                $examenes = $inscripcion->notas
                    ->pluck('examMateria.examen')
                    ->unique('idExamen')
                    ->sortBy('nroParcial')
                    ->values();

                $materias = $inscripcion->notas
                    ->groupBy(fn($n) => $n->examMateria->idMateria)
                    ->map(function ($notas) {
                        $first = $notas->first();
                        return (object)[
                            'nombre'    => $first->examMateria->materia->nombMateria,
                            'parciales' => $notas->keyBy(fn($n) => $n->examMateria->examen->nroParcial),
                        ];
                    })
                    ->sortBy('nombre')
                    ->values();

                $carreraAsignada = $inscripcion->carreraAsignada;
                if ($carreraAsignada) {
                    $opcionRow      = $inscripcion->carrerasInscritas->firstWhere('codCarrera', $carreraAsignada->codCarrera);
                    $opcionAsignada = $opcionRow?->prioridad;
                }
            }
        }

        return view('postulante.dashboard', compact(
            'postulante', 'pago', 'inscripcion',
            'documentos', 'examenes', 'materias',
            'carreraAsignada', 'opcionAsignada'
        ));
    }

    // ── CU16: Consultar estado de admisión y calificaciones ───────────────────

    public function resultados(): View
    {
        $postulante = Auth::user()->postulante;

        $gestion = Gestion::where('estado', 'Abierta')->first()
                ?? Gestion::latest('idGestion')->first();

        if (!$postulante) {
            return view('postulante.resultados', ['error' => 'sin_inscripcion', 'gestion' => $gestion]);
        }

        $inscripcion = $postulante->inscripciones()
            ->when($gestion, fn($q) => $q->where('idGestion', $gestion->idGestion))
            ->latest('idInscripcion')
            ->first();

        if (!$inscripcion) {
            return view('postulante.resultados', ['error' => 'sin_inscripcion', 'gestion' => $gestion]);
        }

        if (!$postulante->tienePagoAprobado()) {
            return view('postulante.resultados', [
                'error'       => 'pago_pendiente',
                'gestion'     => $gestion,
                'inscripcion' => $inscripcion,
            ]);
        }

        $inscripcion->load([
            'notas.examMateria.examen',
            'notas.examMateria.materia',
            'carrerasInscritas.carrera',
            'carreraAsignada',
        ]);

        if ($inscripcion->notas->isEmpty()) {
            return view('postulante.resultados', [
                'error'       => 'sin_resultados',
                'gestion'     => $gestion,
                'inscripcion' => $inscripcion,
            ]);
        }

        // Examenes únicos ordenados por nroParcial
        $examenes = $inscripcion->notas
            ->pluck('examMateria.examen')
            ->unique('idExamen')
            ->sortBy('nroParcial')
            ->values();

        // Notas agrupadas por materia → por nroParcial
        $materias = $inscripcion->notas
            ->groupBy(fn($nota) => $nota->examMateria->idMateria)
            ->map(function ($notas) {
                $first = $notas->first();
                return (object)[
                    'nombre'    => $first->examMateria->materia->nombMateria,
                    'parciales' => $notas->keyBy(fn($n) => $n->examMateria->examen->nroParcial),
                ];
            })
            ->sortBy('nombre')
            ->values();

        // Carrera asignada e indicador de opción
        $carreraAsignada = $inscripcion->carreraAsignada;
        $opcionAsignada  = null;
        if ($carreraAsignada) {
            $opcionRow      = $inscripcion->carrerasInscritas->firstWhere('codCarrera', $carreraAsignada->codCarrera);
            $opcionAsignada = $opcionRow?->prioridad;
        }

        return view('postulante.resultados', compact(
            'postulante', 'gestion', 'inscripcion',
            'materias', 'examenes',
            'carreraAsignada', 'opcionAsignada'
        ));
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
            'inscripciones.carreraAsignada.modalidad',
            'inscripciones.notas.examMateria.examen',
            'inscripciones.notas.examMateria.materia',
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
