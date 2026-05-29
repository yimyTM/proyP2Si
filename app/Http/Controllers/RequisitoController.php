<?php

namespace App\Http\Controllers;

use App\Models\requisito;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequisitoController extends Controller
{
    public function index(): View
    {
        $requisitos = requisito::withCount(['requisitoPostulantes', 'requisitoDocentes'])
            ->orderBy('tipo')
            ->orderBy('nombre')
            ->get();

        return view('admin.requisitos.index', compact('requisitos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:100', 'unique:requisitos,nombre'],
            'tipo'        => ['required', 'in:P,D'],
            'obligatorio' => ['boolean'],
        ], [
            'nombre.required' => 'El nombre del requisito es obligatorio.',
            'nombre.unique'   => 'Ya existe un requisito con ese nombre.',
            'tipo.required'   => 'Selecciona el tipo de requisito.',
            'tipo.in'         => 'El tipo debe ser Postulante (P) o Docente (D).',
        ]);

        $data['obligatorio'] = $request->boolean('obligatorio');

        $req = requisito::create($data);

        BitacoraService::registrar("Requisito creado: {$req->nombre} (tipo {$req->tipo}).");

        return back()->with('success', "Requisito «{$req->nombre}» creado correctamente.");
    }

    public function update(Request $request, requisito $requisito): RedirectResponse
    {
        $data = $request->validate([
            'nombre'      => [
                'required', 'string', 'max:100',
                "unique:requisitos,nombre,{$requisito->idReq},idReq",
            ],
            'tipo'        => ['required', 'in:P,D'],
            'obligatorio' => ['boolean'],
        ], [
            'nombre.required' => 'El nombre del requisito es obligatorio.',
            'nombre.unique'   => 'Ya existe otro requisito con ese nombre.',
            'tipo.required'   => 'Selecciona el tipo de requisito.',
            'tipo.in'         => 'El tipo debe ser Postulante (P) o Docente (D).',
        ]);

        $data['obligatorio'] = $request->boolean('obligatorio');

        $anterior = $requisito->nombre;
        $requisito->update($data);

        BitacoraService::registrar("Requisito actualizado: «{$anterior}» → «{$requisito->nombre}».");

        return back()->with('success', 'Requisito actualizado correctamente.');
    }

    public function destroy(requisito $requisito): RedirectResponse
    {
        $usos = $requisito->requisitoPostulantes()->count()
              + $requisito->requisitoDocentes()->count();

        if ($usos > 0) {
            return back()->with('error',
                "No se puede eliminar «{$requisito->nombre}» porque está asignado a {$usos} registro(s)."
            );
        }

        $nombre = $requisito->nombre;
        $requisito->delete();

        BitacoraService::registrar("Requisito eliminado: «{$nombre}».");

        return back()->with('success', "Requisito «{$nombre}» eliminado.");
    }
}
