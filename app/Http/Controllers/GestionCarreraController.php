<?php

namespace App\Http\Controllers;

use App\Http\Requests\GestionCarreraRequest;
use App\Models\Carrera;
use App\Models\Gestion;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GestionCarreraController extends Controller
{
    public function index(Gestion $gestion): View
    {
        $carreras = Carrera::with('modalidad')->orderBy('nombre')->get();

        $gestion->load('carreras');
        $cuposPorCarrera = $gestion->carreras->pluck('pivot.cupos', 'codCarrera');

        return view('admin.gestion_carreras.index', compact('gestion', 'carreras', 'cuposPorCarrera'));
    }

    public function update(GestionCarreraRequest $request, Gestion $gestion): RedirectResponse
    {
        $syncData = [];

        foreach ($request->input('cupos', []) as $codCarrera => $cupos) {
            if ($cupos !== null && $cupos !== '' && (int) $cupos > 0) {
                $syncData[(int) $codCarrera] = ['cupos' => (int) $cupos];
            }
        }

        if (empty($syncData)) {
            return back()
                ->withInput()
                ->with('error', 'Debe configurar cupos para al menos una carrera.');
        }

        $gestion->carreras()->sync($syncData);

        BitacoraService::registrar(
            "Cupos por carrera actualizados en gestión #{$gestion->idGestion} (" . count($syncData) . ' carreras).'
        );

        return redirect()
            ->route('admin.gestiones.carreras.index', $gestion)
            ->with('success', 'Cupos por carrera guardados correctamente.');
    }
}
