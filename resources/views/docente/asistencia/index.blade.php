@extends('layouts.app')

@section('title', 'Tomar Asistencia')
@section('page-title', 'Tomar Asistencia')

@section('content')
<div class="space-y-5">

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Información del docente --}}
    @if($docente)
    <div class="rounded-2xl p-5 text-white flex items-center justify-between"
         style="background: linear-gradient(135deg, #283342 0%, #3d5068 100%);">
        <div>
            <p class="text-white/60 text-xs uppercase tracking-wider mb-0.5">Docente</p>
            <h2 class="font-bold text-lg">{{ $docente->nombre }} {{ $docente->apellido }}</h2>
            <p class="text-white/60 text-sm mt-0.5">CI: {{ $docente->ci }} · {{ $docente->correo }}</p>
        </div>
        <div class="text-right">
            <p class="text-white/60 text-xs">Grupos asignados</p>
            <p class="text-3xl font-extrabold">{{ $grupos->count() }}</p>
        </div>
    </div>
    @endif

    {{-- Seleccionar grupo --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Selecciona un grupo para tomar asistencia</h3>
            <p class="text-xs text-gray-400 mt-0.5">Solo se muestran los grupos donde estás asignado como docente.</p>
        </div>

        @forelse($grupos as $grupo)
        <a href="{{ route('docente.asistencia.tomar', $grupo->codigoG) }}"
           class="flex items-center justify-between px-6 py-4 border-b last:border-0 hover:bg-gray-50 transition group">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 font-bold text-white text-sm"
                     style="background-color: #283342;">
                    #{{ $grupo->codigoG }}
                </div>
                <div>
                    <p class="font-medium text-gray-800">
                        {{ $grupo->modalidad?->nombModalidad ?? 'Grupo' }} — {{ $grupo->turno?->nombTurno }}
                        <span class="text-gray-400 font-normal text-sm">({{ $grupo->capacidad }} cupos)</span>
                    </p>
                    <div class="flex items-center gap-3 mt-0.5">
                        @if($h = $grupo->horarios->first())
                            <span class="text-xs text-gray-500">
                                📅 {{ $h->dia }} {{ $h->hora_ini->format('H:i') }}–{{ $h->hora_fin->format('H:i') }}
                            </span>
                        @endif
                        @if($m = $grupo->materias->first())
                            <span class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                {{ $m->nombMateria }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <svg class="w-5 h-5 text-gray-300 group-hover:text-[#283342] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        @empty
        <div class="px-6 py-12 text-center">
            <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-gray-400 text-sm">No tienes grupos asignados todavía.</p>
            <p class="text-gray-300 text-xs mt-1">El administrador debe asignarte a un grupo desde el panel de Asignación Docente.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
