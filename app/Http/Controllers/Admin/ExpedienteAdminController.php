<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Requisito_Postulante;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpedienteAdminController extends Controller
{
    /** Lista todos los expedientes pendientes de validación. */
    public function index(): View
    {
        $expedientes = Requisito_Postulante::with(['postulante', 'requisito'])
            ->where('entregado', true)
            ->orderBy('validado')
            ->orderBy('fecha_entrega', 'desc')
            ->paginate(20);

        return view('admin.expedientes', compact('expedientes'));
    }

    /** Valida (o rechaza) un requisito entregado por un postulante. */
    public function validar(Requisito_Postulante $reqPos, string $accion = 'validar'): RedirectResponse
    {
        $validado = request('accion', 'validar') === 'validar';

        $reqPos->update(['validado' => $validado]);

        BitacoraService::registrar(
            ($validado ? 'Validado' : 'Rechazado') .
            " requisito #{$reqPos->idReqPos} del postulante CI:{$reqPos->postulante->ci}."
        );

        return back()->with('success', 'Requisito ' . ($validado ? 'validado' : 'rechazado') . ' correctamente.');
    }
}
