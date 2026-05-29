<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\Modalidad;
use App\Models\Turno;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GrupoController extends Controller
{
    public function index(): View
    {
        $grupos = Grupo::with(['modalidad', 'turno', 'horarios', 'aulas', 'materias', 'docentes'])
            ->orderBy('codigoG', 'desc')
            ->paginate(15);

        return view('admin.grupos.index', compact('grupos'));
    }

    public function create(): View
    {
        return view('admin.grupos.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'capacidad'     => ['required', 'integer', 'min:1', 'max:200'],
            'codeModalidad' => ['required', 'integer', 'exists:modalidads,codeModalidad'],
            'idTurno'       => ['required', 'integer', 'exists:turnos,idTurno'],
            'idHorario'     => ['nullable', 'integer', 'exists:horarios,idHorario'],
            'idAula'        => ['nullable', 'integer', 'exists:aulas,idAula'],
            'idMateria'     => ['nullable', 'integer', 'exists:materias,idMateria'],
            'codigoDoc'     => ['nullable', 'integer', 'exists:docentes,codigoDoc'],
        ]);

        DB::transaction(function () use ($data) {
            $grupo = Grupo::create([
                'capacidad'     => $data['capacidad'],
                'codeModalidad' => $data['codeModalidad'],
                'idTurno'       => $data['idTurno'],
            ]);

            if (! empty($data['idHorario']))  { $grupo->horarios()->sync([$data['idHorario']]); }
            if (! empty($data['idAula']))     { $grupo->aulas()->sync([$data['idAula']]); }
            if (! empty($data['idMateria']))  { $grupo->materias()->sync([$data['idMateria']]); }
            if (! empty($data['codigoDoc'])) { $grupo->docentes()->sync([$data['codigoDoc']]); }
        });

        BitacoraService::registrar("Grupo creado manualmente (cap. {$data['capacidad']}).");

        return redirect()->route('admin.grupos.index')->with('success', 'Grupo creado correctamente.');
    }

    public function edit(Grupo $grupo): View
    {
        $grupo->load(['horarios', 'aulas', 'materias', 'docentes']);

        return view('admin.grupos.edit', array_merge($this->formData(), [
            'grupo'         => $grupo,
            'horarioActual' => $grupo->horarios->first()?->idHorario,
            'aulaActual'    => $grupo->aulas->first()?->idAula,
            'materiaActual' => $grupo->materias->first()?->idMateria,
            'docenteActual' => $grupo->docentes->first()?->codigoDoc,
        ]));
    }

    public function update(Request $request, Grupo $grupo): RedirectResponse
    {
        $data = $request->validate([
            'capacidad'     => ['required', 'integer', 'min:1', 'max:200'],
            'codeModalidad' => ['required', 'integer', 'exists:modalidads,codeModalidad'],
            'idTurno'       => ['required', 'integer', 'exists:turnos,idTurno'],
            'idHorario'     => ['nullable', 'integer', 'exists:horarios,idHorario'],
            'idAula'        => ['nullable', 'integer', 'exists:aulas,idAula'],
            'idMateria'     => ['nullable', 'integer', 'exists:materias,idMateria'],
            'codigoDoc'     => ['nullable', 'integer', 'exists:docentes,codigoDoc'],
        ]);

        DB::transaction(function () use ($data, $grupo) {
            $grupo->update([
                'capacidad'     => $data['capacidad'],
                'codeModalidad' => $data['codeModalidad'],
                'idTurno'       => $data['idTurno'],
            ]);

            $grupo->horarios()->sync(! empty($data['idHorario']) ? [$data['idHorario']] : []);
            $grupo->aulas()->sync(! empty($data['idAula'])       ? [$data['idAula']]    : []);
            $grupo->materias()->sync(! empty($data['idMateria']) ? [$data['idMateria']] : []);
            $grupo->docentes()->sync(! empty($data['codigoDoc']) ? [$data['codigoDoc']] : []);
        });

        BitacoraService::registrar("Grupo #{$grupo->codigoG} actualizado.");

        return redirect()->route('admin.grupos.index')
            ->with('success', "Grupo #{$grupo->codigoG} actualizado correctamente.");
    }

    public function destroy(Grupo $grupo): RedirectResponse
    {
        $id = $grupo->codigoG;
        $grupo->delete();
        BitacoraService::registrar("Grupo #{$id} eliminado.");
        return redirect()->route('admin.grupos.index')->with('success', "Grupo #{$id} eliminado.");
    }

    // ── Apertura automática (conservada para compatibilidad, ya no en sidebar) ─
    public function apertura(): View
    {
        $gestiones = \App\Models\Gestion::orderBy('fecha_ini', 'desc')->get();
        $turnos    = Turno::all();
        return view('admin.apertura_grupos', compact('gestiones', 'turnos'));
    }

    public function calcularApertura(Request $request): RedirectResponse
    {
        return redirect()->route('admin.grupos.index')
            ->with('error', 'La apertura automática fue reemplazada por el CRUD manual de grupos.');
    }

    // ── Helper ────────────────────────────────────────────────────────────────
    private function formData(): array
    {
        return [
            'modalidades' => Modalidad::orderBy('nombModalidad')->get(),
            'turnos'      => Turno::orderBy('nombTurno')->get(),
            'horarios'    => Horario::orderBy('dia')->orderBy('hora_ini')->get(),
            'aulas'       => Aula::orderBy('idAula')->get(),
            'materias'    => Materia::orderBy('nombMateria')->get(),
            'docentes'    => Docente::orderBy('apellido')->get(),
        ];
    }

    public function show(Grupo $grupo) {}
}
