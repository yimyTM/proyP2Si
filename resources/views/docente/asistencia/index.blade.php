@extends('layouts.app')

@section('title', 'Tomar Asistencia')
@section('page-title', 'CU10 – Registrar Asistencia')

@section('content')
<div class="space-y-5">

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Banner gestión --}}
    @if(isset($gestionActiva) && ! $gestionActiva)
        <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm bg-amber-50 border border-amber-200 text-amber-700">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            El período académico ha concluido. No se pueden registrar nuevas asistencias.
        </div>
    @endif

    {{-- Info del docente --}}
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
            <p class="text-xs text-gray-400 mt-0.5">Solo se muestran los grupos donde estás asignado en la gestión activa.</p>
        </div>

        @forelse($grupos as $grupo)
        <a href="{{ route('docente.asistencia.tomar', $grupo->codigoG) }}"
           class="flex items-center justify-between px-6 py-4 border-b last:border-0 hover:bg-gray-50 transition group">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 font-bold text-white text-sm"
                     style="background-color: #283342;">
                    {{ $grupo->numero_grupo ?? '#'.$grupo->codigoG }}
                </div>
                <div>
                    <p class="font-medium text-gray-800">
                        {{ $grupo->modalidad?->nombModalidad ?? 'Grupo' }} — {{ $grupo->turno?->nombTurno }}
                        <span class="text-gray-400 font-normal text-sm">({{ $grupo->capacidad }} cupos)</span>
                    </p>
                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
                        @foreach($grupo->materiGrupos as $mg)
                            @if($mg->materia)
                                <span class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                    {{ $mg->materia->nombMateria }}
                                </span>
                            @endif
                            @if($mg->horario)
                                <span class="text-xs text-gray-500">
                                    {{ $mg->horario->dia }} {{ $mg->horario->hora_ini->format('H:i') }}–{{ $mg->horario->hora_fin->format('H:i') }}
                                </span>
                            @endif
                        @endforeach
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
            <p class="text-gray-500 text-sm font-medium">No tiene grupos asignados en la gestión actual.</p>
            <p class="text-gray-400 text-xs mt-1">Contacte al administrador.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
