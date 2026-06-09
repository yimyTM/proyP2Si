<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Materia;
use App\Models\materi_grupo;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MateriGrupoController extends Controller
{
    // CU07: Agregar asignación materia-horario-aula-docente a un grupo
    public function store(Request $request, Grupo $grupo): RedirectResponse
    {
        $data = $request->validate([
            'idMateria'  => ['required', 'integer', 'exists:materias,idMateria'],
            'idHorario'  => ['required', 'integer', 'exists:horarios,idHorario'],
            'idAula'     => ['required', 'integer', 'exists:aulas,idAula'],
            'codigoDoc'  => ['required', 'integer', 'exists:docentes,codigoDoc'],
        ], [
            'idHorario.required' => 'El campo horario es obligatorio para activar el grupo.',
        ]);

        // ── Materia ya asignada en este grupo ─────────────────────────────────
        $yaExiste = DB::table('materi_grupos')
            ->where('codigoG',   $grupo->codigoG)
            ->where('idMateria', $data['idMateria'])
            ->exists();

        if ($yaExiste) {
            return back()->withInput()
                ->with('error', 'Este grupo ya tiene asignada esa materia.');
        }

        // ── Colisión de docente (mismo horario en cualquier grupo) ────────────
        $colisionDocente = DB::table('materi_grupos')
            ->where('idHorario', $data['idHorario'])
            ->where('codigoDoc', $data['codigoDoc'])
            ->exists();

        if ($colisionDocente) {
            return back()->withInput()->withErrors([
                'codigoDoc' => 'El docente seleccionado ya tiene una clase asignada en este horario.',
            ]);
        }

        // ── Colisión de aula (misma aula, mismo horario) ──────────────────────
        $colisionAula = DB::table('materi_grupos')
            ->where('idHorario', $data['idHorario'])
            ->where('idAula',    $data['idAula'])
            ->exists();

        if ($colisionAula) {
            return back()->withInput()->withErrors([
                'idAula' => 'El aula seleccionada ya está ocupada en este horario.',
            ]);
        }

        // ── Límite de 4 grupos por docente en la gestión activa ───────────────
        $gruposConDocente = DB::table('materi_grupos')
            ->join('grupos', 'materi_grupos.codigoG', '=', 'grupos.codigoG')
            ->where('materi_grupos.codigoDoc', $data['codigoDoc'])
            ->where('grupos.idGestion', $grupo->idGestion)
            ->distinct()
            ->count('materi_grupos.codigoG');

        if ($gruposConDocente >= 4) {
            return back()->withInput()->withErrors([
                'codigoDoc' => 'El docente ha alcanzado el límite máximo de 4 grupos por gestión.',
            ]);
        }

        DB::table('materi_grupos')->insert([
            'codigoG'   => $grupo->codigoG,
            'idMateria' => $data['idMateria'],
            'idHorario' => $data['idHorario'],
            'idAula'    => $data['idAula'],
            'codigoDoc' => $data['codigoDoc'],
        ]);

        $materia = Materia::find($data['idMateria']);
        BitacoraService::registrar(
            "CU07: Asignación «{$materia?->nombMateria}» agregada al grupo «{$grupo->numero_grupo}»."
        );

        return back()->with('success', "Asignación guardada para el grupo «{$grupo->numero_grupo}».");
    }

    // CU07: Eliminar asignación por grupo + materia
    public function destroy(Grupo $grupo, Materia $materia): RedirectResponse
    {
        DB::table('materi_grupos')
            ->where('codigoG',   $grupo->codigoG)
            ->where('idMateria', $materia->idMateria)
            ->delete();

        BitacoraService::registrar(
            "CU07: Asignación «{$materia->nombMateria}» eliminada del grupo «{$grupo->numero_grupo}»."
        );

        return back()->with('success', "Asignación «{$materia->nombMateria}» eliminada.");
    }
}
