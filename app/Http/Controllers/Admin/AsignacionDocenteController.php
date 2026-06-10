<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AsignacionDocenteRequest;
use App\Models\Aula;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Materia;
use App\Services\BitacoraService;
use App\Services\ColisionHorariosService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AsignacionDocenteController extends Controller
{
    /** Muestra el formulario de asignación. */
    public function index(): View
    {
        $grupos   = Grupo::with([
            'modalidad', 'turno',
            'materiGrupos' => fn($q) => $q->with('materia', 'horario', 'aula', 'docente'),
        ])->get();
        $docentes = Docente::orderBy('apellido')->get();
        $aulas    = Aula::orderBy('idAula')->get();
        $horarios = Horario::orderBy('dia')->orderBy('hora_ini')->get();
        $materias = Materia::orderBy('nombMateria')->get();

        return view('admin.asignacion_docente', compact('grupos', 'docentes', 'aulas', 'horarios', 'materias'));
    }

    /**
     * CU10 – Guarda la asignación tras verificar que no existan colisiones.
     *
     * Flujo:
     *  1. Validar el formulario (AsignacionDocenteRequest).
     *  2. Cargar entidades.
     *  3. Ejecutar ColisionHorariosService::verificarTodo().
     *  4a. Si hay colisiones → devolver error con detalle exacto de cada choque.
     *  4b. Sin colisiones → guardar en las tablas pivot y registrar en bitácora.
     */
    public function store(AsignacionDocenteRequest $request): RedirectResponse
    {
        $grupo   = Grupo::findOrFail($request->codigoG);
        $docente = Docente::findOrFail($request->codigoDoc);
        $aula    = Aula::findOrFail($request->idAula);
        $horario = Horario::findOrFail($request->idHorario);
        $materia = Materia::findOrFail($request->idMateria);

        // ── Docente debe estar contratado en la gestión del grupo (CU15) ──────
        if (! $docente->estaContratadoEn($grupo->idGestion)) {
            return back()->withInput()->withErrors([
                'codigoDoc' => 'El docente no puede asignarse: no ha sido contratado para esta gestión.',
            ]);
        }

        // ── Verificar colisiones ANTES de guardar ─────────────────────────────
        $colisiones = ColisionHorariosService::verificarTodo(
            $docente,
            $aula,
            $horario,
            excludeGrupo: $grupo->codigoG
        );

        if (! empty($colisiones)) {
            return back()
                ->withErrors(['colision' => $colisiones])
                ->withInput();
        }

        // ── Duplicado: misma materia ya asignada en este grupo ────────────────
        $yaExiste = DB::table('materi_grupos')
            ->where('codigoG',   $grupo->codigoG)
            ->where('idMateria', $materia->idMateria)
            ->exists();

        if ($yaExiste) {
            return back()->withInput()
                ->with('error', 'Este grupo ya tiene asignada esa materia.');
        }

        // ── Límite de 4 grupos distintos por docente en la gestión ───────────
        $gruposConDocente = DB::table('materi_grupos')
            ->join('grupos', 'materi_grupos.codigoG', '=', 'grupos.codigoG')
            ->where('materi_grupos.codigoDoc', $docente->codigoDoc)
            ->where('grupos.idGestion', $grupo->idGestion)
            ->distinct()
            ->count('materi_grupos.codigoG');

        if ($gruposConDocente >= 4) {
            return back()->withInput()->withErrors([
                'codigoDoc' => 'El docente ha alcanzado el límite máximo de 4 grupos por gestión.',
            ]);
        }

        // ── Sin colisiones ni duplicados: guardar en materi_grupos ────────────
        DB::table('materi_grupos')->insert([
            'codigoG'   => $grupo->codigoG,
            'idMateria' => $materia->idMateria,
            'idHorario' => $horario->idHorario,
            'idAula'    => $aula->idAula,
            'codigoDoc' => $docente->codigoDoc,
        ]);

        BitacoraService::registrar(
            "Asignación: Docente {$docente->nombre_completo} → Grupo #{$grupo->codigoG} " .
            "| Aula #{$aula->idAula} | {$horario->dia} {$horario->hora_ini->format('H:i')}."
        );

        return back()->with('success',
            "Asignación guardada correctamente. Docente: {$docente->nombre_completo}, Grupo: #{$grupo->codigoG}."
        );
    }
}
