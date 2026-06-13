@extends('layouts.app')

@section('title', 'Admisión – ' . $gestionModel->nombre)
@section('page-title', 'CU13 – Gestionar Cupos y Reubicación')

@section('content')
<div class="space-y-5">

    {{-- Encabezado --}}
    <div class="rounded-2xl p-6 text-white"
         style="background: linear-gradient(135deg, #283342 0%, #3d5068 100%);">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold">{{ $gestionModel->nombre }}</h2>
                <p class="text-white/60 text-sm mt-1">
                    {{ $gestionModel->fecha_ini->format('d/m/Y') }} – {{ $gestionModel->fecha_fin->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @if($gestionModel->estado === 'Abierta')
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
                <a href="{{ route('admin.admision.index') }}"
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

    {{-- Advertencia período cerrado --}}
    @if($gestionModel->estado !== 'Abierta')
        <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-5 py-4 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <p class="font-semibold">Período de evaluación cerrado</p>
                <p class="text-amber-700 mt-0.5">Solo se puede consultar la asignación ya registrada. No es posible reprocesar.</p>
            </div>
        </div>
    @endif

    {{-- Resumen de cupos por carrera --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($resumen as $cod => $r)
        @php
            $pctUsado = $r['cuposTotal'] > 0
                ? round(($r['asignados'] / $r['cuposTotal']) * 100)
                : 0;
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1 truncate"
               title="{{ $r['carrera']?->nombre }}">
                {{ \Illuminate\Support\Str::limit($r['carrera']?->nombre ?? 'Carrera '.$cod, 24) }}
            </p>
            <div class="flex items-end justify-between gap-2 mt-1">
                <div>
                    <p class="text-2xl font-extrabold" style="color: #283342;">{{ $r['asignados'] }}</p>
                    <p class="text-xs text-gray-400">de {{ $r['cuposTotal'] }} cupos</p>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $r['cuposRestantes'] <= 0 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                    {{ $r['cuposRestantes'] }} libres
                </span>
            </div>
            <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full {{ $pctUsado >= 100 ? 'bg-red-500' : ($pctUsado >= 75 ? 'bg-amber-400' : 'bg-emerald-500') }}"
                     style="width: {{ min($pctUsado, 100) }}%"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Sin postulantes --}}
    @php
        $totalPostulantes = collect($asignaciones)->count();
        $totalAprobados   = collect($asignaciones)->where('estado_admision', '!=', 'No admitido')->count();
    @endphp
    @if($totalPostulantes === 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
            <p class="text-gray-500 font-medium">No hay postulantes con resultados académicos procesados.</p>
            <p class="text-xs text-gray-400 mt-1">Ejecute primero el procesamiento de promedios desde el panel Docente.</p>
        </div>
    @else

    {{-- Leyenda --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-semibold text-gray-800">Listado de admisión por postulante</h3>
                <p class="text-xs text-gray-400 mt-0.5">Ordenado por promedio final descendente. La asignación es automática según cupos disponibles.</p>
            </div>
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Admitido (1ª opción)
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 font-medium">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Reubicado (2ª opción)
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-700 font-medium">
                    <span class="w-2 h-2 rounded-full bg-red-400"></span> Reprobado (sin cupo o puntaje insuficiente)
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">#</th>
                        <th class="px-5 py-3 text-left font-medium">Postulante</th>
                        <th class="px-4 py-3 text-center font-medium">Promedio</th>
                        <th class="px-4 py-3 text-center font-medium">Resultado</th>
                        <th class="px-4 py-3 text-left font-medium">1ª Opción</th>
                        <th class="px-4 py-3 text-left font-medium">2ª Opción</th>
                        <th class="px-4 py-3 text-left font-medium">Carrera Asignada</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Estado Admisión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $rank = 0; @endphp
                    @foreach($asignaciones as $idInsc => $data)
                    @php
                        $insc   = $data['inscripcion'];
                        $est    = $data['estado_admision'];
                        $p1     = $data['prioridad1'];
                        $p2     = $data['prioridad2'];
                        $carAsg = $data['carreraNombre'];

                        if ($est !== 'Reprobado') $rank++;

                        $rowClass = match($est) {
                            'Admitido'  => 'bg-emerald-50/40',
                            'Reubicado' => 'bg-blue-50/40',
                            'Reprobado' => 'bg-red-50/30',
                            default     => '',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition {{ $rowClass }}">
                        <td class="px-5 py-3.5 text-gray-400 text-xs font-mono">
                            {{ $est !== 'Reprobado' ? $rank : '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                @if($insc->postulante?->foto)
                                <div class="w-9 h-9 rounded-full overflow-hidden shrink-0 border border-gray-200">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($insc->postulante->foto) }}"
                                         alt="{{ $insc->postulante->nombre_completo }}"
                                         class="w-full h-full object-cover">
                                </div>
                                @else
                                <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center shrink-0 text-gray-400 text-xs font-bold border border-gray-200">
                                    {{ strtoupper(mb_substr($insc->postulante?->nombre ?? '?', 0, 1) . mb_substr($insc->postulante?->apellidos ?? '', 0, 1)) }}
                                </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $insc->postulante?->apellidos }}, {{ $insc->postulante?->nombre }}
                                    </p>
                                    <p class="text-xs text-gray-400">CI: {{ $insc->postulante?->ci }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($insc->promedio !== null)
                                <span class="font-bold {{ (float)$insc->promedio >= 60 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ number_format($insc->promedio, 2) }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($insc->resultado === 'Aprobado')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">✓ Aprobado</span>
                            @elseif($insc->resultado === 'Reprobado')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">✗ Reprobado</span>
                            @elseif($insc->resultado === 'Incompleto')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">⚠ Incompleto</span>
                            @else
                                <span class="text-gray-400 text-xs">Sin datos</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-600">
                            {{ \Illuminate\Support\Str::limit($p1?->nombre ?? '—', 28) }}
                        </td>
                        <td class="px-4 py-3.5 text-xs text-gray-500">
                            {{ \Illuminate\Support\Str::limit($p2?->nombre ?? '—', 28) }}
                        </td>
                        <td class="px-4 py-3.5 text-xs font-medium">
                            @if($carAsg)
                                <span class="{{ $est === 'Reubicado' ? 'text-blue-700' : 'text-emerald-700' }}">
                                    {{ \Illuminate\Support\Str::limit($carAsg, 28) }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($est === 'Admitido')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    ✓ Admitido · 1ª opción
                                </span>
                            @elseif($est === 'Reubicado')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                    ⇄ Reubicado · 2ª opción
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    ✗ Reprobado
                                </span>
                            @endif
                            {{-- Mostrar lo ya guardado en BD si difiere --}}
                            @if($insc->estado_admision && $insc->estado_admision !== $est)
                                <p class="text-xs text-gray-400 mt-0.5">BD: {{ $insc->estado_admision }}</p>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pie de tabla: resumen contadores --}}
        @php
            $nAdmitidos  = collect($asignaciones)->where('estado_admision', 'Admitido')->count();
            $nReubicados = collect($asignaciones)->where('estado_admision', 'Reubicado')->count();
            $nReprobados = collect($asignaciones)->where('estado_admision', 'Reprobado')->count();
        @endphp
        <div class="px-6 py-4 bg-gray-50 border-t flex flex-wrap gap-4 text-xs text-gray-600">
            <span>Total postulantes: <strong>{{ count($asignaciones) }}</strong></span>
            <span class="text-emerald-700">Admitidos (1ª opción): <strong>{{ $nAdmitidos }}</strong></span>
            <span class="text-blue-700">Reubicados (2ª opción): <strong>{{ $nReubicados }}</strong></span>
            @if($nReprobados > 0)
            <span class="text-red-700">Reprobados: <strong>{{ $nReprobados }}</strong></span>
            @endif
        </div>
    </div>

    {{-- Acción: Procesar --}}
    @if($gestionModel->estado === 'Abierta')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-gray-800">¿Confirmar asignación de cupos?</p>
                <p class="text-sm text-gray-500 mt-0.5">
                    Se guardarán los estados de admisión mostrados arriba para
                    <strong>{{ count($asignaciones) }}</strong> postulante(s).
                    @if($nReprobados > 0)
                        <span class="text-red-600">
                            {{ $nReprobados }} quedarán reprobados (sin cupo o puntaje insuficiente).
                        </span>
                    @endif
                </p>
            </div>
            <form method="POST"
                  action="{{ route('admin.admision.procesar', $gestionModel->idGestion) }}"
                  onsubmit="return confirm('¿Confirmar el procesamiento de admisión para esta gestión?');">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 px-6 py-3 rounded-xl text-white text-sm font-bold transition hover:opacity-90 shrink-0"
                        style="background-color: #283342;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Procesar y guardar admisión
                </button>
            </form>
        </div>
    </div>
    @endif

    @endif {{-- fin @if totalPostulantes --}}

</div>
@endsection
