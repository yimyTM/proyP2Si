@extends('layouts.app')

@section('title', 'Docente – Dashboard')
@section('page-title', 'Panel Docente')

@section('content')
@php
    $docente = Auth::user()->docente;
    $grupos  = $docente?->grupos()->with(['modalidad','turno','horarios','materias'])->get() ?? collect();
@endphp

<div class="space-y-5">

    {{-- Bienvenida --}}
    <div class="rounded-2xl p-6 text-white flex items-center justify-between"
         style="background: linear-gradient(135deg, #283342 0%, #3d5068 100%);">
        <div>
            <h2 class="text-xl font-bold">Bienvenido, {{ Auth::user()->nombreCompleto }}</h2>
            <p class="text-white/60 text-sm mt-1">
                {{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }} &nbsp;·&nbsp; Docente FICCT
            </p>
        </div>
        <a href="{{ route('docente.asistencia.index') }}"
           class="hidden md:flex items-center gap-2 px-4 py-2.5 bg-white text-sm font-semibold rounded-xl transition hover:bg-gray-100"
           style="color: #283342;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Tomar asistencia
        </a>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Grupos asignados</p>
            <p class="text-3xl font-extrabold" style="color: #283342;">{{ $grupos->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Total de cupos</p>
            <p class="text-3xl font-extrabold text-purple-600">{{ $grupos->sum('capacidad') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Materias activas</p>
            <p class="text-3xl font-extrabold text-blue-600">
                {{ $grupos->flatMap->materias->unique('idMateria')->count() }}
            </p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Turnos distintos</p>
            <p class="text-3xl font-extrabold text-emerald-600">
                {{ $grupos->pluck('idTurno')->unique()->count() }}
            </p>
        </div>
    </div>

    {{-- Mis grupos --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Mis grupos asignados</h3>
            <a href="{{ route('docente.asistencia.index') }}"
               class="text-xs font-medium hover:underline" style="color: #283342;">
                Ir a asistencia →
            </a>
        </div>

        @forelse($grupos as $grupo)
        <div class="flex items-center justify-between px-6 py-4 border-b last:border-0 hover:bg-gray-50 transition">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0"
                     style="background-color: #283342;">#{{ $grupo->codigoG }}</div>
                <div>
                    <p class="font-medium text-gray-800">
                        {{ $grupo->modalidad?->nombModalidad }} — {{ $grupo->turno?->nombTurno }}
                        <span class="text-gray-400 font-normal text-sm">({{ $grupo->capacidad }} cupos)</span>
                    </p>
                    <div class="flex items-center gap-3 mt-0.5">
                        @if($h = $grupo->horarios->first())
                            <span class="text-xs text-gray-500">
                                {{ $h->dia }} {{ $h->hora_ini->format('H:i') }}–{{ $h->hora_fin->format('H:i') }}
                            </span>
                        @endif
                        @if($m = $grupo->materias->first())
                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                                {{ $m->nombMateria }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('docente.asistencia.tomar', $grupo->codigoG) }}"
               class="text-xs px-3 py-1.5 rounded-lg border font-medium transition hover:text-white"
               style="border-color: #283342; color: #283342;"
               onmouseover="this.style.backgroundColor='#283342'"
               onmouseout="this.style.backgroundColor=''">
                Tomar lista
            </a>
        </div>
        @empty
        <div class="px-6 py-10 text-center text-gray-400 text-sm">
            <p>No tienes grupos asignados todavía.</p>
            <p class="text-xs mt-1">El administrador te asignará a un grupo desde Asignación Docente.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
