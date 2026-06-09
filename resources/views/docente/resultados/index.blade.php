@extends('layouts.app')

@section('title', 'Procesar Resultados')
@section('page-title', 'CU12 – Procesamiento de Resultados')

@section('content')
<div class="space-y-5">

    {{-- Encabezado --}}
    <div class="rounded-2xl p-6 text-white flex items-center justify-between"
         style="background: linear-gradient(135deg, #283342 0%, #3d5068 100%);">
        <div>
            <h2 class="text-xl font-bold">Procesar Resultados</h2>
            <p class="text-white/60 text-sm mt-1">
                Calcula y registra el promedio final y el estado académico de cada postulante.
            </p>
        </div>
        <a href="{{ route('docente.dashboard') }}"
           class="hidden md:flex items-center gap-2 px-4 py-2.5 bg-white text-sm font-semibold rounded-xl transition hover:bg-gray-100"
           style="color: #283342;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al panel
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($grupos->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-gray-50">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">No tiene grupos asignados en la gestión actual.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($grupos as $grupo)
            @php
                $gestion = $grupo->gestion;
                $abierta = $gestion && $gestion->estado === 'Abierta';
                $pct     = $grupo->totalEstudiantes > 0
                    ? round(($grupo->totalProcesados / $grupo->totalEstudiantes) * 100)
                    : 0;
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">

                {{-- Cabecera --}}
                <div class="px-5 py-4 border-b flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0"
                         style="background-color: #283342;">
                        {{ $grupo->numero_grupo ?? '#'.$grupo->codigoG }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900">
                            Grupo {{ $grupo->numero_grupo ?? $grupo->codigoG }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $grupo->modalidad?->nombModalidad }} / {{ $grupo->turno?->nombTurno }}
                        </p>
                    </div>
                    <div class="ml-auto shrink-0">
                        @if($abierta)
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Abierta
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-red-700 bg-red-50 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Cerrada
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Progreso --}}
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                        <span>Estudiantes procesados</span>
                        <span class="font-semibold text-gray-700">{{ $grupo->totalProcesados }} / {{ $grupo->totalEstudiantes }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all"
                             style="width: {{ $pct }}%; background-color: {{ $pct === 100 ? '#10b981' : '#283342' }};"></div>
                    </div>
                    <p class="text-right text-xs text-gray-400 mt-1">{{ $pct }}% completado</p>
                </div>

                {{-- Gestión info --}}
                @if($gestion)
                <div class="px-5 py-2 bg-gray-50 text-xs text-gray-500 border-t">
                    {{ $gestion->nombre }} ·
                    {{ $gestion->fecha_ini->format('d/m/Y') }} – {{ $gestion->fecha_fin->format('d/m/Y') }}
                </div>
                @endif

                {{-- Acción --}}
                <div class="px-5 py-4 border-t">
                    @if($grupo->totalEstudiantes === 0)
                        <p class="text-xs text-amber-600 text-center">Sin estudiantes inscritos.</p>
                    @else
                        <a href="{{ route('docente.resultados.show', $grupo->codigoG) }}"
                           class="block w-full text-center px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
                           style="background-color: #283342;">
                            {{ $grupo->totalProcesados === 0 ? 'Procesar resultados' : 'Ver / Reprocesar' }}
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
