<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\DetalleAsistencia;
use App\Models\Gestion;
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
    // CU10: Lista de grupos asignados al docente autenticado
    public function index(): View
    {
        $docente       = Auth::user()->docente;
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();

        if (! $docente) {
            return view('docente.asistencia.index', [
                'grupos'        => collect(),
                'docente'       => null,
                'gestionActiva' => $gestionActiva,
            ]);
        }

        $grupoIds = DB::table('materi_grupos')
            ->where('codigoDoc', $docente->codigoDoc)
            ->distinct()
            ->pluck('codigoG');

        $grupos = Grupo::whereIn('codigoG', $grupoIds)
            ->when($gestionActiva, fn($q) => $q->where('idGestion', $gestionActiva->idGestion))
            ->with([
                'modalidad',
                'turno',
                'materiGrupos' => fn($q) => $q
                    ->where('codigoDoc', $docente->codigoDoc)
                    ->with('horario', 'materia'),
            ])
            ->get();

        return view('docente.asistencia.index', compact('grupos', 'docente', 'gestionActiva'));
    }

    // CU10: Formulario de toma de asistencia para un grupo
    public function tomar(Grupo $grupo): View|RedirectResponse
    {
        $docente = Auth::user()->docente;

        if (! $docente) {
            return redirect()->route('docente.asistencia.index')
                ->with('error', 'No tienes perfil de docente asociado.');
        }

        // Autorización: docente debe estar asignado a este grupo
        $autorizado = DB::table('materi_grupos')
            ->where('codigoG', $grupo->codigoG)
            ->where('codigoDoc', $docente->codigoDoc)
            ->exists();

        if (! $autorizado) {
            return redirect()->route('docente.asistencia.index')
                ->with('error', 'No tienes acceso a este grupo.');
        }

        // Cargar relaciones del grupo para este docente
        $grupo->load([
            'modalidad',
            'turno',
            'materiGrupos' => fn($q) => $q
                ->where('codigoDoc', $docente->codigoDoc)
                ->with('horario', 'materia', 'aula'),
        ]);

        // Gestión activa
        $gestionActiva  = Gestion::where('estado', 'Abierta')->first();
        $gestionCerrada = ! $gestionActiva;

        // Estudiantes del grupo (via inscripciones.codigoG)
        $postulantes = Postulante::whereHas('inscripciones', fn($q) => $q->where('codigoG', $grupo->codigoG))
            ->orderBy('apellidos')
            ->get();

        // Asistencia ya registrada hoy
        $sesionHoy = Asistencia::where('codigoG', $grupo->codigoG)
            ->where('codigoDoc', $docente->codigoDoc)
            ->whereDate('fecha', today())
            ->with('detalles')
            ->first();

        // Calcular porcentaje acumulado de asistencia por estudiante
        $totalSesiones = Asistencia::where('codigoG', $grupo->codigoG)->count();
        $porcentajes   = [];

        if ($totalSesiones > 0 && $postulantes->isNotEmpty()) {
            $conteos = DetalleAsistencia::whereHas(
                    'asistencia',
                    fn($q) => $q->where('codigoG', $grupo->codigoG)
                )
                ->whereIn('idPost', $postulantes->pluck('idPost'))
                ->whereIn('estado', ['presente', 'tardanza'])
                ->select('idPost', DB::raw('count(*) as cnt'))
                ->groupBy('idPost')
                ->pluck('cnt', 'idPost');

            foreach ($postulantes as $p) {
                $porcentajes[$p->idPost] = round(($conteos->get($p->idPost, 0) / $totalSesiones) * 100, 1);
            }
        }

        // Sesiones anteriores (sidebar)
        $sesionesAnteriores = Asistencia::where('codigoG', $grupo->codigoG)
            ->where('codigoDoc', $docente->codigoDoc)
            ->orderBy('fecha', 'desc')
            ->with('detalles')
            ->take(10)
            ->get();

        return view('docente.asistencia.tomar', compact(
            'grupo', 'postulantes', 'docente',
            'sesionesAnteriores', 'sesionHoy',
            'gestionActiva', 'gestionCerrada',
            'porcentajes', 'totalSesiones'
        ));
    }

    // CU10: Guardar (o modificar) asistencia
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'codigoG'     => ['required', 'integer', 'exists:grupos,codigoG'],
            'fecha'       => ['required', 'date', 'before_or_equal:today'],
            'observacion' => ['nullable', 'string', 'max:500'],
        ]);

        $docente = Auth::user()->docente;
        if (! $docente) {
            return back()->with('error', 'No tienes perfil de docente asociado.');
        }

        // CU10-EX: Gestión cerrada
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();
        if (! $gestionActiva) {
            return back()->with('error', 'El período académico ha concluido. No se pueden registrar nuevas asistencias.');
        }

        $esModificacion  = $request->boolean('_modificar');
        $sesionExistente = Asistencia::where('codigoG', $request->codigoG)
            ->where('codigoDoc', $docente->codigoDoc)
            ->whereDate('fecha', $request->fecha)
            ->first();

        // CU10-EX: Ya registrada — bloquear sin confirmación de modificación
        if ($sesionExistente && ! $esModificacion) {
            return redirect()->route('docente.asistencia.tomar', $request->codigoG)
                ->with('ya_registrada', true);
        }

        $asistencias = $request->input('asistencia', []);

        // CU10: Validar que todos los estudiantes tengan estado
        $totalEstudiantes = Postulante::whereHas(
            'inscripciones', fn($q) => $q->where('codigoG', $request->codigoG)
        )->count();

        $marcados = count(array_filter($asistencias, fn($e) => in_array($e, ['presente', 'ausente', 'tardanza'])));

        if ($totalEstudiantes > 0 && $marcados < $totalEstudiantes) {
            return back()->withInput()
                ->with('error', "Debe registrar el estado de todos los estudiantes ({$totalEstudiantes}) antes de guardar.");
        }

        DB::transaction(function () use ($request, $docente, $asistencias, $sesionExistente) {
            if ($sesionExistente) {
                $sesionExistente->delete(); // cascade elimina detalles
            }

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

        $tipo = $sesionExistente ? 'modificada' : 'guardada';
        BitacoraService::registrar(
            "CU10: Asistencia {$tipo} — Grupo #{$request->codigoG} · {$request->fecha} · {$marcados} alumno(s)."
        );

        return redirect()->route('docente.asistencia.tomar', $request->codigoG)
            ->with('success', "Asistencia del {$request->fecha} {$tipo} correctamente.");
    }

    public function create()  {}
    public function show(Asistencia $asistencia) {}
    public function edit(Asistencia $asistencia)  {}
    public function update(Request $request, Asistencia $asistencia) {}
    public function destroy(Asistencia $asistencia) {}
}
