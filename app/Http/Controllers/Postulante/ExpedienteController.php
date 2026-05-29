<?php

namespace App\Http\Controllers\Postulante;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpedienteRequest;
use App\Models\Carrera;
use App\Models\Carrera_Inscrito;
use App\Models\Gestion;
use App\Models\Inscripcion;
use App\Models\requisito;
use App\Models\Requisito_Postulante;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExpedienteController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $postulante = Auth::user()->postulante;

        if (! $postulante) {
            return redirect()->route('postulante.dashboard')
                ->with('error', 'No tienes un perfil de postulante asociado.');
        }

        if (! $postulante->tienePagoAprobado()) {
            return redirect()->route('postulante.dashboard')
                ->with('error', 'Debes tener un pago aprobado antes de gestionar tu expediente.');
        }

        $carreras        = Carrera::with('modalidad')->get();
        $gestionActiva   = Gestion::where('estado', 'Abierta')->first();
        $inscripcionExistente = null;

        if ($gestionActiva) {
            $inscripcionExistente = Inscripcion::where('idPost', $postulante->idPost)
                ->where('idGestion', $gestionActiva->idGestion)
                ->with('carrerasInscritas.carrera')
                ->first();
        }

        $requisitoTitulo = requisito::where('tipo', 'titulo_bachiller')->first();
        $tituloEntregado = $requisitoTitulo
            ? Requisito_Postulante::where('idPost', $postulante->idPost)
                ->where('idReq', $requisitoTitulo->idReq)
                ->first()
            : null;

        return view('postulante.expediente', compact(
            'postulante',
            'carreras',
            'gestionActiva',
            'inscripcionExistente',
            'tituloEntregado'
        ));
    }

    public function store(ExpedienteRequest $request): RedirectResponse
    {
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();

        if (! $gestionActiva) {
            return back()->with('error', 'No hay ningún período de admisión activo en este momento.');
        }

        $postulante = Auth::user()->postulante;

        DB::transaction(function () use ($request, $postulante, $gestionActiva) {

            // 1. Actualizar datos personales
            $postulante->update([
                'nombre'              => $request->nombre,
                'apellidos'           => $request->apellidos,
                'ci'                  => $request->ci,
                'fecha_nacimiento'    => $request->fecha_nacimiento,
                'sexo'                => $request->sexo,
                'nroTelefono'         => $request->nroTelefono,
                'ciudad'              => $request->ciudad,
                'direccion'           => $request->direccion,
                'colegio_procedencia' => $request->colegio_procedencia,
                'estado'              => 'activo',
            ]);

            // 2. Crear inscripción
            $inscripcion = Inscripcion::create([
                'fecha'      => now()->toDateString(),
                'estado'     => 'pendiente',
                'idPost'     => $postulante->idPost,
                'idGestion'  => $gestionActiva->idGestion,
            ]);

            // 3. Opciones de carrera
            Carrera_Inscrito::create([
                'prioridad'      => 1,
                'idInscripcion'  => $inscripcion->idInscripcion,
                'codCarrera'     => $request->carrera_primera,
            ]);

            if ($request->filled('carrera_segunda')) {
                Carrera_Inscrito::create([
                    'prioridad'      => 2,
                    'idInscripcion'  => $inscripcion->idInscripcion,
                    'codCarrera'     => $request->carrera_segunda,
                ]);
            }

            // 4. Registrar entrega del Título de Bachiller (solo datos, sin archivo físico)
            $requisitoTitulo = requisito::where('nombre', 'Título de bachiller')->first();

            if ($requisitoTitulo) {
                Requisito_Postulante::updateOrCreate(
                    ['idReq' => $requisitoTitulo->idReq, 'idPost' => $postulante->idPost],
                    [
                        'fecha_entrega' => now()->toDateString(),
                        'entregado'     => true,
                        'validado'      => false,
                    ]
                );
            }
        });

        BitacoraService::registrar('Expediente digital registrado/actualizado.');

        return redirect()->route('postulante.expediente')
            ->with('success', 'Tu expediente fue guardado correctamente. Espera la validación del administrador.');
    }
}
