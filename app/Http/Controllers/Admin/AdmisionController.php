<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gestion;
use App\Models\GestionCarrera;
use App\Models\Inscripcion;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdmisionController extends Controller
{
    /** CU13 – Lista de gestiones con estado de admisión. */
    public function index(): View
    {
        $gestiones = Gestion::with(['gestionCarreras.carrera'])
            ->orderByDesc('fecha_ini')
            ->get();

        foreach ($gestiones as $g) {
            $total        = Inscripcion::where('idGestion', $g->idGestion)->count();
            $aprobados    = Inscripcion::where('idGestion', $g->idGestion)
                                ->where('resultado', 'Aprobado')->count();
            $procesados   = Inscripcion::where('idGestion', $g->idGestion)
                                ->whereNotNull('estado_admision')->count();

            $g->totalInscritos  = $total;
            $g->totalAprobados  = $aprobados;
            $g->totalProcesados = $procesados;
        }

        return view('admin.admision.index', compact('gestiones'));
    }

    /** CU13 – Preview de la asignación (no guarda). */
    public function show(int $gestion): View
    {
        $gestionModel = Gestion::with(['gestionCarreras.carrera'])->findOrFail($gestion);

        [$asignaciones, $cuposRestantes, $resumen] = $this->calcularAsignaciones($gestionModel);

        return view('admin.admision.show', [
            'gestionModel'   => $gestionModel,
            'asignaciones'   => $asignaciones,
            'cuposRestantes' => $cuposRestantes,
            'resumen'        => $resumen,
        ]);
    }

    /** CU13 – Ejecuta y persiste la asignación por cupos. */
    public function procesar(Request $request, int $gestion): RedirectResponse
    {
        $gestionModel = Gestion::with(['gestionCarreras.carrera'])->findOrFail($gestion);

        if ($gestionModel->estado !== 'Abierta') {
            return back()->with('error', 'El período de evaluación está cerrado. No se puede procesar la admisión.');
        }

        // Verificar que existan resultados procesados
        $aprobados = Inscripcion::where('idGestion', $gestionModel->idGestion)
            ->where('resultado', 'Aprobado')
            ->count();

        if ($aprobados === 0) {
            return back()->with('error',
                'No existen postulantes con resultado Aprobado. Ejecute primero el procesamiento de promedios (CU12).'
            );
        }

        [$asignaciones] = $this->calcularAsignaciones($gestionModel);

        $admitidos  = 0;
        $reubicados = 0;
        $reprobados = 0;

        foreach ($asignaciones as $idInscripcion => $data) {
            Inscripcion::where('idInscripcion', $idInscripcion)->update([
                'codCarreraAsignada' => $data['codCarreraAsignada'],
                'estado_admision'    => $data['estado_admision'],
            ]);

            BitacoraService::registrar(
                "CU13 – Admisión: Inscripción #{$idInscripcion}, " .
                "Carrera: " . ($data['carreraNombre'] ?? 'Ninguna') .
                ", Estado: {$data['estado_admision']}."
            );

            match ($data['estado_admision']) {
                'Admitido'  => $admitidos++,
                'Reubicado' => $reubicados++,
                'Reprobado' => $reprobados++,
                default     => null,
            };
        }

        $msg = "Admisión procesada: {$admitidos} admitido(s), {$reubicados} reubicado(s).";
        if ($reprobados > 0) {
            $msg .= " {$reprobados} reprobado(s) (sin cupo o sin puntaje suficiente).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Algoritmo de asignación por cupos y prioridad de carrera.
     * Devuelve [asignaciones[], cuposRestantes[], resumen[]].
     */
    private function calcularAsignaciones(Gestion $gestionModel): array
    {
        // Cupos disponibles por carrera (map codCarrera => cupos)
        $cuposBase = $gestionModel->gestionCarreras
            ->pluck('cupos', 'codCarrera')
            ->toArray();

        // Cupos ya consumidos por asignaciones previas guardadas
        $yaAsignados = Inscripcion::where('idGestion', $gestionModel->idGestion)
            ->whereNotNull('codCarreraAsignada')
            ->selectRaw('"codCarreraAsignada", COUNT(*) as total')
            ->groupBy('codCarreraAsignada')
            ->pluck('total', 'codCarreraAsignada')
            ->toArray();

        $cuposRestantes = [];
        foreach ($cuposBase as $cod => $total) {
            $cuposRestantes[$cod] = $total - ($yaAsignados[$cod] ?? 0);
        }

        // Postulantes Aprobados ordenados por promedio DESC
        $inscripciones = Inscripcion::where('idGestion', $gestionModel->idGestion)
            ->where('resultado', 'Aprobado')
            ->with(['postulante', 'carreras'])
            ->orderByDesc('promedio')
            ->get();

        $asignaciones = [];

        foreach ($inscripciones as $insc) {
            $p1 = $insc->carreras->where('pivot.prioridad', 1)->first();
            $p2 = $insc->carreras->where('pivot.prioridad', 2)->first();

            $codAsignada    = null;
            $estadoAdmision = 'Reprobado';
            $carreraNombre  = null;

            if ($p1 && ($cuposRestantes[$p1->codCarrera] ?? 0) > 0) {
                $codAsignada    = $p1->codCarrera;
                $estadoAdmision = 'Admitido';
                $carreraNombre  = $p1->nombre;
                $cuposRestantes[$p1->codCarrera]--;
            } elseif ($p2 && ($cuposRestantes[$p2->codCarrera] ?? 0) > 0) {
                $codAsignada    = $p2->codCarrera;
                $estadoAdmision = 'Reubicado';
                $carreraNombre  = $p2->nombre;
                $cuposRestantes[$p2->codCarrera]--;
            }

            $asignaciones[$insc->idInscripcion] = [
                'inscripcion'       => $insc,
                'codCarreraAsignada'=> $codAsignada,
                'carreraNombre'     => $carreraNombre,
                'estado_admision'   => $estadoAdmision,
                'prioridad1'        => $p1,
                'prioridad2'        => $p2,
            ];
        }

        // Postulantes NO aprobados → No admitido
        $noAprobados = Inscripcion::where('idGestion', $gestionModel->idGestion)
            ->where(fn($q) => $q->where('resultado', '!=', 'Aprobado')->orWhereNull('resultado'))
            ->with('postulante')
            ->orderByDesc('promedio')
            ->get();

        foreach ($noAprobados as $insc) {
            $asignaciones[$insc->idInscripcion] = [
                'inscripcion'       => $insc,
                'codCarreraAsignada'=> null,
                'carreraNombre'     => null,
                'estado_admision'   => 'Reprobado',
                'prioridad1'        => null,
                'prioridad2'        => null,
            ];
        }

        // Resumen por carrera
        $resumen = [];
        foreach ($gestionModel->gestionCarreras as $gc) {
            $cod = $gc->codCarrera;
            $resumen[$cod] = [
                'carrera'        => $gc->carrera,
                'cuposTotal'     => $gc->cupos,
                'cuposRestantes' => $cuposRestantes[$cod] ?? $gc->cupos,
                'asignados'      => ($gc->cupos - ($cuposRestantes[$cod] ?? $gc->cupos)),
            ];
        }

        return [$asignaciones, $cuposRestantes, $resumen];
    }
}
