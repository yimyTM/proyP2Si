<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Docente;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\requisito;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\Modalidad;
use App\Models\Turno;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GrupoController extends Controller
{
    public function index(): View
    {
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();

        $grupos = Grupo::with([
            'modalidad', 'turno',
            'materiGrupos.materia',
            'materiGrupos.horario',
            'materiGrupos.aula',
            'materiGrupos.docente',
        ])->when($gestionActiva, fn($q) => $q->where('idGestion', $gestionActiva->idGestion))
          ->orderBy('numero_grupo')
          ->paginate(20);

        return view('admin.grupos.index', compact('grupos', 'gestionActiva'));
    }

    public function create(): View|RedirectResponse
    {
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();

        if (! $gestionActiva) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'Debe existir una gestión activa para crear grupos.');
        }

        return view('admin.grupos.create', array_merge($this->formData(), compact('gestionActiva')));
    }

    public function store(Request $request): RedirectResponse
    {
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();

        if (! $gestionActiva) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'Debe existir una gestión activa para crear grupos.');
        }

        $data = $request->validate([
            'numero_grupo'  => [
                'required', 'string', 'max:50',
                Rule::unique('grupos')->where(fn($q) => $q->where('idGestion', $gestionActiva->idGestion)),
            ],
            'capacidad'     => ['required', 'integer', 'min:1', 'max:500'],
            'codeModalidad' => ['required', 'integer', 'exists:modalidads,codeModalidad'],
            'idTurno'       => ['required', 'integer', 'exists:turnos,idTurno'],
        ], [
            'numero_grupo.unique' => 'Ya existe un grupo con este número en la gestión actual.',
        ]);

        Grupo::create([
            'numero_grupo'  => $data['numero_grupo'],
            'capacidad'     => $data['capacidad'],
            'codeModalidad' => $data['codeModalidad'],
            'idTurno'       => $data['idTurno'],
            'idGestion'     => $gestionActiva->idGestion,
        ]);

        BitacoraService::registrar("CU06: Grupo «{$data['numero_grupo']}» creado en gestión {$gestionActiva->idGestion}.");

        return redirect()->route('admin.grupos.index')
            ->with('success', "Grupo «{$data['numero_grupo']}» creado correctamente.");
    }

    public function edit(Grupo $grupo): View
    {
        $grupo->load(['materiGrupos.materia', 'materiGrupos.horario', 'materiGrupos.aula', 'materiGrupos.docente']);

        $totalRequisitosD = requisito::where('tipo', 'D')->where('obligatorio', true)->count();

        $docentesHabilitados = $totalRequisitosD > 0
            ? Docente::whereHas('requisitosDocente', function ($q) {
                $q->where('validado', true)
                  ->whereHas('requisito', fn($r) => $r->where('tipo', 'D')->where('obligatorio', true));
            }, '>=', $totalRequisitosD)->orderBy('apellido')->get()
            : Docente::orderBy('apellido')->get();

        return view('admin.grupos.edit', array_merge($this->formData(), [
            'grupo'               => $grupo,
            'docentesHabilitados' => $docentesHabilitados,
        ]));
    }

    public function update(Request $request, Grupo $grupo): RedirectResponse
    {
        $data = $request->validate([
            'numero_grupo'  => [
                'required', 'string', 'max:50',
                Rule::unique('grupos')
                    ->where(fn($q) => $q->where('idGestion', $grupo->idGestion))
                    ->ignore($grupo->codigoG, 'codigoG'),
            ],
            'capacidad'     => ['required', 'integer', 'min:1', 'max:500'],
            'codeModalidad' => ['required', 'integer', 'exists:modalidads,codeModalidad'],
            'idTurno'       => ['required', 'integer', 'exists:turnos,idTurno'],
        ], [
            'numero_grupo.unique' => 'Ya existe un grupo con este número en la gestión actual.',
        ]);

        if (! empty($data['idAula'])) {
            $aula = Aula::find($data['idAula']);
            if ($aula && $data['capacidad'] > $aula->capacidad) {
                return back()->withInput()->withErrors([
                    'capacidad' => "La capacidad ingresada ({$data['capacidad']}) supera la capacidad máxima del aula seleccionada ({$aula->capacidad} cupos).",
                ]);
            }
        }

        $grupo->update([
            'numero_grupo'  => $data['numero_grupo'],
            'capacidad'     => $data['capacidad'],
            'codeModalidad' => $data['codeModalidad'],
            'idTurno'       => $data['idTurno'],
        ]);

        BitacoraService::registrar("CU06: Grupo «{$grupo->numero_grupo}» (#{$grupo->codigoG}) actualizado.");

        return redirect()->route('admin.grupos.index')
            ->with('success', "Grupo «{$grupo->numero_grupo}» actualizado correctamente.");
    }

    public function destroy(Grupo $grupo): RedirectResponse
    {
        if ($grupo->inscripciones()->exists()) {
            return back()->with('error', 'No es posible eliminar un grupo con alumnos registrados.');
        }

        $nombre = $grupo->numero_grupo;
        $grupo->delete();
        BitacoraService::registrar("CU06: Grupo «{$nombre}» eliminado.");

        return redirect()->route('admin.grupos.index')
            ->with('success', "Grupo «{$nombre}» eliminado correctamente.");
    }

    // ── Apertura automática (conservada para compatibilidad, ya no en sidebar) ─
    public function apertura(): View
    {
        $gestiones = Gestion::orderBy('fecha_ini', 'desc')->get();
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
