<?php

namespace App\Http\Controllers;

use App\Models\Docente;
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
    // ── CU04 (Admin): Supervisión y validación de expedientes ─────────────────

    public function supervisar(): View
    {
        $seccion = request('seccion', 'postulantes');

        // ── Postulantes ───────────────────────────────────────────────────────
        $requisitosP = requisito::where('tipo', 'P')->orderBy('nombre')->get();

        $postulantes = Postulante::with([
            'requisitos' => fn($q) => $q->with('requisito')
                ->whereHas('requisito', fn($r) => $r->where('tipo', 'P')),
        ])->orderBy('apellidos')->get();

        // ── Docentes ──────────────────────────────────────────────────────────
        $requisitosD = requisito::where('tipo', 'D')->orderBy('nombre')->get();

        $docentes = Docente::with([
            'requisitosDocente' => fn($q) => $q->with('requisito')
                ->whereHas('requisito', fn($r) => $r->where('tipo', 'D')),
        ])->orderBy('apellido')->get();

        return view('admin.expedientes', compact(
            'seccion',
            'requisitosP', 'postulantes',
            'requisitosD', 'docentes'
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
