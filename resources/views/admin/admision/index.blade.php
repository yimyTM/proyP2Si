@extends('layouts.app')

@section('title', 'Gestionar Cupos y Reubicación')
@section('page-title', 'CU13 – Gestionar Cupos y Reubicación')

@section('content')
<div class="space-y-5">

    {{-- Encabezado --}}
    <div class="rounded-2xl p-6 text-white"
         style="background: linear-gradient(135deg, #283342 0%, #3d5068 100%);">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold">Gestionar Cupos y Reubicación</h2>
                <p class="text-white/60 text-sm mt-1">
                    Asignación automática de plazas por carrera según promedio y preferencias del postulante.
                </p>
            </div>
        </div>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-3 text-sm flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Lista de gestiones --}}
    @forelse($gestiones as $g)
    @php
        $periodoAbierto = $g->estado === 'Abierta';
        $pct = $g->totalAprobados > 0
            ? round(($g->totalProcesados / $g->totalAprobados) * 100)
            : 0;
    @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4 justify-between">

            {{-- Info gestión --}}
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shrink-0"
                     style="background-color: #283342;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-semibold text-gray-800">{{ $g->nombre }}</p>
                        @if($periodoAbierto)
                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Período abierto
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                                Período cerrado
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $g->fecha_ini->format('d/m/Y') }} – {{ $g->fecha_fin->format('d/m/Y') }}
                    </p>
                    <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-600">
                        <span>Inscritos: <strong>{{ $g->totalInscritos }}</strong></span>
                        <span class="text-emerald-700">Aprobados: <strong>{{ $g->totalAprobados }}</strong></span>
                        <span class="{{ $g->totalProcesados > 0 ? 'text-blue-700' : 'text-gray-400' }}">
                            Admisión procesada: <strong>{{ $g->totalProcesados }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Cupos y acciones --}}
            <div class="flex flex-col gap-3 shrink-0 min-w-[200px]">
                {{-- Barra progreso --}}
                @if($g->totalAprobados > 0)
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Admisión asignada</span>
                        <span>{{ $g->totalProcesados }}/{{ $g->totalAprobados }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all {{ $pct >= 100 ? 'bg-emerald-500' : 'bg-blue-500' }}"
                             style="width: {{ min($pct, 100) }}%"></div>
                    </div>
                </div>
                @endif

                {{-- Cupos mini-chips --}}
                <div class="flex flex-wrap gap-1">
                    @foreach($g->gestionCarreras as $gc)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium"
                          title="{{ $gc->carrera?->nombre ?? 'Carrera '.$gc->codCarrera }}">
                        {{ \Illuminate\Support\Str::limit($gc->carrera?->nombre ?? 'C'.$gc->codCarrera, 14) }}:
                        <strong>{{ $gc->cupos }}</strong>
                    </span>
                    @endforeach
                </div>

                <a href="{{ route('admin.admision.show', $g->idGestion) }}"
                   class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
                   style="background-color: #283342;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ $g->totalProcesados > 0 ? 'Ver / Reprocesar' : 'Procesar admisión' }}
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
        <p class="text-gray-400 text-sm">No hay gestiones académicas registradas.</p>
    </div>
    @endforelse

</div>
@endsection
