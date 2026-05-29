<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsignacionDocenteRequest;
use App\Models\Aula;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Materia;
use App\Services\BitacoraService;
use App\Services\ColisionHorariosService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DocenteGrupoController extends Controller
{
    // ── CU10: Asignación docente y carga logística ────────────────────────────

    public function asignacion(): View
    {
        $grupos   = Grupo::with(['modalidad', 'turno', 'docentes', 'horarios', 'aulas', 'materias'])->get();
        $docentes = Docente::orderBy('apellido')->get();
        $aulas    = Aula::orderBy('idAula')->get();
        $horarios = Horario::orderBy('dia')->orderBy('hora_ini')->get();
        $materias = Materia::orderBy('nombMateria')->get();

        return view('admin.asignacion_docente', compact('grupos', 'docentes', 'aulas', 'horarios', 'materias'));
    }

    /**
     * Guarda la asignación tras verificar que no existan colisiones.
     * Detecta: choque de docente (mismo horario en otro grupo)
     *          choque de aula    (mismo horario, mismo espacio físico)
     */
    public function store(AsignacionDocenteRequest $request): RedirectResponse
    {
        $grupo   = Grupo::findOrFail($request->codigoG);
        $docente = Docente::findOrFail($request->codigoDoc);
        $aula    = Aula::findOrFail($request->idAula);
        $horario = Horario::findOrFail($request->idHorario);
        $materia = Materia::findOrFail($request->idMateria);

        // Verificar colisiones antes de guardar
        $colisiones = ColisionHorariosService::verificarTodo($docente, $aula, $horario, $grupo->codigoG);

        if (! empty($colisiones)) {
            return back()->withErrors(['colision' => $colisiones])->withInput();
        }

        DB::transaction(function () use ($grupo, $docente, $aula, $horario, $materia) {
            $grupo->docentes()->syncWithoutDetaching([$docente->codigoDoc]);
            $grupo->horarios()->syncWithoutDetaching([$horario->idHorario]);
            $grupo->aulas()->syncWithoutDetaching([$aula->idAula]);
            $grupo->materias()->syncWithoutDetaching([$materia->idMateria]);
        });

        BitacoraService::registrar(
            "Asignación: {$docente->nombre_completo} → Grupo #{$grupo->codigoG} | Aula #{$aula->idAula} | {$horario->dia} {$horario->hora_ini->format('H:i')}."
        );

        return back()->with('success', "Asignación guardada. Docente: {$docente->nombre_completo}, Grupo: #{$grupo->codigoG}.");
    }

    // ── CRUD base ─────────────────────────────────────────────────────────────
    public function index()  {}
    public function create() {}
    public function show(Request $request)   {}
    public function edit(Request $request)   {}
    public function update(Request $request, $id) {}
    public function destroy($id)             {}
}
