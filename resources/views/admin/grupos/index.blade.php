@extends('layouts.app')

@section('title', 'Gestión de Grupos')
@section('page-title', 'Gestión de Grupos Académicos')

@section('content')
<div class="space-y-5">

    {{-- Alertas --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Header con botón crear --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $grupos->total() }} grupo(s) registrado(s)</p>
        <a href="{{ route('admin.grupos.create') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
           style="background-color: #283342;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Grupo
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3">#</th>
                        <th class="px-5 py-3">Capacidad</th>
                        <th class="px-5 py-3">Modalidad</th>
                        <th class="px-5 py-3">Turno</th>
                        <th class="px-5 py-3">Horario</th>
                        <th class="px-5 py-3">Aula</th>
                        <th class="px-5 py-3">Materia</th>
                        <th class="px-5 py-3">Docente</th>
                        <th class="px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($grupos as $grupo)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-bold text-gray-800">#{{ $grupo->codigoG }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-gray-700">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $grupo->capacidad }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                                {{ $grupo->modalidad?->nombModalidad ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $grupo->turno?->nombTurno ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600 text-xs">
                            @if($h = $grupo->horarios->first())
                                {{ $h->dia }} {{ $h->hora_ini->format('H:i') }}–{{ $h->hora_fin->format('H:i') }}
                            @else
                                <span class="text-gray-300">Sin horario</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-600 text-xs">
                            @if($a = $grupo->aulas->first())
                                Aula #{{ $a->idAula }} ({{ $a->capacidad }} cupos)
                            @else
                                <span class="text-gray-300">Sin aula</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-600 text-xs">
                            {{ $grupo->materias->first()?->nombMateria ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-gray-600 text-xs">
                            @if($d = $grupo->docentes->first())
                                {{ $d->nombre }} {{ $d->apellido }}
                            @else
                                <span class="text-gray-300">Sin docente</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.grupos.edit', $grupo->codigoG) }}"
                                   class="p-1.5 rounded-lg text-gray-400 hover:text-[#283342] hover:bg-gray-100 transition"
                                   title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.grupos.destroy', $grupo->codigoG) }}"
                                      onsubmit="return confirm('¿Eliminar el Grupo #{{ $grupo->codigoG }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                            title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-12 text-center text-gray-400 text-sm">
                            No hay grupos registrados. Crea el primero con el botón "Nuevo Grupo".
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($grupos->hasPages())
            <div class="px-5 py-4 border-t">{{ $grupos->links() }}</div>
        @endif
    </div>

</div>
@endsection
