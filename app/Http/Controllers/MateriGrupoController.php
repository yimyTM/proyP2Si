<?php

namespace App\Http\Controllers;

use App\Models\Docente;
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

        // ── Docente debe estar contratado en la gestión del grupo (CU15) ──────
        $docente = Docente::find($data['codigoDoc']);
        if (! $docente || ! $docente->estaContratadoEn($grupo->idGestion)) {
            return back()->withInput()->withErrors([
                'codigoDoc' => 'El docente no ha sido contratado para esta gestión y no puede asignarse.',
            ]);
        }

        // ── El docente debe tener la materia ACEPTADA (solicitud de materia) ──
        $materiaAceptada = DB::table('solicitud_materias')
            ->where('codigoDoc', $data['codigoDoc'])
            ->where('idMateria', $data['idMateria'])
            ->where('estado', 'aceptado')
            ->exists();
        if (! $materiaAceptada) {
            return back()->withInput()->withErrors([
                'codigoDoc' => 'El docente no tiene una solicitud ACEPTADA para esta materia.',
            ]);
        }

        // ── Materia ya asignada en este grupo ─────────────────────────────────
        $yaExiste = DB::table('materi_grupos')
            ->where('codigoG',   $grupo->codigoG)
            ->where('idMateria', $data['idMateria'])
            ->exists();

        if ($yaExiste) {
            return back()->withInput()
                ->with('error', 'Este grupo ya tiene asignada esa materia.');
        }

        // ── Obtener datos del horario para comparar solapamiento ─────────────
        $nuevoHorario = DB::table('horarios')->where('idHorario', $data['idHorario'])->first();
        if (! $nuevoHorario) {
            return back()->withInput()->withErrors(['idHorario' => 'Horario no encontrado.']);
        }

        // ── Colisión de docente: mismo día con horario solapado ───────────────
        $colisionDocente = DB::table('materi_grupos')
            ->join('horarios', 'materi_grupos.idHorario', '=', 'horarios.idHorario')
            ->where('materi_grupos.codigoDoc', $data['codigoDoc'])
            ->where('horarios.dia', $nuevoHorario->dia)
            ->where('horarios.hora_ini', '<', $nuevoHorario->hora_fin)
            ->where('horarios.hora_fin', '>', $nuevoHorario->hora_ini)
            ->exists();

        if ($colisionDocente) {
            return back()->withInput()->withErrors([
                'codigoDoc' => 'El docente ya tiene una clase asignada en un horario que se solapa con el seleccionado.',
            ]);
        }

        // ── Colisión de aula: misma aula, mismo día, horario solapado ─────────
        $colisionAula = DB::table('materi_grupos')
            ->join('horarios', 'materi_grupos.idHorario', '=', 'horarios.idHorario')
            ->where('materi_grupos.idAula', $data['idAula'])
            ->where('horarios.dia', $nuevoHorario->dia)
            ->where('horarios.hora_ini', '<', $nuevoHorario->hora_fin)
            ->where('horarios.hora_fin', '>', $nuevoHorario->hora_ini)
            ->exists();

        if ($colisionAula) {
            return back()->withInput()->withErrors([
                'idAula' => 'El aula ya está ocupada en un horario que se solapa con el seleccionado.',
            ]);
        }

        // ── Colisión del grupo: el mismo grupo no puede tener dos materias solapadas
        $colisionGrupo = DB::table('materi_grupos')
            ->join('horarios', 'materi_grupos.idHorario', '=', 'horarios.idHorario')
            ->where('materi_grupos.codigoG', $grupo->codigoG)
            ->where('horarios.dia', $nuevoHorario->dia)
            ->where('horarios.hora_ini', '<', $nuevoHorario->hora_fin)
            ->where('horarios.hora_fin', '>', $nuevoHorario->hora_ini)
            ->exists();

        if ($colisionGrupo) {
            return back()->withInput()
                ->with('error', 'El grupo ya tiene una materia asignada en un horario que se solapa con el seleccionado.');
        }

        // ── Límite de 4 grupos por docente en la misma gestión ───────────────
        $gruposConDocente = DB::table('materi_grupos')
            ->join('grupos', 'materi_grupos.codigoG', '=', 'grupos.codigoG')
            ->where('materi_grupos.codigoDoc', $data['codigoDoc'])
            ->where('grupos.idGestion', $grupo->idGestion)
            ->distinct()
            ->count('materi_grupos.codigoG');

        if ($gruposConDocente >= 4) {
            return back()->withInput()->withErrors([
                'codigoDoc' => 'El docente ya tiene el máximo de 4 grupos asignados en esta gestión.',
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
