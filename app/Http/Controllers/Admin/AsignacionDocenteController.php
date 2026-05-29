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
        $grupos   = Grupo::with(['modalidad', 'turno', 'docentes', 'horarios', 'aulas', 'materias'])->get();
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

        // ── Sin colisiones: guardar asignación ────────────────────────────────
        DB::transaction(function () use ($grupo, $docente, $aula, $horario, $materia) {
            // Docente → Grupo (docente__grupos)
            $grupo->docentes()->syncWithoutDetaching([$docente->codigoDoc]);

            // Grupo → Horario (grupo__horarios)
            $grupo->horarios()->syncWithoutDetaching([$horario->idHorario]);

            // Grupo → Aula (grupo__aulas)
            $grupo->aulas()->syncWithoutDetaching([$aula->idAula]);

            // Materia → Grupo (materi_grupos)
            $grupo->materias()->syncWithoutDetaching([$materia->idMateria]);
        });

        BitacoraService::registrar(
            "Asignación: Docente {$docente->nombre_completo} → Grupo #{$grupo->codigoG} " .
            "| Aula #{$aula->idAula} | {$horario->dia} {$horario->hora_ini->format('H:i')}."
        );

        return back()->with('success',
            "Asignación guardada correctamente. Docente: {$docente->nombre_completo}, Grupo: #{$grupo->codigoG}."
        );
    }
}
