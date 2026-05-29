<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Services\BitacoraService;
use App\Services\CuentaProvisionaService;
use App\Services\PagoGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagoController extends Controller
{
    // ── CU03: Verificación de pago (acceso público, sin login) ───────────────

    public function verificar(): View
    {
        return view('postulante.verificar_pago');
    }

    public function consultar(Request $request): View|RedirectResponse
    {
        $request->validate([
            'ci' => ['required', 'string', 'min:5', 'max:20'],
        ], ['ci.required' => 'Ingrese su número de CI.']);

        $resultado  = PagoGatewayService::consultarPorCI(trim($request->ci));
        $postulante = $resultado['postulante'];
        $pago       = $resultado['pago'];

        if (! $resultado['encontrado']) {
            return back()->withErrors(['ci' => 'No se encontró ningún registro con ese CI.'])->withInput();
        }
        if (! $resultado['aprobado']) {
            return back()->with('warning', 'Tu CI fue encontrado pero no tienes pago aprobado. Contacta a tesorería.')->withInput();
        }
        if ($postulante->idUsuario !== null) {
            BitacoraService::registrar("Postulante CI:{$request->ci} verificó pago (cuenta existente).", $postulante->idUsuario);
            return redirect()->route('login')->with('success', 'Tu pago está aprobado. Inicia sesión con tus credenciales.');
        }
        if (empty($postulante->correo)) {
            return back()->with('warning', 'Tu pago está aprobado pero no tienes correo registrado. Ve a la oficina de admisiones.')->withInput();
        }

        $passwordPlano = CuentaProvisionaService::crearCuentaPostulante($postulante);
        BitacoraService::registrar("Cuenta creada para postulante CI:{$request->ci}.", $postulante->fresh()->idUsuario);

        return view('postulante.verificar_pago', [
            'credenciales' => ['correo' => $postulante->correo, 'password' => $passwordPlano, 'nombre' => $postulante->nombre_completo],
            'pago'         => $pago,
        ]);
    }

    // ── CRUD base ─────────────────────────────────────────────────────────────
    public function index()  {}
    public function create() {}
    public function store(Request $request)    {}
    public function show(Pago $pago)           {}
    public function edit(Pago $pago)           {}
    public function update(Request $request, Pago $pago) {}
    public function destroy(Pago $pago)        {}
}
