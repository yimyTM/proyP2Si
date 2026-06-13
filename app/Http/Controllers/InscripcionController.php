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
    // ── CU05: Expediente digital (Postulante) ─────────────────────────────────

    public function expediente(): View|RedirectResponse
    {
        $postulante = Auth::user()->postulante;

        if (! $postulante) {
            return redirect()->route('postulante.dashboard')->with('error', 'No tienes perfil de postulante asociado.');
        }

        $gestionActiva        = Gestion::where('estado', 'Abierta')->first();
        $inscripcionExistente = null;

        if ($gestionActiva) {
            $inscripcionExistente = Inscripcion::where('idPost', $postulante->idPost)
                ->where('idGestion', $gestionActiva->idGestion)
                ->with('carrerasInscritas.carrera')
                ->first();
        }

        $carreras    = Carrera::with('modalidad')->get();
        $documentos  = $this->cargarDocumentosPostulante($postulante->idPost);

        return view('postulante.expediente', compact(
            'postulante', 'carreras', 'gestionActiva',
            'inscripcionExistente', 'documentos'
        ));
    }

    public function guardarExpediente(ExpedienteRequest $request): RedirectResponse
    {
        $gestionActiva = Gestion::where('estado', 'Abierta')->first();
        if (! $gestionActiva) {
            return back()->with('error', 'Las inscripciones no están habilitadas en este momento.');
        }

        $user       = Auth::user();
        $postulante = $user->postulante;

        if (Inscripcion::where('idPost', $postulante->idPost)
                       ->where('idGestion', $gestionActiva->idGestion)
                       ->exists()) {
            return back()->with('error', 'Ya tienes un expediente registrado en esta gestión.');
        }

        DB::transaction(function () use ($request, $user, $postulante, $gestionActiva) {
            $fotoPath = $postulante->foto;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store("fotos/{$postulante->idPost}", 'public');
            }

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
                'foto'                => $fotoPath,
                'estado'              => 'activo',
            ]);

            if ($request->correo !== $user->correo) {
                $user->update(['correo' => $request->correo]);
            }

            $inscripcion = Inscripcion::create([
                'fecha'     => now()->toDateString(),
                'estado'    => 'Pendiente',
                'idPost'    => $postulante->idPost,
                'idGestion' => $gestionActiva->idGestion,
            ]);

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

            $this->guardarDocumento($request, 'doc_titulo_bachiller',          'Titulo Bachiller',              $postulante->idPost);
            $this->guardarDocumento($request, 'doc_libreta_escolar',            'Libreta escolar',               $postulante->idPost);
            $this->guardarDocumento($request, 'doc_cedula_identidad',           'Fotocopia de CI',               $postulante->idPost);
            $this->guardarDocumento($request, 'doc_formulario_preinscripcion',  'Formulario de preinscripción',  $postulante->idPost);
        });

        BitacoraService::registrar("CU05: Expediente registrado para {$postulante->nombre} {$postulante->apellidos} (CI: {$postulante->ci}).");
        return redirect()->route('postulante.expediente')
            ->with('success', 'Tu expediente fue guardado. Espera la validación del administrador.');
    }

    // ── CU05 (Admin): Validar / Rechazar inscripción ──────────────────────────

    public function validarAdmin(Inscripcion $inscripcion): RedirectResponse
    {
        // CU08: Bloquear si quedan requisitos obligatorios sin validar
        $obligatorios = requisito::where('tipo', 'P')->where('obligatorio', true)->get();

        if ($obligatorios->isNotEmpty()) {
            $validadosIds = Requisito_Postulante::where('idPost', $inscripcion->idPost)
                ->whereIn('idReq', $obligatorios->pluck('idReq'))
                ->where('validado', true)
                ->pluck('idReq');

            $pendientes = $obligatorios->filter(fn($r) => ! $validadosIds->contains($r->idReq));

            if ($pendientes->isNotEmpty()) {
                $nombres = $pendientes->pluck('nombre')->implode(', ');
                return back()->with('error',
                    "No es posible validar el expediente. Documentos obligatorios pendientes o faltantes: {$nombres}."
                );
            }
        }

        $inscripcion->update(['estado' => 'Validado', 'motivo_rechazo' => null]);
        $nombre = "{$inscripcion->postulante->nombre} {$inscripcion->postulante->apellidos}";
        BitacoraService::registrar("CU08: Expediente validado — {$nombre} (Inscripción #{$inscripcion->idInscripcion}).");
        return back()->with('success', "Expediente de {$nombre} validado correctamente.");
    }

    public function rechazarAdmin(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $data = $request->validate([
            'motivo_rechazo' => ['nullable', 'string', 'max:500'],
        ]);

        $inscripcion->update([
            'estado'          => 'Rechazado',
            'motivo_rechazo'  => $data['motivo_rechazo'] ?? null,
        ]);

        $nombre = "{$inscripcion->postulante->nombre} {$inscripcion->postulante->apellidos}";
        BitacoraService::registrar("CU05: Expediente rechazado — {$nombre}. Motivo: " . ($data['motivo_rechazo'] ?? 'Sin motivo'));
        return back()->with('success', "Expediente de {$nombre} marcado como Rechazado.");
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function guardarDocumento(Request $request, string $inputName, string $reqNombre, int $idPost): void
    {
        if (! $request->hasFile($inputName)) {
            return;
        }

        $ruta = $request->file($inputName)->store("expedientes/{$idPost}", 'public');
        $req  = requisito::firstOrCreate(
            ['nombre' => $reqNombre, 'tipo' => 'P'],
            ['obligatorio' => true]
        );

        Requisito_Postulante::updateOrCreate(
            ['idReq' => $req->idReq, 'idPost' => $idPost],
            [
                'fecha_entrega' => now()->toDateString(),
                'entregado'     => true,
                'validado'      => false,
                'ruta_archivo'  => $ruta,
            ]
        );
    }

    private function cargarDocumentosPostulante(int $idPost): array
    {
        $nombres = ['Titulo Bachiller', 'Libreta escolar', 'Fotocopia de CI', 'Formulario de preinscripción'];
        $reqs    = requisito::whereIn('nombre', $nombres)->where('tipo', 'P')->get()->keyBy('nombre');
        $entregados = Requisito_Postulante::where('idPost', $idPost)
            ->whereIn('idReq', $reqs->pluck('idReq'))
            ->get()
            ->keyBy('idReq');

        $resultado = [];
        foreach ($nombres as $nombre) {
            $req = $reqs->get($nombre);
            $rp  = $req ? $entregados->get($req->idReq) : null;
            $resultado[$nombre] = [
                'entregado' => (bool) $rp?->entregado,
                'validado'  => (bool) $rp?->validado,
            ];
        }
        return $resultado;
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
