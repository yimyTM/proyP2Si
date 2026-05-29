@extends('layouts.app')

@section('title', 'Gestiones Académicas')
@section('page-title', 'Gestiones Académicas')

@section('content')
<div class="space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-500">Crea periodos de admisión y define cupos distintos por carrera.</p>
        <a href="{{ route('admin.gestiones.create') }}"
           class="px-4 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
           style="background-color: #283342;">
            + Nueva gestión
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">
                Gestiones
                <span class="ml-2 text-sm font-normal text-gray-400">({{ $gestiones->total() }} registros)</span>
            </h3>
        </div>

        @if($gestiones->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400 text-sm">
                No hay gestiones registradas.
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Periodo</th>
                        <th class="px-6 py-3">Carreras</th>
                        <th class="px-6 py-3">Inscripciones</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($gestiones as $g)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $g->idGestion }}</td>
                        <td class="px-6 py-3 text-gray-600">
                            {{ $g->fecha_ini->format('d/m/Y') }} — {{ $g->fecha_fin->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-3">
                            @if($g->carreras_count > 0)
                                <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                                    {{ $g->carreras_count }} configurada(s)
                                </span>
                            @else
                                <span class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">Sin configurar</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $g->inscripciones_count }}</td>
                        <td class="px-6 py-3">
                            @if($g->estaAbierta())
                                <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Abierta</span>
                            @else
                                <span class="text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">Cerrada</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.gestiones.carreras.index', $g) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">Cupos</a>
                                <a href="{{ route('admin.gestiones.edit', $g) }}"
                                   class="text-xs text-amber-600 hover:underline">Editar</a>
                                @if($g->estaAbierta())
                                    <form method="POST" action="{{ route('admin.gestiones.cerrar', $g) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs text-red-600 hover:underline">Cerrar</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.gestiones.abrir', $g) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs text-green-600 hover:underline">Abrir</button>
                                    </form>
                                @endif
                                @unless($g->inscripciones_count)
                                <form method="POST" action="{{ route('admin.gestiones.destroy', $g) }}"
                                      onsubmit="return confirm('¿Eliminar la gestión #{{ $g->idGestion }}?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">Eliminar</button>
                                </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">
            {{ $gestiones->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
