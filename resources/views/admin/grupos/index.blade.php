@extends('layouts.app')

@section('title', 'Gestión de Grupos')
@section('page-title', 'CU06 – Gestión de Grupos Académicos')

@section('content')
<div class="space-y-5">

    {{-- Alertas --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Banner de gestión activa --}}
    @if($gestionActiva)
        <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-medium text-white" style="background-color: #283342;">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Gestión activa: <strong class="ml-1">{{ $gestionActiva->nombre ?? "ID {$gestionActiva->idGestion}" }}</strong>
            &nbsp;·&nbsp; Mostrando grupos de esta gestión
        </div>
    @else
        <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm bg-amber-50 border border-amber-200 text-amber-700">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            No hay una gestión académica activa. Debe existir una gestión activa para crear grupos.
        </div>
    @endif

    {{-- Header con botón crear --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $grupos->total() }} grupo(s) registrado(s)</p>
        @if($gestionActiva)
        <a href="{{ route('admin.grupos.create') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
           style="background-color: #283342;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            + Nuevo Grupo
        </a>
        @else
        <button disabled
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold opacity-40 cursor-not-allowed"
                style="background-color: #283342;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Grupo
        </button>
        @endif
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3">Grupo</th>
                        <th class="px-5 py-3">Capacidad</th>
                        <th class="px-5 py-3">Modalidad</th>
                        <th class="px-5 py-3">Turno</th>
                        <th class="px-5 py-3">Materias / Asignaciones</th>
                        <th class="px-5 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($grupos as $grupo)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <span class="font-bold text-gray-800">{{ $grupo->numero_grupo }}</span>
                            <span class="text-xs text-gray-400 ml-1">#{{ $grupo->codigoG }}</span>
                        </td>
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
                        <td class="px-5 py-3">
                            @if($grupo->materiGrupos->isNotEmpty())
                                <ul class="space-y-1">
                                @foreach($grupo->materiGrupos as $mg)
                                    <li class="text-xs text-gray-700 leading-snug">
                                        <span class="font-medium">{{ $mg->materia?->nombMateria }}</span>
                                        @if($mg->docente)
                                            · <span class="text-gray-500">{{ $mg->docente->apellido }}</span>
                                        @else
                                            · <span class="text-gray-300">Sin docente</span>
                                        @endif
                                        @if($mg->horario)
                                            · <span class="text-gray-400">{{ $mg->horario->dia }} {{ $mg->horario->hora_ini->format('H:i') }}</span>
                                        @else
                                            · <span class="text-gray-300">Sin horario</span>
                                        @endif
                                    </li>
                                @endforeach
                                </ul>
                            @else
                                <span class="text-xs text-gray-300">Sin asignaciones</span>
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
                                      onsubmit="return confirm('¿Eliminar el Grupo «{{ $grupo->numero_grupo }}»?')">
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
                            @if($gestionActiva)
                                No hay grupos en la gestión activa. Crea el primero con "+ Nuevo Grupo".
                            @else
                                No hay gestión activa. Abre una gestión para comenzar a crear grupos.
                            @endif
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
