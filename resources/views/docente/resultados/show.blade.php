@extends('layouts.app')

@section('title', 'Resultados – Grupo ' . ($grupoModel->numero_grupo ?? $grupoModel->codigoG))
@section('page-title', 'CU12 – Procesar Resultados')

@section('content')
<div class="space-y-5">

    {{-- Encabezado --}}
    <div class="rounded-2xl p-6 text-white"
         style="background: linear-gradient(135deg, #283342 0%, #3d5068 100%);">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold">
                    Grupo {{ $grupoModel->numero_grupo ?? $grupoModel->codigoG }}
                    <span class="font-normal text-white/70 text-base">
                        — {{ $grupoModel->modalidad?->nombModalidad }} / {{ $grupoModel->turno?->nombTurno }}
                    </span>
                </h2>
                @if($gestion)
                <p class="text-white/60 text-sm mt-1">
                    {{ $gestion->nombre }} ·
                    {{ $gestion->fecha_ini->format('d/m/Y') }} – {{ $gestion->fecha_fin->format('d/m/Y') }}
                </p>
                @endif
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @if($periodoAbierto)
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium bg-emerald-400/30 text-white px-3 py-1.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                        Período abierto
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium bg-red-400/30 text-white px-3 py-1.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-300"></span>
                        Período cerrado
                    </span>
                @endif
                <a href="{{ route('docente.resultados.index') }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-white/20 border border-white/30 text-white text-sm font-semibold rounded-xl transition hover:bg-white/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>
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
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-3 text-sm">
            @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    {{-- Bloqueo período cerrado --}}
    @if(!$periodoAbierto)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-5 py-4 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <p class="font-semibold">Período de evaluación cerrado</p>
                <p class="text-amber-700 mt-0.5">Solo es posible consultar los resultados ya calculados. No se puede reprocesar.</p>
            </div>
        </div>
    @endif

    {{-- Sin exámenes --}}
    @if(!empty($sinExamenes) && $sinExamenes)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
            <p class="text-gray-500 font-medium">No hay exámenes configurados para la gestión activa.</p>
            <p class="text-xs text-gray-400 mt-1">El administrador debe crear los exámenes antes de procesar resultados.</p>
        </div>

    {{-- Sin estudiantes --}}
    @elseif($inscripciones->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
            <p class="text-gray-500 font-medium">No hay estudiantes inscritos en este grupo.</p>
        </div>

    @else
    {{-- Tabla de resultados --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-800">Resultados por estudiante</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    Promedio ponderado: Σ(nota × ponderación) / Σ(ponderación) · Aprobado ≥ 60
                </p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aprobado ≥60
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-700 font-medium">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Reprobado
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 font-medium">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> Incompleto
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">Estudiante</th>
                        @foreach($examenes as $ex)
                        <th class="px-4 py-3 text-center font-medium">
                            {{ $ex->descripcion }}
                            <span class="block text-gray-400 normal-case font-normal">({{ $ex->ponderacion }}%)</span>
                        </th>
                        @endforeach
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Promedio</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Estado</th>
                        <th class="px-4 py-3 text-center font-medium">Notas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($inscripciones as $insc)
                    @php
                        $res      = $resultados[$insc->idInscripcion] ?? null;
                        $promedio = $res['promedio']  ?? null;
                        $resText  = $res['resultado'] ?? null;
                        $completo = $res['completo']  ?? false;
                        $guardado = $res['guardado']  ?? false;

                        $rowClass = match($resText) {
                            'Aprobado'   => 'bg-emerald-50/40',
                            'Reprobado'  => 'bg-red-50/40',
                            'Incompleto' => 'bg-amber-50/40',
                            default      => '',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition {{ $rowClass }}">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-gray-900">
                                {{ $insc->postulante?->apellidos }}, {{ $insc->postulante?->nombre }}
                            </p>
                            <p class="text-xs text-gray-400">CI: {{ $insc->postulante?->ci }}</p>
                        </td>
                        @foreach($examenes as $ex)
                        @php $val = $res['porExamen'][$ex->idExamen] ?? null; @endphp
                        <td class="px-4 py-3.5 text-center">
                            @if($val !== null)
                                <span class="{{ $val >= 60 ? 'text-emerald-700 font-semibold' : 'text-red-600 font-semibold' }}">
                                    {{ number_format($val, 2) }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="px-4 py-3.5 text-center">
                            @if($promedio !== null)
                                <span class="text-base font-bold {{ $promedio >= 60 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ number_format($promedio, 2) }}
                                </span>
                                @if($guardado && $insc->promedio !== null)
                                    <span class="block text-xs text-gray-400">guardado: {{ number_format($insc->promedio, 2) }}</span>
                                @endif
                            @else
                                <span class="text-gray-300 text-lg">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($resText === 'Aprobado')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    ✓ Aprobado
                                </span>
                            @elseif($resText === 'Reprobado')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    ✗ Reprobado
                                </span>
                            @elseif($resText === 'Incompleto')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                    ⚠ Incompleto
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">Sin notas</span>
                            @endif
                            {{-- Estado ya guardado en BD --}}
                            @if($insc->resultado && $insc->resultado !== $resText)
                                <p class="text-xs text-gray-400 mt-0.5">BD: {{ $insc->resultado }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($completo)
                                <span class="text-emerald-600 text-xs font-medium">Completas</span>
                            @elseif(($res['totalNotas'] ?? 0) > 0)
                                <span class="text-amber-600 text-xs">{{ $res['totalNotas'] ?? 0 }}/{{ $expectedCount }}</span>
                            @else
                                <span class="text-gray-400 text-xs">Sin notas</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Resumen pie de tabla --}}
        @php
            $nAprobados   = collect($resultados)->where('resultado', 'Aprobado')->count();
            $nReprobados  = collect($resultados)->where('resultado', 'Reprobado')->count();
            $nIncompletos = collect($resultados)->where('resultado', 'Incompleto')->count();
            $nSinNotas    = collect($resultados)->whereNull('resultado')->count();
        @endphp
        <div class="px-6 py-4 bg-gray-50 border-t flex flex-wrap gap-4 text-xs text-gray-600">
            <span>Total: <strong>{{ $inscripciones->count() }}</strong></span>
            <span class="text-emerald-700">Aprobados: <strong>{{ $nAprobados }}</strong></span>
            <span class="text-red-700">Reprobados: <strong>{{ $nReprobados }}</strong></span>
            @if($nIncompletos > 0)
            <span class="text-amber-700">Incompletos: <strong>{{ $nIncompletos }}</strong></span>
            @endif
            @if($nSinNotas > 0)
            <span class="text-gray-500">Sin notas: <strong>{{ $nSinNotas }}</strong></span>
            @endif
        </div>
    </div>

    {{-- Acción: Procesar --}}
    @if($periodoAbierto)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-gray-800">¿Confirmar procesamiento?</p>
                <p class="text-sm text-gray-500 mt-0.5">
                    Se guardarán los promedios y estados mostrados arriba para
                    <strong>{{ $inscripciones->count() }}</strong> estudiante(s).
                    @if($nIncompletos > 0)
                        <span class="text-amber-600">
                            {{ $nIncompletos }} con calificaciones incompletas serán marcados como 'Incompleto'.
                        </span>
                    @endif
                </p>
            </div>
            <form method="POST"
                  action="{{ route('docente.resultados.procesar', $grupoModel->codigoG) }}"
                  onsubmit="return confirm('¿Confirmar el procesamiento de resultados para este grupo?');">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 px-6 py-3 rounded-xl text-white text-sm font-bold transition hover:opacity-90 shrink-0"
                        style="background-color: #283342;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Procesar y guardar resultados
                </button>
            </form>
        </div>
    </div>
    @endif

    @endif {{-- fin @if inscripciones --}}

</div>
@endsection
