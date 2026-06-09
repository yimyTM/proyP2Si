<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Gestion;
use App\Models\Postulante;
use App\Models\requisito;
use App\Models\Requisito_docente;
use App\Models\Requisito_Postulante;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequisitoPostulanteController extends Controller
{
    // ── CU08: Consultar y gestionar expedientes ───────────────────────────────

    public function supervisar(Request $request): View
    {
        $seccion = $request->get('seccion', 'postulantes');

        $gestionActiva = Gestion::where('estado', 'Abierta')->first();
        $gestionVista  = $gestionActiva ?? Gestion::orderBy('fecha_ini', 'desc')->first();
        $soloLectura   = $gestionVista && $gestionVista->estado !== 'Abierta';

        // ── Filtros ───────────────────────────────────────────────────────────
        $buscarCi       = $request->get('buscar_ci');
        $buscarNombre   = $request->get('buscar_nombre');
        $buscarApellido = $request->get('buscar_apellido');
        $estadoFiltro   = $request->get('estado_expediente');
        $carreraFiltro  = $request->get('carrera');
        $hayFiltros     = filled($buscarCi) || filled($buscarNombre) || filled($buscarApellido)
                          || filled($estadoFiltro) || filled($carreraFiltro);

        // ── Postulantes ───────────────────────────────────────────────────────
        $requisitosP = requisito::where('tipo', 'P')->orderBy('nombre')->get();

        $queryP = Postulante::with([
            'requisitos'    => fn($q) => $q->with('requisito')
                ->whereHas('requisito', fn($r) => $r->where('tipo', 'P')),
            'inscripciones' => fn($q) => $q
                ->when($gestionVista, fn($q) => $q->where('idGestion', $gestionVista->idGestion))
                ->with('carrerasInscritas.carrera')
                ->latest(),
        ])->orderBy('apellidos');

        if (filled($buscarCi))       $queryP->where('ci',        'ilike', "%{$buscarCi}%");
        if (filled($buscarNombre))   $queryP->where('nombre',    'ilike', "%{$buscarNombre}%");
        if (filled($buscarApellido)) $queryP->where('apellidos', 'ilike', "%{$buscarApellido}%");

        if (filled($estadoFiltro)) {
            if ($estadoFiltro === 'Faltante') {
                $queryP->whereDoesntHave('inscripciones', function ($q) use ($gestionVista) {
                    if ($gestionVista) $q->where('idGestion', $gestionVista->idGestion);
                });
            } else {
                $queryP->whereHas('inscripciones', function ($q) use ($estadoFiltro, $gestionVista) {
                    $q->where('estado', $estadoFiltro);
                    if ($gestionVista) $q->where('idGestion', $gestionVista->idGestion);
                });
            }
        }

        if (filled($carreraFiltro)) {
            $queryP->whereHas('inscripciones', function ($q) use ($carreraFiltro, $gestionVista) {
                if ($gestionVista) $q->where('idGestion', $gestionVista->idGestion);
                $q->whereHas('carrerasInscritas', fn($q2) => $q2->where('codCarrera', $carreraFiltro));
            });
        }

        $postulantes = $queryP->get();

        // ── Docentes ──────────────────────────────────────────────────────────
        $requisitosD = requisito::where('tipo', 'D')->orderBy('nombre')->get();

        $queryD = Docente::with([
            'requisitosDocente' => fn($q) => $q->with('requisito')
                ->whereHas('requisito', fn($r) => $r->where('tipo', 'D')),
        ])->orderBy('apellido');

        if (filled($buscarCi))       $queryD->where('ci',       'ilike', "%{$buscarCi}%");
        if (filled($buscarNombre))   $queryD->where('nombre',   'ilike', "%{$buscarNombre}%");
        if (filled($buscarApellido)) $queryD->where('apellido', 'ilike', "%{$buscarApellido}%");

        if (filled($estadoFiltro)) {
            $totalObligD = requisito::where('tipo', 'D')->where('obligatorio', true)->count();
            if ($totalObligD > 0) {
                if ($estadoFiltro === 'Validado') {
                    $queryD->whereHas(
                        'requisitosDocente',
                        fn($q) => $q->where('validado', true)
                            ->whereHas('requisito', fn($r) => $r->where('tipo', 'D')->where('obligatorio', true)),
                        '>=', $totalObligD
                    );
                } elseif ($estadoFiltro === 'Faltante') {
                    $queryD->whereDoesntHave('requisitosDocente');
                } elseif ($estadoFiltro === 'Pendiente') {
                    $queryD->whereHas('requisitosDocente', fn($q) => $q->where('validado', false));
                }
            }
        }

        $docentes = $queryD->get();
        $carreras = Carrera::orderBy('nombre')->get();

        return view('admin.expedientes', compact(
            'seccion', 'soloLectura', 'hayFiltros',
            'gestionActiva', 'gestionVista', 'carreras',
            'requisitosP', 'postulantes',
            'requisitosD', 'docentes',
            'buscarCi', 'buscarNombre', 'buscarApellido', 'estadoFiltro', 'carreraFiltro'
        ));
    }

    public function estadoPostulante(Request $request, Postulante $postulante, requisito $requisito): RedirectResponse
    {
        $estado   = $request->validate(['estado' => 'required|in:faltante,pendiente,validado'])['estado'];
        $registro = Requisito_Postulante::where('idPost', $postulante->idPost)
            ->where('idReq', $requisito->idReq)->first();

        match ($estado) {
            'faltante'  => $registro?->delete(),
            'pendiente' => $registro
                ? $registro->update(['validado' => false])
                : Requisito_Postulante::create([
                    'idPost' => $postulante->idPost, 'idReq' => $requisito->idReq,
                    'fecha_entrega' => now()->toDateString(), 'entregado' => true, 'validado' => false,
                  ]),
            'validado'  => $registro
                ? $registro->update(['validado' => true])
                : Requisito_Postulante::create([
                    'idPost' => $postulante->idPost, 'idReq' => $requisito->idReq,
                    'fecha_entrega' => now()->toDateString(), 'entregado' => true, 'validado' => true,
                  ]),
        };

        $nombre = "{$postulante->nombre} {$postulante->apellidos}";
        BitacoraService::registrar("Requisito «{$requisito->nombre}» → {$estado} para {$nombre}.");
        return back()->with('success', "Estado de «{$requisito->nombre}» actualizado a «{$estado}» para {$nombre}.");
    }

    public function estadoDocente(Request $request, Docente $docente, requisito $requisito): RedirectResponse
    {
        $estado   = $request->validate(['estado' => 'required|in:faltante,pendiente,validado'])['estado'];
        $registro = Requisito_docente::where('codigoDoc', $docente->codigoDoc)
            ->where('idReq', $requisito->idReq)->first();

        match ($estado) {
            'faltante'  => $registro?->delete(),
            'pendiente' => $registro
                ? $registro->update(['validado' => false])
                : Requisito_docente::create([
                    'codigoDoc' => $docente->codigoDoc, 'idReq' => $requisito->idReq,
                    'fecha_entrega' => now()->toDateString(), 'entregado' => true, 'validado' => false,
                  ]),
            'validado'  => $registro
                ? $registro->update(['validado' => true])
                : Requisito_docente::create([
                    'codigoDoc' => $docente->codigoDoc, 'idReq' => $requisito->idReq,
                    'fecha_entrega' => now()->toDateString(), 'entregado' => true, 'validado' => true,
                  ]),
        };

        $nombre = "{$docente->nombre} {$docente->apellido}";
        BitacoraService::registrar("Requisito «{$requisito->nombre}» → {$estado} para {$nombre}.");
        return back()->with('success', "Estado de «{$requisito->nombre}» actualizado a «{$estado}» para {$nombre}.");
    }

    // ── CRUD base ─────────────────────────────────────────────────────────────
    public function index()  {}
    public function create() {}
    public function store(Request $request)                                          {}
    public function show(Requisito_Postulante $requisito_Postulante)                 {}
    public function edit(Requisito_Postulante $requisito_Postulante)                 {}
    public function update(Request $request, Requisito_Postulante $requisito_Postulante) {}
    public function destroy(Requisito_Postulante $requisito_Postulante)              {}
}
