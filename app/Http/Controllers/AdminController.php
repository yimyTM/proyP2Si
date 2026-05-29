<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Pago;
use App\Models\Postulante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    // ── CU01: Dashboard del Administrador ────────────────────────────────────

    public function dashboard(): View
    {
        $kpis = [
            'totalPostulantes' => Postulante::count(),
            'totalDocentes'    => Docente::count(),
            'pagosAprobados'   => Pago::where('estado', 'aprobado')->count(),
            'totalGrupos'      => Grupo::count(),
        ];

        $estadosPago = Pago::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        $inscritosPorCarrera = DB::table('carrera__inscritos as ci')
            ->join('carreras as c', 'c.codCarrera', '=', 'ci.codCarrera')
            ->select('c.nombre', DB::raw('count(*) as total'))
            ->where('ci.prioridad', 1)
            ->groupBy('c.codCarrera', 'c.nombre')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $registrosSemana = DB::table('postulantes')
            ->select(DB::raw('DATE(created_at) as dia'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $diasSemana    = collect();
        $totalesSemana = collect();
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->toDateString();
            $diasSemana->push(now()->subDays($i)->isoFormat('ddd D/M'));
            $totalesSemana->push($registrosSemana->get($fecha)?->total ?? 0);
        }

        return view('admin.dashboard', compact(
            'kpis', 'estadosPago', 'inscritosPorCarrera', 'diasSemana', 'totalesSemana'
        ));
    }
}
