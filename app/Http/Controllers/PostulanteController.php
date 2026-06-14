<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostulanteRequest;
use App\Models\Carrera;
use App\Models\Carrera_Inscrito;
use App\Models\Gestion;
use App\Models\Inscripcion;
use App\Models\Pago;
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

    // ── Gestión de pago e inscripción (Admin) ─────────────────────────────────

    public function gestionarPago(Request $request, Postulante $postulante): RedirectResponse
    {
        $request->validate(['estado_pago' => ['required', 'in:aprobado,pendiente']]);

        $nuevoEstado   = $request->estado_pago;
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();

        // Si se va a aprobar y no tiene inscripción, la carrera es obligatoria
        $inscripcionExistente = $gestionActiva
            ? Inscripcion::where('idPost', $postulante->idPost)
                ->where('idGestion', $gestionActiva->idGestion)
                ->first()
            : null;

        if ($nuevoEstado === 'aprobado' && $gestionActiva && ! $inscripcionExistente) {
            $request->validate([
                'carrera_primera'  => ['required', 'exists:carreras,codCarrera'],
                'carrera_segunda'  => ['nullable', 'exists:carreras,codCarrera', 'different:carrera_primera'],
            ], [
                'carrera_primera.required' => 'Seleccione la carrera de primera opción para inscribir al postulante.',
            ]);
        }

        // Crear o actualizar pago
        $pago = $postulante->pagos()->latest()->first();
        if ($pago) {
            $pago->update(['estado' => $nuevoEstado]);
        } else {
            $postulante->pagos()->create([
                'monto'  => 150,
                'fecha'  => now()->toDateString(),
                'estado' => $nuevoEstado,
            ]);
        }

        $msg = $nuevoEstado === 'aprobado' ? 'Pago aprobado.' : 'Pago marcado como pendiente.';

        if ($nuevoEstado === 'aprobado' && $gestionActiva) {
            if (! $inscripcionExistente) {
                // Crear inscripción con carreras → directamente Validado
                $inscripcion = Inscripcion::create([
                    'fecha'     => now()->toDateString(),
                    'estado'    => 'Validado',
                    'idPost'    => $postulante->idPost,
                    'idGestion' => $gestionActiva->idGestion,
                ]);
                Carrera_Inscrito::create([
                    'prioridad'     => 1,
                    'idInscripcion' => $inscripcion->idInscripcion,
                    'codCarrera'    => $request->carrera_primera,
                ]);
                if ($request->filled('carrera_segunda')) {
                    Carrera_Inscrito::create([
                        'prioridad'     => 2,
                        'idInscripcion' => $inscripcion->idInscripcion,
                        'codCarrera'    => $request->carrera_segunda,
                    ]);
                }
                $msg .= " Inscrito en Gestión #{$gestionActiva->idGestion} con carrera asignada.";
            } elseif ($inscripcionExistente->estado === 'Pendiente') {
                $inscripcionExistente->update(['estado' => 'Validado']);
                $msg .= " Expediente validado automáticamente.";
            } else {
                $msg .= " El postulante ya tiene inscripción activa ({$inscripcionExistente->estado}).";
            }
        }

        BitacoraService::registrar("Pago de {$postulante->nombre_completo} → {$nuevoEstado}.");

        return back()->with('success', $msg);
    }

    // ── CRUD Admin ────────────────────────────────────────────────────────────

    public function create(): View
    {
        $carreras      = Carrera::with('modalidad')->orderBy('nombre')->get();
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();
        return view('admin.postulantes.create', compact('carreras', 'gestionActiva'));
    }

    public function store(PostulanteRequest $request): RedirectResponse
    {
        // Validar carreras si se proporcionaron
        if ($request->filled('carrera_primera')) {
            $request->validate([
                'carrera_primera' => ['required', 'exists:carreras,codCarrera'],
                'carrera_segunda' => ['nullable', 'exists:carreras,codCarrera', 'different:carrera_primera'],
            ], [
                'carrera_primera.exists'   => 'La 1ª carrera seleccionada no es válida.',
                'carrera_segunda.exists'   => 'La 2ª carrera seleccionada no es válida.',
                'carrera_segunda.different'=> 'La 2ª opción debe ser diferente a la 1ª.',
            ]);
        }

        $postulante = Postulante::create($request->validated());

        $passwordPlano = CuentaProvisionaService::sincronizarCuentaPostulante($postulante);

        BitacoraService::registrar("Postulante creado: {$postulante->nombre_completo} (CI: {$postulante->ci})");

        $mensaje = 'Postulante registrado correctamente.';
        if ($passwordPlano) {
            $mensaje .= " Contraseña provisional: {$passwordPlano}";
        }

        // Inscribir en la gestión activa si se eligieron carreras
        if ($request->filled('carrera_primera')) {
            $gestionActiva = Gestion::where('estado', 'Abierta')->first();
            if ($gestionActiva) {
                $inscripcion = Inscripcion::create([
                    'fecha'     => now()->toDateString(),
                    'estado'    => 'Validado',
                    'idPost'    => $postulante->idPost,
                    'idGestion' => $gestionActiva->idGestion,
                ]);
                Carrera_Inscrito::create([
                    'prioridad'     => 1,
                    'idInscripcion' => $inscripcion->idInscripcion,
                    'codCarrera'    => $request->carrera_primera,
                ]);
                if ($request->filled('carrera_segunda')) {
                    Carrera_Inscrito::create([
                        'prioridad'     => 2,
                        'idInscripcion' => $inscripcion->idInscripcion,
                        'codCarrera'    => $request->carrera_segunda,
                    ]);
                }
                $labelGestion = $gestionActiva->nombre ?: '#'.$gestionActiva->idGestion;
                $mensaje .= " Inscrito en la gestión activa ({$labelGestion}).";
            }
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
