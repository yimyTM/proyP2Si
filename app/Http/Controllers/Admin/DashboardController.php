<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Pago;
use App\Models\Postulante;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── KPI cards ─────────────────────────────────────────────────────────
        $kpis = [
            'totalPostulantes' => Postulante::count(),
            'totalDocentes'    => Docente::count(),
            'pagosAprobados'   => Pago::where('estado', 'aprobado')->count(),
            'totalGrupos'      => Grupo::count(),
        ];

        // ── Gráfica 1: distribución de estados de pago (dona) ─────────────────
        $estadosPago = Pago::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // ── Gráfica 2: inscritos por carrera (barras) ─────────────────────────
        $inscritosPorCarrera = DB::table('carrera__inscritos as ci')
            ->join('carreras as c', 'c.codCarrera', '=', 'ci.codCarrera')
            ->select('c.nombre', DB::raw('count(*) as total'))
            ->where('ci.prioridad', 1)
            ->groupBy('c.codCarrera', 'c.nombre')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // ── Gráfica 3: postulantes registrados en los últimos 7 días (línea) ──
        $registrosSemana = DB::table('postulantes')
            ->select(DB::raw('DATE(created_at) as dia'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        // Rellenar días sin registros con 0
        $diasSemana    = collect();
        $totalesSemana = collect();
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->toDateString();
            $diasSemana->push(now()->subDays($i)->isoFormat('ddd D/M'));
            $totalesSemana->push($registrosSemana->get($fecha)?->total ?? 0);
        }

        return view('admin.dashboard', compact(
            'kpis',
            'estadosPago',
            'inscritosPorCarrera',
            'diasSemana',
            'totalesSemana'
        ));
    }
}
