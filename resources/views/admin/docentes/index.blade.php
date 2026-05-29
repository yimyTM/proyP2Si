@extends('layouts.app')

@section('title', 'Docentes')
@section('page-title', 'Gestión de Docentes')

@section('content')
<div class="space-y-5">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-500">Administra el personal docente del sistema.</p>
        <div class="flex gap-2">
            <a href="{{ route('admin.importar-personal') }}"
               class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                Importar CSV
            </a>
            <a href="{{ route('admin.docentes.create') }}"
               class="px-4 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
               style="background-color: #283342;">
                + Nuevo docente
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.docentes.index') }}" class="flex gap-3">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Buscar por nombre, CI o correo..."
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <button type="submit"
                    class="px-6 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
                    style="background-color: #283342;">
                Buscar
            </button>
            @if(request('q'))
            <a href="{{ route('admin.docentes.index') }}"
               class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                Limpiar
            </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">
                Docentes
                <span class="ml-2 text-sm font-normal text-gray-400">({{ $docentes->total() }} registros)</span>
            </h3>
        </div>

        @if($docentes->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400 text-sm">
                No hay docentes registrados.
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Docente</th>
                        <th class="px-6 py-3">CI</th>
                        <th class="px-6 py-3">Teléfono</th>
                        <th class="px-6 py-3">Carga horaria</th>
                        <th class="px-6 py-3">Cuenta</th>
                        <th class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($docentes as $docente)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $docente->nombre_completo }}</p>
                            <p class="text-xs text-gray-400">{{ $docente->correo ?? 'Sin correo' }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $docente->ci }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $docente->nroTelefono ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $docente->carga_horaria ? $docente->carga_horaria . ' hrs' : '—' }}</td>
                        <td class="px-6 py-3">
                            @if($docente->usuario)
                                <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Activa</span>
                            @else
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Sin cuenta</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.docentes.show', $docente) }}"
                                   class="text-xs text-blue-600 hover:underline">Ver</a>
                                <a href="{{ route('admin.docentes.edit', $docente) }}"
                                   class="text-xs text-amber-600 hover:underline">Editar</a>
                                @if($docente->correo)
                                    @include('admin.docentes._cuenta', ['docente' => $docente])
                                @endif
                                <form method="POST" action="{{ route('admin.docentes.destroy', $docente) }}"
                                      onsubmit="return confirm('¿Eliminar a {{ $docente->nombre_completo }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">
            {{ $docentes->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
