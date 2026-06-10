@extends('layouts.app')

@section('title', 'Distribuir Postulantes – CU17')
@section('page-title', 'CU17 – Distribución de Postulantes en Grupos')

@section('content')
<div class="space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.grupos.index') }}" class="hover:text-[#283342] transition">Grupos</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-800 font-medium">Distribuir postulantes</span>
    </div>

    {{-- Flash --}}
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Gestión activa --}}
    <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-medium text-white" style="background-color: #283342;">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Gestión activa: <strong class="ml-1">{{ $gestion->nombre ?? "ID {$gestion->idGestion}" }}</strong>
    </div>

    {{-- Alerta redistribución --}}
    @if($yaDistribuido)
    <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="font-semibold">Ya existe una distribución confirmada</p>
            <p class="text-amber-700 mt-0.5">Confirmar redistribuirá eliminando las asignaciones actuales y recalculará la distribución desde cero.</p>
        </div>
    </div>
    @endif

    {{-- Alerta capacidad insuficiente --}}
    @if(!$capacidadOk)
    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="font-semibold">Capacidad insuficiente</p>
            <p class="text-red-700 mt-0.5">
                La cantidad de postulantes (<strong>{{ $totalPostulantes }}</strong>) excede la capacidad total de los grupos (<strong>{{ $capacidadTotal }}</strong>).
                Se requiere abrir grupos adicionales (CU06) antes de distribuir.
            </p>
        </div>
    </div>
    @endif

    {{-- Resumen estadístico --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-[#283342]">{{ $totalPostulantes }}</p>
            <p class="text-xs text-gray-500 mt-1">Postulantes a distribuir</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-[#283342]">{{ $grupos->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Grupos disponibles</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-[#283342]">{{ $capacidadTotal }}</p>
            <p class="text-xs text-gray-500 mt-1">Capacidad total</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            @if($capacidadOk)
                <p class="text-2xl font-bold text-green-600">{{ $totalPostulantes > 0 ? number_format(($totalPostulantes / $capacidadTotal) * 100, 1) : 0 }}%</p>
                <p class="text-xs text-gray-500 mt-1">Ocupación total</p>
            @else
                <p class="text-2xl font-bold text-red-600">{{ number_format(($totalPostulantes / $capacidadTotal) * 100, 1) }}%</p>
                <p class="text-xs text-red-500 mt-1">Capacidad excedida</p>
            @endif
        </div>
    </div>

    @if($capacidadOk)
    {{-- Vista previa por grupo --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 text-sm">Vista previa de distribución</h3>
            <p class="text-xs text-gray-400 mt-0.5">Distribución equitativa ordenada alfabéticamente por apellido</p>
        </div>

        {{-- Tabla resumen de grupos --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Grupo</th>
                        <th class="px-5 py-3 text-left">Turno</th>
                        <th class="px-5 py-3 text-left">Modalidad</th>
                        <th class="px-5 py-3 text-center">Capacidad</th>
                        <th class="px-5 py-3 text-center">Asignados</th>
                        <th class="px-5 py-3 text-center">Ocupación</th>
                        <th class="px-5 py-3 text-center">Lista</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($grupos as $grupo)
                    @php
                        $asignados = $distribucion[$grupo->codigoG]->count();
                        $pct = $grupo->capacidad > 0 ? ($asignados / $grupo->capacidad) * 100 : 0;
                        $barColor = $pct >= 90 ? 'bg-red-400' : ($pct >= 70 ? 'bg-amber-400' : 'bg-green-400');
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3.5">
                            <span class="font-bold text-gray-800">{{ $grupo->numero_grupo }}</span>
                            <span class="text-xs text-gray-400 ml-1">#{{ $grupo->codigoG }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-600 text-xs">{{ $grupo->turno?->nombTurno ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                                {{ $grupo->modalidad?->nombModalidad ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center text-gray-600">{{ $grupo->capacidad }}</td>
                        <td class="px-5 py-3.5 text-center font-semibold text-gray-800">{{ $asignados }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2 justify-center">
                                <div class="w-20 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="{{ $barColor }} h-full rounded-full transition-all"
                                         style="width: {{ min($pct, 100) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-10 text-right">{{ number_format($pct, 0) }}%</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($asignados > 0)
                            <details class="text-left">
                                <summary class="cursor-pointer text-xs text-[#283342] font-medium hover:underline list-none flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver lista
                                </summary>
                                <div class="absolute z-10 mt-1 ml-[-160px] w-64 bg-white border border-gray-200 rounded-xl shadow-lg p-3 text-xs">
                                    <p class="font-semibold text-gray-700 mb-2 border-b border-gray-100 pb-1.5">
                                        {{ $grupo->numero_grupo }} — {{ $asignados }} estudiante(s)
                                    </p>
                                    <ol class="space-y-1 list-decimal list-inside max-h-48 overflow-y-auto">
                                        @foreach($distribucion[$grupo->codigoG] as $insc)
                                        <li class="text-gray-700 leading-snug">
                                            {{ $insc->postulante->apellidos }}, {{ $insc->postulante->nombre }}
                                            <span class="text-gray-400">· {{ $insc->postulante->ci }}</span>
                                        </li>
                                        @endforeach
                                    </ol>
                                </div>
                            </details>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Nóminas expandidas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($grupos as $grupo)
        @php $lista = $distribucion[$grupo->codigoG]; @endphp
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 flex items-center justify-between" style="background-color: #283342;">
                <div>
                    <p class="text-white font-bold text-sm">{{ $grupo->numero_grupo }}</p>
                    <p class="text-white/60 text-xs">{{ $grupo->turno?->nombTurno }} · {{ $grupo->modalidad?->nombModalidad }}</p>
                </div>
                <span class="text-white/80 text-xs font-semibold bg-white/10 px-2.5 py-1 rounded-full">
                    {{ $lista->count() }}/{{ $grupo->capacidad }}
                </span>
            </div>
            @if($lista->isEmpty())
                <p class="px-4 py-4 text-xs text-gray-400 text-center">Sin postulantes asignados</p>
            @else
            <ol class="divide-y divide-gray-50">
                @foreach($lista as $j => $insc)
                <li class="px-4 py-2 flex items-center gap-3 text-xs">
                    <span class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-semibold shrink-0 text-[10px]">
                        {{ $j + 1 }}
                    </span>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ $insc->postulante->apellidos }}, {{ $insc->postulante->nombre }}</p>
                        <p class="text-gray-400">CI: {{ $insc->postulante->ci }}</p>
                    </div>
                </li>
                @endforeach
            </ol>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Botón de confirmación --}}
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4">
        <div>
            <p class="text-sm font-semibold text-gray-800">
                @if($yaDistribuido) Redistribuir {{ $totalPostulantes }} postulantes
                @else Confirmar distribución de {{ $totalPostulantes }} postulantes
                @endif
            </p>
            <p class="text-xs text-gray-400 mt-0.5">Esta acción actualizará la base de datos con las asignaciones mostradas arriba.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.grupos.index') }}"
               class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 font-medium transition">
                Cancelar
            </a>
            <form method="POST" action="{{ route('admin.grupos.distribuir.confirmar') }}"
                  onsubmit="return confirm('{{ $yaDistribuido ? '¿Redistribuir? Las asignaciones actuales serán eliminadas y recalculadas.' : '¿Confirmar la distribución de '.$totalPostulantes.' postulantes?' }}')">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90
                               {{ $yaDistribuido ? 'bg-amber-500 hover:bg-amber-600' : '' }}"
                        style="{{ $yaDistribuido ? '' : 'background-color: #283342;' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $yaDistribuido ? 'Redistribuir' : 'Confirmar distribución' }}
                </button>
            </form>
        </div>
    </div>

    @endif {{-- capacidadOk --}}

    {{-- Si capacidad insuficiente: solo botón cancelar --}}
    @if(!$capacidadOk)
    <div class="flex justify-end">
        <a href="{{ route('admin.grupos.index') }}"
           class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-700 font-medium hover:bg-gray-50 transition">
            Volver a Grupos
        </a>
    </div>
    @endif

</div>
@endsection
