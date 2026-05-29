<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gestion;
use App\Models\Turno;
use App\Services\AperturaGruposService;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AperturaGruposController extends Controller
{
    /** Muestra el panel de configuración de apertura. */
    public function index(): View
    {
        $gestiones = Gestion::orderBy('fecha_ini', 'desc')->get();
        $turnos    = Turno::all();

        return view('admin.apertura_grupos', compact('gestiones', 'turnos'));
    }

    /**
     * CU09 – Dispara el algoritmo de apertura en lote.
     *
     * Valida que:
     *  - Se seleccione una gestión activa.
     *  - Se seleccione un turno.
     *  - La gestión no haya tenido apertura previa (evitar duplicados).
     *
     * Luego delega toda la lógica matemática a AperturaGruposService.
     */
    public function calcular(Request $request): View|RedirectResponse
    {
        $request->validate([
            'idGestion' => ['required', 'integer', 'exists:gestions,idGestion'],
            'idTurno'   => ['required', 'integer', 'exists:turnos,idTurno'],
        ], [
            'idGestion.required' => 'Seleccione una gestión.',
            'idTurno.required'   => 'Seleccione un turno.',
        ]);

        $gestion = Gestion::findOrFail($request->idGestion);

        if (! $gestion->estaAbierta()) {
            return back()->with('error', 'La gestión seleccionada no está en estado "Abierta".');
        }

        $resultado = AperturaGruposService::calcularYAbrir($gestion, (int) $request->idTurno);

        BitacoraService::registrar(
            "Apertura automática de grupos: " . $resultado['grupos_creados']->count() .
            " grupos creados para la gestión #{$gestion->idGestion}."
        );

        return view('admin.apertura_grupos_resultado', [
            'resumen'       => $resultado['resumen'],
            'totalGrupos'   => $resultado['grupos_creados']->count(),
            'gestion'       => $gestion,
            'capacidadMax'  => $gestion->capacidad,
        ]);
    }
}
