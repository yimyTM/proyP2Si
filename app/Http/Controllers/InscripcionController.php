<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpedienteRequest;
use App\Models\Carrera;
use App\Models\Carrera_Inscrito;
use App\Models\Gestion;
use App\Models\Inscripcion;
use App\Models\requisito;
use App\Models\Requisito_Postulante;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InscripcionController extends Controller
{
    // ── CU04: Expediente digital (Postulante) ─────────────────────────────────

    public function expediente(): View|RedirectResponse
    {
        $postulante = Auth::user()->postulante;

        if (! $postulante) {
            return redirect()->route('postulante.dashboard')->with('error', 'No tienes perfil de postulante asociado.');
        }
        if (! $postulante->tienePagoAprobado()) {
            return redirect()->route('postulante.dashboard')->with('error', 'Debes verificar tu pago antes de gestionar tu expediente.');
        }

        $carreras             = Carrera::with('modalidad')->get();
        $gestionActiva        = Gestion::where('estado', 'Abierta')->first();
        $inscripcionExistente = null;

        if ($gestionActiva) {
            $inscripcionExistente = Inscripcion::where('idPost', $postulante->idPost)
                ->where('idGestion', $gestionActiva->idGestion)
                ->with('carrerasInscritas.carrera')
                ->first();
        }

        $requisitoTitulo = requisito::where('tipo', 'titulo_bachiller')->first();
        $tituloEntregado = $requisitoTitulo
            ? Requisito_Postulante::where('idPost', $postulante->idPost)->where('idReq', $requisitoTitulo->idReq)->first()
            : null;

        return view('postulante.expediente', compact('postulante','carreras','gestionActiva','inscripcionExistente','tituloEntregado'));
    }

    public function guardarExpediente(ExpedienteRequest $request): RedirectResponse
    {
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();
        if (! $gestionActiva) {
            return back()->with('error', 'No hay ningún período de admisión activo.');
        }

        $postulante = Auth::user()->postulante;

        DB::transaction(function () use ($request, $postulante, $gestionActiva) {
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

            $inscripcion = Inscripcion::create([
                'fecha'     => now()->toDateString(),
                'estado'    => 'pendiente',
                'idPost'    => $postulante->idPost,
                'idGestion' => $gestionActiva->idGestion,
            ]);

            Carrera_Inscrito::create(['prioridad' => 1, 'idInscripcion' => $inscripcion->idInscripcion, 'codCarrera' => $request->carrera_primera]);

            if ($request->filled('carrera_segunda')) {
                Carrera_Inscrito::create(['prioridad' => 2, 'idInscripcion' => $inscripcion->idInscripcion, 'codCarrera' => $request->carrera_segunda]);
            }

            $rutaArchivo = $request->file('titulo_bachiller')->store("titulos/{$postulante->idPost}", 'public');

            $reqTitulo = requisito::firstOrCreate(
                ['tipo' => 'titulo_bachiller'],
                ['nombre' => 'Título de Bachiller', 'obligatorio' => true]
            );

            Requisito_Postulante::updateOrCreate(
                ['idReq' => $reqTitulo->idReq, 'idPost' => $postulante->idPost],
                ['fecha_entrega' => now()->toDateString(), 'entregado' => true, 'validado' => false, 'ruta_archivo' => $rutaArchivo]
            );
        });

        BitacoraService::registrar('Expediente digital registrado/actualizado.');
        return redirect()->route('postulante.expediente')->with('success', 'Tu expediente fue guardado. Espera la validación del administrador.');
    }

    // ── CRUD base ─────────────────────────────────────────────────────────────
    public function index()  {}
    public function create() {}
    public function store(Request $request)              {}
    public function show(Inscripcion $inscripcion)       {}
    public function edit(Inscripcion $inscripcion)       {}
    public function update(Request $request, Inscripcion $inscripcion) {}
    public function destroy(Inscripcion $inscripcion)    {}
}
