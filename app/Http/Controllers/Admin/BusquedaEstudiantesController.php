<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Postulante;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusquedaEstudiantesController extends Controller
{
    /**
     * CU05 – Búsqueda avanzada de postulantes/estudiantes.
     *
     * Filtros disponibles:
     *  - ci       : Cédula de identidad (búsqueda exacta)
     *  - apellido : Búsqueda parcial por apellidos (LIKE)
     *  - carrera  : FK codCarrera en carrera_inscritos
     *  - estado   : Estado del postulante (activo, inactivo, etc.)
     *
     * Devuelve resultados paginados con datos del pago y expediente.
     */
    public function index(Request $request): View
    {
        $query = Postulante::with([
            'pagos',
            'inscripciones.carrerasInscritas.carrera',
            'requisitos.requisito',
        ]);

        // Filtro por CI
        if ($request->filled('ci')) {
            $query->where('ci', $request->ci);
        }

        // Filtro por apellido (LIKE)
        if ($request->filled('apellido')) {
            $query->where('apellidos', 'like', '%' . $request->apellido . '%');
        }

        // Filtro por carrera (a través de inscripciones y carrera_inscritos)
        if ($request->filled('carrera')) {
            $query->whereHas('inscripciones.carrerasInscritas', function ($q) use ($request) {
                $q->where('codCarrera', $request->carrera);
            });
        }

        // Filtro por estado del postulante
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $postulantes = $query->orderBy('apellidos')->paginate(15)->withQueryString();
        $carreras    = Carrera::orderBy('nombre')->get();

        if ($request->anyFilled(['ci', 'apellido', 'carrera', 'estado'])) {
            BitacoraService::registrar(
                'Búsqueda de estudiantes con filtros: ' . json_encode($request->only('ci', 'apellido', 'carrera', 'estado'))
            );
        }

        return view('admin.estudiantes', compact('postulantes', 'carreras'));
    }
}
