<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\DetalleAsistencia;
use App\Models\Grupo;
use App\Models\Postulante;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AsistenciaController extends Controller
{
    /**
     * Muestra los grupos asignados al docente autenticado.
     * El docente selecciona un grupo para iniciar la lista de asistencia.
     */
    public function index(): View
    {
        $docente = Auth::user()->docente;

        $grupos = $docente
            ? $docente->grupos()->with(['modalidad', 'turno', 'horarios', 'materias'])->get()
            : collect();

        return view('docente.asistencia.index', compact('grupos', 'docente'));
    }

    /**
     * Muestra el formulario de toma de asistencia para un grupo específico.
     *
     * Lista de postulantes: obtiene todos los postulantes con inscripción activa
     * en la gestión abierta. Si no hay gestión abierta, muestra aviso.
     * (En una implementación final, se filtraría por estudiantes asignados al grupo).
     */
    public function tomar(Grupo $grupo): View|RedirectResponse
    {
        $docente = Auth::user()->docente;

        if (! $docente || ! $grupo->docentes->contains('codigoDoc', $docente->codigoDoc)) {
            return redirect()->route('docente.asistencia.index')
                ->with('error', 'No tienes acceso a este grupo.');
        }

        // Obtener postulantes con inscripción activa/pendiente
        $postulantes = Postulante::whereHas('inscripciones', function ($q) {
            $q->whereIn('estado', ['activa', 'pendiente', 'validada']);
        })->orderBy('apellidos')->get();

        // Sesiones previas de este grupo
        $sesionesAnteriores = Asistencia::where('codigoG', $grupo->codigoG)
            ->where('codigoDoc', $docente->codigoDoc)
            ->orderBy('fecha', 'desc')
            ->with('detalles')
            ->take(10)
            ->get();

        return view('docente.asistencia.tomar', compact('grupo', 'postulantes', 'docente', 'sesionesAnteriores'));
    }

    /**
     * Guarda la sesión de asistencia y el detalle por postulante.
     *
     * Estructura del formulario:
     *   fecha          : date
     *   observacion    : text (opcional)
     *   codigoG        : int
     *   asistencia[idPost] = 'presente' | 'ausente' | 'tardanza'
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'codigoG'    => ['required', 'integer', 'exists:grupos,codigoG'],
            'fecha'      => ['required', 'date'],
            'observacion'=> ['nullable', 'string', 'max:500'],
        ]);

        $docente = Auth::user()->docente;

        if (! $docente) {
            return back()->with('error', 'No tienes perfil de docente asociado.');
        }

        $asistencias = $request->input('asistencia', []);

        DB::transaction(function () use ($request, $docente, $asistencias) {
            $sesion = Asistencia::create([
                'fecha'       => $request->fecha,
                'observacion' => $request->observacion,
                'codigoG'     => $request->codigoG,
                'codigoDoc'   => $docente->codigoDoc,
            ]);

            foreach ($asistencias as $idPost => $estado) {
                if (in_array($estado, ['presente', 'ausente', 'tardanza'])) {
                    DetalleAsistencia::create([
                        'idAsistencia' => $sesion->idAsistencia,
                        'idPost'       => (int) $idPost,
                        'estado'       => $estado,
                    ]);
                }
            }
        });

        $grupo = Grupo::find($request->codigoG);
        BitacoraService::registrar(
            "Asistencia registrada — Grupo #{$request->codigoG} · {$request->fecha} · " .
            count($asistencias) . " alumno(s) marcados."
        );

        return redirect()->route('docente.asistencia.tomar', $request->codigoG)
            ->with('success', "Asistencia del {$request->fecha} guardada correctamente.");
    }

    // Sin uso directo
    public function create()  {}
    public function show(Asistencia $asistencia) {}
    public function edit(Asistencia $asistencia)  {}
    public function update(Request $request, Asistencia $asistencia) {}
    public function destroy(Asistencia $asistencia) {}
}
