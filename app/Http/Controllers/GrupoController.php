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
            'modalidad',
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
        ], [
            'numero_grupo.unique' => 'Ya existe un grupo con este número en la gestión actual.',
        ]);

        Grupo::create([
            'numero_grupo'  => $data['numero_grupo'],
            'capacidad'     => $data['capacidad'],
            'codeModalidad' => $data['codeModalidad'],
            'idGestion'     => $gestionActiva->idGestion,
        ]);

        BitacoraService::registrar("CU06: Grupo «{$data['numero_grupo']}» creado en gestión {$gestionActiva->idGestion}.");

        return redirect()->route('admin.grupos.index')
            ->with('success', "Grupo «{$data['numero_grupo']}» creado correctamente.");
    }

    public function edit(Grupo $grupo): View
    {
        $grupo->load([
            'materiGrupos.materia',
            'materiGrupos.horario',
            'materiGrupos.aula',
            'materiGrupos.docente',
            'inscripciones.postulante',
        ]);

        $totalRequisitosD = requisito::where('tipo', 'D')->where('obligatorio', true)->count();

        $docentesHabilitados = $totalRequisitosD > 0
            ? Docente::whereHas('requisitosDocente', function ($q) {
                $q->where('validado', true)
                  ->whereHas('requisito', fn($r) => $r->where('tipo', 'D')->where('obligatorio', true));
            }, '>=', $totalRequisitosD)->orderBy('apellido')->get()
            : Docente::orderBy('apellido')->get();

        $otrosGrupos = Grupo::where('idGestion', $grupo->idGestion)
            ->where('codigoG', '!=', $grupo->codigoG)
            ->with('modalidad')
            ->orderBy('numero_grupo')
            ->get()
            ->map(function ($g) {
                $g->inscritos_count = $g->inscripciones()->count();
                return $g;
            });

        return view('admin.grupos.edit', array_merge($this->formData(), [
            'grupo'               => $grupo,
            'docentesHabilitados' => $docentesHabilitados,
            'otrosGrupos'         => $otrosGrupos,
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
        ], [
            'numero_grupo.unique' => 'Ya existe un grupo con este número en la gestión actual.',
        ]);

        $grupo->update([
            'numero_grupo'  => $data['numero_grupo'],
            'capacidad'     => $data['capacidad'],
            'codeModalidad' => $data['codeModalidad'],
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

    // ── CU17: Distribuir postulantes en grupos ────────────────────────────────

    public function distribuirPreview(): View|RedirectResponse
    {
        $gestion = Gestion::where('estado', 'Abierta')->first();

        if (!$gestion) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'No hay una gestión activa. Abra una gestión antes de distribuir.');
        }

        $grupos = Grupo::where('idGestion', $gestion->idGestion)
            ->with(['modalidad'])
            ->orderBy('numero_grupo')
            ->get();

        if ($grupos->isEmpty()) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'Debe crear los grupos académicos antes de realizar la distribución (CU06).');
        }

        // Include already-assigned for redistribution preview
        $postulantes = \App\Models\Inscripcion::where('idGestion', $gestion->idGestion)
            ->whereIn('estado', ['Validado', 'Habilitado', 'Asignado a grupo'])
            ->with('postulante')
            ->whereHas('postulante')
            ->get()
            ->sortBy('postulante.apellidos')
            ->values();

        if ($postulantes->isEmpty()) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'No hay postulantes habilitados para distribuir en la gestión activa.');
        }

        $capacidadTotal   = $grupos->sum('capacidad');
        $totalPostulantes = $postulantes->count();
        $yaDistribuido    = $postulantes->contains('estado', 'Asignado a grupo');
        $capacidadOk      = $totalPostulantes <= $capacidadTotal;

        // Distribución secuencial: llena cada grupo hasta su capacidad antes de pasar al siguiente
        $distribucion = $grupos->mapWithKeys(fn($g) => [$g->codigoG => collect()]);
        if ($capacidadOk) {
            $gi = 0;
            foreach ($postulantes as $insc) {
                while ($gi < $grupos->count() - 1
                    && $distribucion[$grupos[$gi]->codigoG]->count() >= $grupos[$gi]->capacidad) {
                    $gi++;
                }
                $distribucion[$grupos[$gi]->codigoG]->push($insc);
            }
        }

        return view('admin.grupos.distribuir', compact(
            'gestion', 'grupos', 'postulantes', 'distribucion',
            'capacidadTotal', 'totalPostulantes', 'yaDistribuido', 'capacidadOk'
        ));
    }

    public function distribuirConfirmar(Request $request): RedirectResponse
    {
        $gestion = Gestion::where('estado', 'Abierta')->first();

        if (!$gestion) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'No hay una gestión activa.');
        }

        $grupos = Grupo::where('idGestion', $gestion->idGestion)
            ->orderBy('numero_grupo')
            ->get();

        if ($grupos->isEmpty()) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'No hay grupos disponibles para la distribución.');
        }

        $postulantes = \App\Models\Inscripcion::where('idGestion', $gestion->idGestion)
            ->whereIn('estado', ['Validado', 'Habilitado', 'Asignado a grupo'])
            ->whereHas('postulante')
            ->with('postulante')
            ->get()
            ->sortBy('postulante.apellidos')
            ->values();

        if ($postulantes->isEmpty()) {
            return redirect()->route('admin.grupos.index')
                ->with('error', 'No hay postulantes habilitados para distribuir.');
        }

        $capacidadTotal = $grupos->sum('capacidad');

        if ($postulantes->count() > $capacidadTotal) {
            return redirect()->route('admin.grupos.distribuir')
                ->with('error', "La cantidad de postulantes ({$postulantes->count()}) excede la capacidad total ({$capacidadTotal}). Se requiere abrir grupos adicionales.");
        }

        DB::transaction(function () use ($postulantes, $grupos) {
            $gi      = 0;
            $enGrupo = 0;
            foreach ($postulantes as $insc) {
                if ($gi < $grupos->count() - 1 && $enGrupo >= $grupos[$gi]->capacidad) {
                    $gi++;
                    $enGrupo = 0;
                }
                $insc->update([
                    'codigoG' => $grupos[$gi]->codigoG,
                    'estado'  => 'Asignado a grupo',
                ]);
                $enGrupo++;
            }
        });

        $total = $postulantes->count();
        $nGrupos = $grupos->count();

        BitacoraService::registrar(
            "CU17: {$total} postulantes distribuidos en {$nGrupos} grupos (gestión {$gestion->idGestion})."
        );

        return redirect()->route('admin.grupos.index')
            ->with('success', "Distribución completada. {$total} postulantes asignados a {$nGrupos} grupos.");
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

    // ── Mover alumno individual entre grupos ──────────────────────────────────

    public function moverAlumno(Request $request, Grupo $grupo, \App\Models\Inscripcion $inscripcion): RedirectResponse
    {
        $data = $request->validate([
            'codigoG_destino' => ['required', 'integer', 'exists:grupos,codigoG'],
        ]);

        $destino = Grupo::findOrFail($data['codigoG_destino']);

        if ((int) $inscripcion->codigoG !== $grupo->codigoG) {
            return back()->with('error', 'El alumno no pertenece a este grupo.');
        }

        if ($destino->idGestion !== $grupo->idGestion) {
            return back()->with('error', 'El grupo destino no pertenece a la misma gestión.');
        }

        $ocupacion = $destino->inscripciones()->count();
        if ($ocupacion >= $destino->capacidad) {
            return back()->with('error', "El grupo «{$destino->numero_grupo}» está lleno ({$ocupacion}/{$destino->capacidad}).");
        }

        $nombreAlumno = $inscripcion->postulante
            ? trim($inscripcion->postulante->apellidos . ' ' . $inscripcion->postulante->nombre)
            : "Inscripción #{$inscripcion->idInscripcion}";

        $inscripcion->update(['codigoG' => $destino->codigoG]);

        BitacoraService::registrar(
            "Alumno {$nombreAlumno} movido del grupo «{$grupo->numero_grupo}» al grupo «{$destino->numero_grupo}»."
        );

        return back()->with('success', "«{$nombreAlumno}» movido al grupo «{$destino->numero_grupo}».");
    }

    // ── Helper ────────────────────────────────────────────────────────────────
    private function formData(): array
    {
        return [
            'modalidades' => Modalidad::orderBy('nombModalidad')->get(),
            'horarios'    => Horario::orderBy('dia')->orderBy('hora_ini')->get(),
            'aulas'       => Aula::orderBy('idAula')->get(),
            'materias'    => Materia::orderBy('nombMateria')->get(),
            'docentes'    => Docente::orderBy('apellido')->get(),
        ];
    }

    public function show(Grupo $grupo) {}
}
