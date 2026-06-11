<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Form_Academica;
use App\Models\requisito;
use App\Models\User;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Postulación pública de docentes desde la landing.
 * Mismos pasos que el postulante EXCEPTO el pago. No crea cuenta de acceso:
 * el docente queda como candidato; el Admin valida sus requisitos y lo contrata (CU15).
 */
class PostulacionDocenteController extends Controller
{
    private const SESSION_KEY = 'postulacion_docente_id';

    // ── Paso 1: datos personales + formación académica ────────────────────────

    public function index(): View
    {
        $formaciones = Form_Academica::orderBy('nombProfesion')->get();
        return view('postular_docente.paso1', compact('formaciones'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'      => ['required', 'string', 'max:100'],
            'apellido'    => ['required', 'string', 'max:100'],
            'ci'          => ['required', 'string', 'max:20', Rule::unique('docentes', 'ci'), Rule::unique('users', 'ci')],
            'nroTelefono' => ['nullable', 'string', 'max:20'],
            'direccion'   => ['nullable', 'string', 'max:255'],
            'formaciones'                        => ['nullable', 'array'],
            'formaciones.*'                      => ['integer', 'exists:form_academicas,idForm'],
            'nuevas_profesiones'                 => ['nullable', 'array'],
            'nuevas_profesiones.*.nombProfesion' => ['nullable', 'string', 'max:100'],
            'nuevas_profesiones.*.nroProfesion'  => ['nullable', 'string', 'max:50'],
        ], [
            'ci.unique' => 'Ya existe un registro con esa cédula de identidad.',
        ]);

        $docente = DB::transaction(function () use ($request, $validated) {
            $docente = Docente::create([
                'nombre'      => $validated['nombre'],
                'apellido'    => $validated['apellido'],
                'ci'          => $validated['ci'],
                'nroTelefono' => $validated['nroTelefono'] ?? null,
                'direccion'   => $validated['direccion'] ?? null,
            ]);

            $idsFormacion = array_filter((array) $request->input('formaciones', []));

            foreach ((array) $request->input('nuevas_profesiones', []) as $prof) {
                $nombre = trim((string) ($prof['nombProfesion'] ?? ''));
                if ($nombre === '') {
                    continue;
                }
                $nueva = Form_Academica::create([
                    'nroProfesion'  => trim((string) ($prof['nroProfesion'] ?? '')) ?: null,
                    'nombProfesion' => $nombre,
                ]);
                $idsFormacion[] = $nueva->idForm;
            }

            if (! empty($idsFormacion)) {
                $docente->formAcademicas()->sync(array_unique($idsFormacion));
            }

            return $docente;
        });

        // Guardar el candidato en sesión para el paso 2 (no hay cuenta todavía).
        $request->session()->put(self::SESSION_KEY, $docente->codigoDoc);

        BitacoraService::registrar("Postulación docente iniciada: {$docente->nombre_completo} (CI {$docente->ci}).");

        return redirect()->route('postular-docente.documentos');
    }

    // ── Paso 2: documentos (requisitos tipo D) ────────────────────────────────

    public function documentos(Request $request): View|RedirectResponse
    {
        $docente = $this->docenteEnSesion($request);
        if (! $docente) {
            return redirect()->route('postular-docente')->with('error', 'Inicia tu postulación primero.');
        }

        $requisitos = requisito::where('tipo', 'D')->orderBy('idReq')->get();

        return view('postular_docente.paso2', compact('docente', 'requisitos'));
    }

    public function storeDocumentos(Request $request): RedirectResponse
    {
        $docente = $this->docenteEnSesion($request);
        if (! $docente) {
            return redirect()->route('postular-docente')->with('error', 'Inicia tu postulación primero.');
        }

        $requisitos = requisito::where('tipo', 'D')->get();

        foreach ($requisitos as $req) {
            $campo = 'archivo_' . $req->idReq;
            if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                // La tabla requisito_docente no almacena ruta; guardamos el archivo en disco
                // bajo el código del docente y marcamos el requisito como entregado.
                $request->file($campo)->store("expedientes/docentes/{$docente->codigoDoc}", 'public');

                $docente->requisitosDocente()->updateOrCreate(
                    ['idReq' => $req->idReq],
                    ['entregado' => true, 'validado' => false, 'fecha_entrega' => now()->toDateString()]
                );
            }
        }

        $request->session()->forget(self::SESSION_KEY);

        BitacoraService::registrar("Postulación docente completada: {$docente->nombre_completo} (CI {$docente->ci}).");

        return redirect()->route('postular-docente.gracias');
    }

    public function gracias(): View
    {
        return view('postular_docente.gracias');
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function docenteEnSesion(Request $request): ?Docente
    {
        $id = $request->session()->get(self::SESSION_KEY);
        return $id ? Docente::find($id) : null;
    }
}
