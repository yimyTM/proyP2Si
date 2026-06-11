<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Carrera_Inscrito;
use App\Models\Comprobante;
use App\Models\Gestion;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\Postulante;
use App\Models\requisito;
use App\Models\Requisito_Postulante;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegistroController extends Controller
{
    /** Landing page pública. */
    public function landing(): View
    {
        $carreras = Carrera::with('modalidad')->orderBy('nombre')->get();
        $gestions  = Gestion::where('estado', 'Abierta')->latest('fecha_ini')->first();
        return view('landing', compact('carreras', 'gestions'));
    }

    /** Paso 1 – Datos personales y preferencias de carrera. */
    public function index(): View
    {
        $gestion  = Gestion::where('estado', 'Abierta')->latest('fecha_ini')->first();
        $carreras = $gestion
            ? Carrera::whereHas('gestiones', fn ($q) => $q->where('gestions.idGestion', $gestion->idGestion))
                     ->with('modalidad')
                     ->orderBy('nombre')
                     ->get()
            : Carrera::with('modalidad')->orderBy('nombre')->get();

        return view('registro.paso1', compact('carreras', 'gestion'));
    }

    /** Paso 1 POST – Crea cuenta de usuario y postulante. */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'              => 'required|string|max:100',
            'apellidos'           => 'required|string|max:100',
            'ci'                  => 'required|string|max:20|unique:postulantes,ci|unique:users,ci',
            'correo'              => 'required|email|max:200|unique:users,correo',
            'password'            => 'required|string|min:8|confirmed',
            'nroTelefono'         => 'nullable|string|max:20',
            'sexo'                => 'nullable|in:M,F',
            'fecha_nacimiento'    => 'nullable|date',
            'ciudad'              => 'nullable|string|max:100',
            'direccion'           => 'nullable|string|max:255',
            'colegio_procedencia' => 'nullable|string|max:150',
            'carrera_opcion1'     => 'required|exists:carreras,codCarrera',
            'carrera_opcion2'     => 'required|exists:carreras,codCarrera|different:carrera_opcion1',
        ], [
            'ci.unique'                 => 'Este CI ya está registrado en el sistema.',
            'correo.unique'             => 'Este correo ya está en uso.',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
            'carrera_opcion2.different' => 'La 2ª opción debe ser diferente a la 1ª.',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'nombreCompleto' => $validated['nombre'] . ' ' . $validated['apellidos'],
                'ci'             => $validated['ci'],
                'correo'         => $validated['correo'],
                'password'       => $validated['password'],
                'idRol'          => 5,
                'estado'         => false,
            ]);

            $postulante = Postulante::create([
                'nombre'              => $validated['nombre'],
                'apellidos'           => $validated['apellidos'],
                'ci'                  => $validated['ci'],
                'nroTelefono'         => $validated['nroTelefono']         ?? null,
                'sexo'                => $validated['sexo']                ?? null,
                'fecha_nacimiento'    => $validated['fecha_nacimiento']    ?? null,
                'ciudad'              => $validated['ciudad']              ?? null,
                'direccion'           => $validated['direccion']           ?? null,
                'colegio_procedencia' => $validated['colegio_procedencia'] ?? null,
                'estado'              => 'activo',
                'idUsuario'           => $user->idUsuario,
            ]);

            $gestion = Gestion::where('estado', 'Abierta')->latest('fecha_ini')->first();
            if ($gestion) {
                $inscripcion = Inscripcion::create([
                    'fecha'     => now()->toDateString(),
                    'estado'    => 'Pendiente',
                    'idPost'    => $postulante->idPost,
                    'idGestion' => $gestion->idGestion,
                ]);

                Carrera_Inscrito::create([
                    'idInscripcion' => $inscripcion->idInscripcion,
                    'codCarrera'    => $validated['carrera_opcion1'],
                    'prioridad'     => 1,
                ]);
                Carrera_Inscrito::create([
                    'idInscripcion' => $inscripcion->idInscripcion,
                    'codCarrera'    => $validated['carrera_opcion2'],
                    'prioridad'     => 2,
                ]);
            }

            Auth::login($user);
        });

        return redirect()->route('registro.documentos');
    }

    /** Paso 2 – Subida de documentos. */
    public function showDocumentos(): View
    {
        $postulante = Auth::user()->postulante;
        $requisitos = requisito::where('tipo', 'P')->orderBy('idReq')->get();
        $yaSubidos  = Requisito_Postulante::where('idPost', $postulante->idPost)
            ->pluck('ruta_archivo', 'idReq')
            ->toArray();

        return view('registro.paso2', compact('postulante', 'requisitos', 'yaSubidos'));
    }

    /** Paso 2 POST – Guarda archivos subidos. */
    public function storeDocumentos(Request $request): RedirectResponse
    {
        $postulante = Auth::user()->postulante;
        $requisitos = requisito::where('tipo', 'P')->get();

        foreach ($requisitos as $req) {
            $campo = 'archivo_' . $req->idReq;
            if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                $ruta = $request->file($campo)->store(
                    "expedientes/{$postulante->idPost}",
                    'public'
                );
                Requisito_Postulante::updateOrCreate(
                    ['idPost' => $postulante->idPost, 'idReq' => $req->idReq],
                    [
                        'fecha_entrega' => now()->toDateString(),
                        'entregado'     => true,
                        'validado'      => false,
                        'ruta_archivo'  => $ruta,
                    ]
                );
            }
        }

        return redirect()->route('registro.pago');
    }

    /** Paso 3 – Pantalla de pago con Stripe. */
    public function showPago(): View
    {
        $postulante  = Auth::user()->postulante;
        $gestion     = Gestion::where('estado', 'Abierta')->latest('fecha_ini')->first();
        $inscripcion = $gestion
            ? Inscripcion::where('idPost', $postulante->idPost)->where('idGestion', $gestion->idGestion)->first()
            : null;

        $yaPagado = Pago::where('idPost', $postulante->idPost)->where('estado', 'aprobado')->exists();
        $monto    = config('services.stripe.monto') / 100;
        $moneda   = strtoupper(config('services.stripe.currency'));

        return view('registro.paso3', compact('postulante', 'gestion', 'inscripcion', 'yaPagado', 'monto', 'moneda'));
    }

    /** Paso 3 POST – Crea la sesión de pago de Stripe Checkout y redirige. */
    public function crearCheckout(Request $request): RedirectResponse
    {
        $postulante = Auth::user()->postulante;
        $gestion    = Gestion::where('estado', 'Abierta')->latest('fecha_ini')->first();

        if (! $gestion) {
            return back()->with('error', 'No hay una gestión académica abierta.');
        }

        $inscripcion = Inscripcion::where('idPost', $postulante->idPost)
            ->where('idGestion', $gestion->idGestion)->first();

        if (! $inscripcion) {
            return back()->with('error', 'No se encontró tu inscripción.');
        }

        // Idempotencia: si ya pagó, no volver a cobrar.
        if (Pago::where('idPost', $postulante->idPost)->where('estado', 'aprobado')->exists()) {
            return redirect()->route('postulante.dashboard')->with('success', 'Tu inscripción ya fue pagada.');
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'mode'           => 'payment',
            'customer_email' => Auth::user()->correo,
            'line_items'     => [[
                'quantity'   => 1,
                'price_data' => [
                    'currency'     => config('services.stripe.currency'),
                    'unit_amount'  => (int) config('services.stripe.monto'),
                    'product_data' => [
                        'name'        => 'Inscripción FICCT — ' . $gestion->nombre,
                        'description' => "Postulante: {$postulante->nombre} {$postulante->apellidos} (CI {$postulante->ci})",
                    ],
                ],
            ]],
            'success_url' => route('registro.pago.exito') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('registro.pago.cancelado'),
            'metadata'    => [
                'idInscripcion' => $inscripcion->idInscripcion,
                'idPost'        => $postulante->idPost,
            ],
        ]);

        return redirect()->away($session->url);
    }

    /** Paso 3 – Retorno de Stripe tras pago exitoso. Registra el pago y confirma la inscripción. */
    public function pagoExito(Request $request): View|RedirectResponse
    {
        $postulante = Auth::user()->postulante;
        $sessionId  = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('registro.pago')->with('error', 'No se recibió la confirmación del pago.');
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
        } catch (\Throwable $e) {
            return redirect()->route('registro.pago')->with('error', 'No se pudo verificar el pago con Stripe.');
        }

        if (($session->payment_status ?? null) !== 'paid') {
            return redirect()->route('registro.pago')->with('error', 'El pago no se completó.');
        }

        $gestion     = Gestion::where('estado', 'Abierta')->latest('fecha_ini')->first();
        $inscripcion = $gestion
            ? Inscripcion::where('idPost', $postulante->idPost)->where('idGestion', $gestion->idGestion)->first()
            : null;

        // Idempotencia: no duplicar el pago si la página se recarga.
        $pago = Pago::where('idPost', $postulante->idPost)->where('estado', 'aprobado')->first();

        if (! $pago) {
            $pago = DB::transaction(function () use ($postulante, $inscripcion, $session) {
                $pago = Pago::create([
                    'monto'  => ($session->amount_total ?? config('services.stripe.monto')) / 100,
                    'fecha'  => now()->toDateString(),
                    'estado' => 'aprobado',
                    'idPost' => $postulante->idPost,
                ]);

                Comprobante::create([
                    'codigo'         => 'COMP-' . now()->format('Y') . '-' . str_pad((string) $pago->nroPago, 4, '0', STR_PAD_LEFT),
                    'nroComprobante' => (string) ($session->payment_intent ?? Str::upper(Str::random(16))),
                    'concepto'       => 'Pago de inscripción ' . ($inscripcion?->gestion?->nombre ?? ''),
                    'fecha'          => now()->toDateString(),
                    'nroPago'        => $pago->nroPago,
                ]);

                // Confirmar la inscripción (borrador → habilitada/inscrita).
                if ($inscripcion) {
                    $inscripcion->update(['estado' => 'Habilitado']);
                }

                return $pago;
            });
        }

        return view('registro.pago_exito', compact('postulante', 'pago', 'inscripcion'));
    }

    /** Paso 3 – Retorno de Stripe si el usuario cancela. */
    public function pagoCancelado(): RedirectResponse
    {
        return redirect()->route('registro.pago')
            ->with('error', 'Pago cancelado. Puedes intentarlo nuevamente cuando quieras.');
    }
}
