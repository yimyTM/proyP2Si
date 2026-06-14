<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gestion;
use App\Models\Modalidad;
use App\Services\AperturaGruposService;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AperturaGruposController extends Controller
{
    public function index(Request $request): View
    {
        $gestiones           = Gestion::withCount('carreras')->orderBy('fecha_ini', 'desc')->get();
        $gestionSeleccionada = $request->query('gestion');

        $previewData = null;
        if ($gestionSeleccionada) {
            $previewData = AperturaGruposService::preview((int) $gestionSeleccionada, 70);
        }

        // Precalculado en PHP para evitar closures con { dentro de @json() en Blade
        $modalidades = Modalidad::all();
        $inscritosPorGestion = [];
        foreach ($gestiones as $g) {
            $inscritosPorGestion[$g->idGestion] = [];
            foreach ($modalidades as $m) {
                $count = DB::table('inscripcions as i')
                    ->join('carrera__inscritos as ci', 'ci.idInscripcion', '=', 'i.idInscripcion')
                    ->join('carreras as c', 'c.codCarrera', '=', 'ci.codCarrera')
                    ->where('i.idGestion', $g->idGestion)
                    ->where('i.estado', 'Validado')
                    ->where('c.codeModalidad', $m->codeModalidad)
                    ->where('ci.prioridad', 1)
                    ->distinct('i.idInscripcion')
                    ->count('i.idInscripcion');
                $inscritosPorGestion[$g->idGestion][$m->nombModalidad] = $count;
            }
        }

        return view('admin.apertura_grupos', compact(
            'gestiones', 'gestionSeleccionada', 'previewData', 'inscritosPorGestion'
        ));
    }

    /** CU09 – Crea los grupos usando el algoritmo automático. */
    public function calcular(Request $request): View|RedirectResponse
    {
        $request->validate([
            'idGestion' => ['required', 'integer', 'exists:gestions,idGestion'],
        ], [
            'idGestion.required' => 'Seleccione una gestión.',
        ]);

        $gestion = Gestion::findOrFail($request->idGestion);

        if (! $gestion->estaAbierta()) {
            return back()->with('error', 'La gestión seleccionada no está en estado "Abierta".');
        }

        $capacidadPorGrupo = 70;

        $resultado = AperturaGruposService::calcularYAbrir($gestion, $capacidadPorGrupo);

        if (isset($resultado['error'])) {
            return back()->with('error', $resultado['error']);
        }

        $totalGrupos = $resultado['grupos_creados']->count();

        BitacoraService::registrar(
            "CU09 – Apertura automática: {$totalGrupos} grupos creados para la gestión #{$gestion->idGestion} (cap. {$capacidadPorGrupo}/grupo)."
        );

        return view('admin.apertura_grupos_resultado', [
            'resumen'           => $resultado['resumen'],
            'totalGrupos'       => $totalGrupos,
            'gestion'           => $gestion,
            'capacidadPorGrupo' => $capacidadPorGrupo,
        ]);
    }
}
