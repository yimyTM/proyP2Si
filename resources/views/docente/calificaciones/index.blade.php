@extends('layouts.app')

@section('title', 'Mis Calificaciones')
@section('page-title', 'CU11 – Registro de Calificaciones')

@section('content')
<div class="space-y-5">

    {{-- Encabezado --}}
    <div class="rounded-2xl p-6 text-white flex items-center justify-between"
         style="background: linear-gradient(135deg, #283342 0%, #3d5068 100%);">
        <div>
            <h2 class="text-xl font-bold">Calificaciones</h2>
            <p class="text-white/60 text-sm mt-1">
                Selecciona el grupo y materia para ingresar o revisar notas.
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

    @if($asignaciones->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-gray-50">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">No tiene grupos asignados en la gestión actual.</p>
            <p class="text-xs text-gray-400 mt-1">El administrador debe asignarle grupos desde la asignación docente.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($asignaciones as $asig)
            @php
                $g       = $asig->grupo;
                $gestion = $g?->gestion;
                $abierta = $gestion && $gestion->estado === 'Abierta';
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">

                {{-- Cabecera de la tarjeta --}}
                <div class="px-5 py-4 border-b flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0"
                         style="background-color: #283342;">
                        {{ $g?->numero_grupo ?? '#'.$asig->codigoG }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $asig->materia?->nombMateria ?? '—' }}</p>
                        <p class="text-xs text-gray-400 truncate">
                            Grupo {{ $g?->numero_grupo }} ·
                            {{ $g?->modalidad?->nombModalidad }} /
                            {{ $g?->turno?->nombTurno }}
                        </p>
                    </div>
                </div>

                {{-- Estadísticas --}}
                <div class="grid grid-cols-3 divide-x divide-gray-100 text-center py-3">
                    <div>
                        <p class="text-lg font-bold text-gray-800">{{ $asig->totalEstudiantes }}</p>
                        <p class="text-xs text-gray-400">Estudiantes</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-800">{{ $asig->totalExamenes }}</p>
                        <p class="text-xs text-gray-400">Exámenes</p>
                    </div>
                    <div>
                        @if($abierta)
                            <p class="text-lg font-bold text-emerald-600">Abierta</p>
                        @else
                            <p class="text-lg font-bold text-red-500">Cerrada</p>
                        @endif
                        <p class="text-xs text-gray-400">Gestión</p>
                    </div>
                </div>

                {{-- Gestión info --}}
                @if($gestion)
                <div class="px-5 py-2 bg-gray-50 text-xs text-gray-500">
                    {{ $gestion->nombre }} ·
                    {{ $gestion->fecha_ini->format('d/m/Y') }} – {{ $gestion->fecha_fin->format('d/m/Y') }}
                </div>
                @endif

                {{-- Acción --}}
                <div class="px-5 py-4">
                    @if($asig->totalExamenes === 0)
                        <p class="text-xs text-amber-600 text-center">
                            Sin exámenes configurados para esta materia.
                        </p>
                    @elseif($asig->totalEstudiantes === 0)
                        <p class="text-xs text-amber-600 text-center">
                            Este grupo no tiene estudiantes asignados aún.
                        </p>
                    @else
                        <a href="{{ route('docente.calificaciones.edit', [$asig->codigoG, $asig->idMateria]) }}"
                           class="block w-full text-center px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
                           style="background-color: #283342;">
                            {{ $abierta ? 'Ingresar / Editar notas' : 'Ver calificaciones' }}
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
