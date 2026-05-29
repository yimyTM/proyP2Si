<?php

namespace App\Http\Controllers\Postulante;

use App\Http\Controllers\Controller;
use App\Services\BitacoraService;
use App\Services\CuentaProvisionaService;
use App\Services\PagoGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagoController extends Controller
{
    /**
     * Muestra el formulario de verificación de pago.
     * Accesible sin autenticación para que el postulante pueda verificar
     * antes de recibir sus credenciales de acceso.
     */
    public function index(): View
    {
        return view('postulante.verificar_pago');
    }

    /**
     * CU03 – Consulta la pasarela de pagos externa con el CI ingresado.
     *
     * Flujo:
     *  1. Validar que el CI no esté vacío.
     *  2. Consultar PagoGatewayService (simula pasarela externa).
     *  3a. CI no encontrado → error "No estás registrado en el sistema".
     *  3b. CI encontrado pero sin pago aprobado → "Pago pendiente".
     *  3c. Pago aprobado + SIN cuenta → crear cuenta provisional + mostrar credenciales.
     *  3d. Pago aprobado + YA tiene cuenta → redirigir al login con mensaje.
     */
    public function verificar(Request $request): View|RedirectResponse
    {
        $request->validate([
            'ci' => ['required', 'string', 'min:5', 'max:20'],
        ], [
            'ci.required' => 'Ingrese su número de CI.',
            'ci.min'      => 'El CI debe tener al menos 5 caracteres.',
        ]);

        $resultado   = PagoGatewayService::consultarPorCI(trim($request->ci));
        $postulante  = $resultado['postulante'];
        $pago        = $resultado['pago'];

        // Caso 1: CI no existe en el sistema
        if (! $resultado['encontrado']) {
            return back()->withErrors([
                'ci' => 'No se encontró ningún registro con ese CI en el sistema.',
            ])->withInput();
        }

        // Caso 2: CI existe pero no tiene pago aprobado
        if (! $resultado['aprobado']) {
            return back()
                ->with('warning', 'Tu CI fue encontrado, pero no tienes ningún pago aprobado. Contacta a tesorería.')
                ->withInput();
        }

        // Caso 3: Pago aprobado — ¿ya tiene cuenta?
        if ($postulante->idUsuario !== null) {
            BitacoraService::registrar(
                "Postulante CI:{$request->ci} verificó pago (cuenta ya existente).",
                $postulante->idUsuario
            );
            return redirect()->route('login')
                ->with('success', 'Tu pago está aprobado. Inicia sesión con tus credenciales registradas.');
        }

        // Caso 4: Pago aprobado + sin cuenta → crear cuenta provisional
        if (empty($postulante->correo)) {
            return back()
                ->with('warning', 'Tu pago está aprobado, pero no tienes correo registrado. Acércate a la oficina de admisiones.')
                ->withInput();
        }

        $passwordPlano = CuentaProvisionaService::crearCuentaPostulante($postulante);

        BitacoraService::registrar(
            "Cuenta creada para postulante CI:{$request->ci} tras verificación de pago.",
            $postulante->fresh()->idUsuario
        );

        return view('postulante.verificar_pago', [
            'credenciales' => [
                'correo'   => $postulante->correo,
                'password' => $passwordPlano,
                'nombre'   => $postulante->nombre_completo,
            ],
            'pago' => $pago,
        ]);
    }
}
