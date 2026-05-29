@extends('layouts.app')

@section('title', $docente->nombre_completo)
@section('page-title', 'Detalle del Docente')

@section('content')
<div class="space-y-5 max-w-4xl">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">{{ $docente->nombre_completo }}</h2>
            <p class="text-sm text-gray-500">CI: {{ $docente->ci }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.docentes.edit', $docente) }}"
               class="px-4 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
               style="background-color: #283342;">
                Editar
            </a>
            <a href="{{ route('admin.docentes.index') }}"
               class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                Volver al listado
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Información personal</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Correo</dt>
                <dd class="font-medium text-gray-800">{{ $docente->correo ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Teléfono</dt>
                <dd class="font-medium text-gray-800">{{ $docente->nroTelefono ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Dirección</dt>
                <dd class="font-medium text-gray-800">{{ $docente->direccion ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Carga horaria</dt>
                <dd class="font-medium text-gray-800">{{ $docente->carga_horaria ? $docente->carga_horaria . ' hrs' : '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Cuenta de usuario</dt>
                <dd class="font-medium text-gray-800">
                    @if($docente->usuario)
                        <span class="text-green-700">Activa</span> — {{ $docente->usuario->correo }}
                    @else
                        <span class="text-gray-500">Sin cuenta</span>
                    @endif
                    @if($docente->correo)
                        <div class="mt-2">
                            @include('admin.docentes._cuenta', ['docente' => $docente])
                        </div>
                    @else
                        <p class="text-xs text-gray-400 mt-1">Agregue un correo para habilitar el acceso.</p>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    @if($docente->grupos->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Grupos asignados ({{ $docente->grupos->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Grupo</th>
                        <th class="px-6 py-3">Turno</th>
                        <th class="px-6 py-3">Modalidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($docente->grupos as $grupo)
                    <tr>
                        <td class="px-6 py-3">Grupo #{{ $grupo->codigoG }}</td>
                        <td class="px-6 py-3">{{ $grupo->turno?->nombTurno ?? '—' }}</td>
                        <td class="px-6 py-3">{{ $grupo->modalidad?->nombModalidad ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.docentes.destroy', $docente) }}"
          onsubmit="return confirm('¿Eliminar permanentemente a {{ $docente->nombre_completo }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-red-600 hover:underline">
            Eliminar docente
        </button>
    </form>

</div>
@endsection
