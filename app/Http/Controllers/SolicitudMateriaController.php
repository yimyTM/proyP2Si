<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Materia;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudMateriaController extends Controller
{
    private const TABLA = 'solicitud_materias';

    // ════════════════════════════════════════════════════════════════════════
    //  LADO DOCENTE — solicitar materias
    // ════════════════════════════════════════════════════════════════════════

    /** Formulario del docente: materias disponibles + estado de sus solicitudes. */
    public function misSolicitudes(): View|RedirectResponse
    {
        $docente = Auth::user()->docente;
        if (! $docente) {
            return redirect()->route('docente.dashboard')
                ->with('error', 'Tu cuenta no está vinculada a un perfil docente.');
        }

        $materias    = Materia::orderBy('nombMateria')->get();
        $solicitudes = DB::table(self::TABLA)
            ->where('codigoDoc', $docente->codigoDoc)
            ->pluck('estado', 'idMateria'); // [idMateria => estado]

        return view('docente.solicitud_materias', compact('docente', 'materias', 'solicitudes'));
    }

    /** Guarda las solicitudes del docente (crea pendientes, retira pendientes no marcadas). */
    public function solicitar(Request $request): RedirectResponse
    {
        $docente = Auth::user()->docente;
        if (! $docente) {
            return redirect()->route('docente.dashboard')
                ->with('error', 'Tu cuenta no está vinculada a un perfil docente.');
        }

        $request->validate([
            'materias'   => ['nullable', 'array'],
            'materias.*' => ['integer', 'exists:materias,idMateria'],
        ]);

        $marcadas = array_map('intval', (array) $request->input('materias', []));

        $actuales = DB::table(self::TABLA)
            ->where('codigoDoc', $docente->codigoDoc)
            ->pluck('estado', 'idMateria');

        foreach (Materia::pluck('idMateria') as $idMateria) {
            $estadoActual = $actuales[$idMateria] ?? null;
            $marcada      = in_array($idMateria, $marcadas, true);

            if ($marcada) {
                // Nueva o re-solicitud (si estaba rechazada) → pendiente. No tocar las aceptadas.
                if ($estadoActual === null || $estadoActual === 'rechazado') {
                    DB::table(self::TABLA)->updateOrInsert(
                        ['codigoDoc' => $docente->codigoDoc, 'idMateria' => $idMateria],
                        ['estado' => 'pendiente']
                    );
                }
            } else {
                // Desmarcada: solo se retira si seguía pendiente (no borrar aceptadas/rechazadas).
                if ($estadoActual === 'pendiente') {
                    DB::table(self::TABLA)
                        ->where('codigoDoc', $docente->codigoDoc)
                        ->where('idMateria', $idMateria)
                        ->delete();
                }
            }
        }

        BitacoraService::registrar("Docente {$docente->nombre_completo} actualizó sus solicitudes de materia.");

        return redirect()->route('docente.solicitud-materias')
            ->with('success', 'Tus solicitudes fueron registradas. Quedan pendientes de aprobación.');
    }

    // ════════════════════════════════════════════════════════════════════════
    //  LADO ADMIN / COORDINADOR — revisar solicitudes
    // ════════════════════════════════════════════════════════════════════════

    /** Listado de solicitudes con filtro por estado. */
    public function index(Request $request): View
    {
        $estado = $request->string('estado')->toString();
        $estado = in_array($estado, ['pendiente', 'aceptado', 'rechazado'], true) ? $estado : null;

        $query = DB::table(self::TABLA . ' as s')
            ->join('docentes as d', 's.codigoDoc', '=', 'd.codigoDoc')
            ->join('materias as m', 's.idMateria', '=', 'm.idMateria')
            ->select('s.estado', 's.codigoDoc', 's.idMateria',
                     'd.nombre', 'd.apellido', 'm.nombMateria')
            ->orderByRaw("CASE s.estado WHEN 'pendiente' THEN 0 WHEN 'aceptado' THEN 1 ELSE 2 END")
            ->orderBy('d.apellido');

        if ($estado) {
            $query->where('s.estado', $estado);
        }

        $solicitudes = $query->get();

        $conteos = DB::table(self::TABLA)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return view('admin.solicitudes.index', compact('solicitudes', 'estado', 'conteos'));
    }

    public function aceptar(int $codigoDoc, int $idMateria): RedirectResponse
    {
        return $this->cambiarEstado($codigoDoc, $idMateria, 'aceptado');
    }

    public function rechazar(int $codigoDoc, int $idMateria): RedirectResponse
    {
        return $this->cambiarEstado($codigoDoc, $idMateria, 'rechazado');
    }

    private function cambiarEstado(int $codigoDoc, int $idMateria, string $estado): RedirectResponse
    {
        $afectadas = DB::table(self::TABLA)
            ->where('codigoDoc', $codigoDoc)
            ->where('idMateria', $idMateria)
            ->update(['estado' => $estado]);

        if ($afectadas === 0) {
            return back()->with('error', 'No se encontró la solicitud.');
        }

        $docente = Docente::find($codigoDoc);
        $materia = Materia::find($idMateria);
        BitacoraService::registrar(
            "Solicitud de materia «{$materia?->nombMateria}» de {$docente?->nombre_completo} marcada como {$estado}."
        );

        return back()->with('success', "Solicitud {$estado}.");
    }
}
