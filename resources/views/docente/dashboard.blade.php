@extends('layouts.app')

@section('title', 'Docente – Dashboard')
@section('page-title', 'Panel Docente')

@section('content')
@php
    use Illuminate\Support\Facades\DB;
    $docente = Auth::user()->docente;
    if ($docente) {
        $grupoIds = DB::table('materi_grupos')
            ->where('codigoDoc', $docente->codigoDoc)
            ->distinct()->pluck('codigoG');
        $grupos = \App\Models\Grupo::whereIn('codigoG', $grupoIds)
            ->with([
                'modalidad', 'turno',
                'materiGrupos' => fn($q) => $q
                    ->where('codigoDoc', $docente->codigoDoc)
                    ->with('horario', 'materia'),
            ])->get();
    } else {
        $grupos = collect();
    }
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
        <div class="hidden md:flex items-center gap-2">
            <a href="{{ route('docente.solicitud-materias') }}"
               class="flex items-center gap-2 px-4 py-2.5 bg-white/20 border border-white/30 text-white text-sm font-semibold rounded-xl transition hover:bg-white/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Solicitar materias
            </a>
            <a href="{{ route('docente.calificaciones.index') }}"
               class="flex items-center gap-2 px-4 py-2.5 bg-white/20 border border-white/30 text-white text-sm font-semibold rounded-xl transition hover:bg-white/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Calificaciones
            </a>
            <a href="{{ route('docente.resultados.index') }}"
               class="flex items-center gap-2 px-4 py-2.5 bg-white/20 border border-white/30 text-white text-sm font-semibold rounded-xl transition hover:bg-white/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                Resultados
            </a>
            <a href="{{ route('docente.asistencia.index') }}"
               class="flex items-center gap-2 px-4 py-2.5 bg-white text-sm font-semibold rounded-xl transition hover:bg-gray-100"
               style="color: #283342;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Tomar asistencia
            </a>
        </div>
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
                {{ $grupos->flatMap->materiGrupos->unique('idMateria')->count() }}
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
                     style="background-color: #283342;">
                    {{ $grupo->numero_grupo ?? '#'.$grupo->codigoG }}
                </div>
                <div>
                    <p class="font-medium text-gray-800">
                        {{ $grupo->modalidad?->nombModalidad }} — {{ $grupo->turno?->nombTurno }}
                        <span class="text-gray-400 font-normal text-sm">({{ $grupo->capacidad }} cupos)</span>
                    </p>
                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
                        @foreach($grupo->materiGrupos as $mg)
                            @if($mg->materia)
                                <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
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
            <div class="flex items-center gap-2 shrink-0">
                @foreach($grupo->materiGrupos as $mg)
                    @if($mg->materia)
                    <a href="{{ route('docente.calificaciones.edit', [$grupo->codigoG, $mg->idMateria]) }}"
                       class="text-xs px-2.5 py-1.5 rounded-lg border font-medium transition hover:bg-blue-50"
                       style="border-color: #3b82f6; color: #3b82f6;"
                       title="Notas: {{ $mg->materia->nombMateria }}">
                        Notas
                    </a>
                    @endif
                @endforeach
                <a href="{{ route('docente.asistencia.tomar', $grupo->codigoG) }}"
                   class="text-xs px-3 py-1.5 rounded-lg border font-medium transition hover:text-white"
                   style="border-color: #283342; color: #283342;"
                   onmouseover="this.style.backgroundColor='#283342'"
                   onmouseout="this.style.backgroundColor=''">
                    Tomar lista
                </a>
            </div>
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
