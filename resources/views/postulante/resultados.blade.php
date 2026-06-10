@extends('layouts.app')

@section('title', 'Mis Resultados | FICCT')
@section('page-title', 'Estado de Admisión y Resultados')

@section('content')

@php
    $error = $error ?? null;
    $estadoResultado = $inscripcion->resultado ?? null;
    $estadoColores = [
        'Aprobado'  => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'dot' => 'bg-green-500',  'label' => 'Aprobado'],
        'Reprobado' => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'dot' => 'bg-red-500',    'label' => 'Reprobado'],
        'Abandonó'  => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'dot' => 'bg-gray-400',   'label' => 'Abandonó'],
    ];
    $colorSet = $estadoColores[$estadoResultado] ?? ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500', 'label' => 'En Curso'];
@endphp

{{-- ── Estados de error ──────────────────────────────────────────────────────── --}}
@if($error === 'sin_inscripcion')
<div class="max-w-xl mx-auto mt-10">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-10 flex flex-col items-center text-center gap-4">
        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-800">Sin inscripción activa</h2>
        <p class="text-sm text-gray-500 max-w-sm">
            No se encontró una inscripción activa para su cuenta
            @if($gestion) en la gestión <strong>{{ $gestion->nombre }}</strong>@endif.
        </p>
        <a href="{{ route('registro') }}"
           class="mt-2 px-6 py-2.5 bg-[#283342] text-white text-sm font-semibold rounded-lg hover:bg-[#1a2430] transition">
            Inscribirme
        </a>
    </div>
</div>

@elseif($error === 'pago_pendiente')
<div class="max-w-xl mx-auto mt-10">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-10 flex flex-col items-center text-center gap-4">
        <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-800">Habilitación no confirmada</h2>
        <p class="text-sm text-gray-500 max-w-sm">
            Su pago no ha sido validado aún. Los resultados estarán disponibles una vez que el área administrativa confirme su habilitación.
        </p>
        <a href="{{ route('verificar-pago') }}"
           class="mt-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition">
            Verificar estado de pago
        </a>
    </div>
</div>

@elseif($error === 'sin_resultados')
<div class="max-w-xl mx-auto mt-10">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-10 flex flex-col items-center text-center gap-4">
        <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center">
            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-800">Resultados aún no disponibles</h2>
        <p class="text-sm text-gray-500 max-w-sm">
            Los resultados de esta gestión aún no están disponibles. Intente más tarde.
        </p>
        @if($gestion)
        <span class="inline-flex items-center gap-1.5 text-xs font-medium bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full border border-blue-200">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
            {{ $gestion->nombre }}
        </span>
        @endif
    </div>
</div>

@else
{{-- ── Vista principal de resultados ──────────────────────────────────────────── --}}

{{-- Encabezado con estado --}}
<div class="rounded-xl p-6 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
     style="background: linear-gradient(135deg, #283342 0%, #1a2430 100%);">
    <div>
        <p class="text-white/60 text-xs font-semibold uppercase tracking-widest mb-1">
            {{ $gestion->nombre ?? 'Gestión' }}
        </p>
        <h2 class="text-xl font-bold text-white">
            {{ $postulante->nombre }} {{ $postulante->apellidos }}
        </h2>
        <p class="text-white/60 text-sm mt-0.5">CI: {{ $postulante->ci }}</p>
    </div>

    <div class="flex flex-col items-end gap-2">
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold {{ $colorSet['bg'] }} {{ $colorSet['text'] }}">
            <span class="w-2 h-2 rounded-full {{ $colorSet['dot'] }}"></span>
            {{ $colorSet['label'] }}
        </span>
        @if($inscripcion->promedio !== null)
        <span class="text-white/80 text-sm font-semibold">
            Promedio final: <span class="text-white font-bold">{{ number_format($inscripcion->promedio, 2) }}</span>
        </span>
        @endif
    </div>
</div>

{{-- Carrera asignada (solo si Aprobado y se ejecutó CU13) --}}
@if($estadoResultado === 'Aprobado' && $carreraAsignada)
<div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-6 flex items-start gap-4">
    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div class="flex-1">
        <p class="text-xs font-semibold text-green-700 uppercase tracking-wider mb-1">Carrera asignada</p>
        <p class="text-base font-bold text-green-800">{{ $carreraAsignada->nombre }}</p>
        @if($carreraAsignada->modalidad)
        <p class="text-sm text-green-700">{{ $carreraAsignada->modalidad->nombModalidad }}</p>
        @endif
        @if($opcionAsignada)
        <span class="inline-block mt-2 text-xs font-semibold bg-green-200 text-green-800 px-2.5 py-0.5 rounded-full">
            Ubicado en {{ $opcionAsignada == 1 ? '1ª opción' : '2ª opción' }}
        </span>
        @endif
    </div>
    <button onclick="window.print()"
            class="hidden sm:flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition print:hidden">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Imprimir constancia
    </button>
</div>
@endif

{{-- Calificaciones por materia --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-sm">Calificaciones por materia</h3>
        <span class="text-xs text-gray-400">{{ $examenes->count() }} examen(es) registrado(s)</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <th class="px-5 py-3 text-left font-semibold">Materia</th>
                    @foreach($examenes as $examen)
                    <th class="px-4 py-3 text-center font-semibold">
                        {{ $examen->descripcion }}
                        @if($examen->ponderacion)
                        <span class="block text-gray-400 font-normal normal-case text-xs">({{ $examen->ponderacion }}%)</span>
                        @endif
                    </th>
                    @endforeach
                    <th class="px-5 py-3 text-center font-semibold">Suma</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($materias as $materia)
                @php
                    $sumaNotas = $materia->parciales->sum('calificacion');
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5 font-semibold text-gray-800">{{ $materia->nombre }}</td>
                    @foreach($examenes as $examen)
                    @php
                        $nota = $materia->parciales->get($examen->nroParcial);
                    @endphp
                    <td class="px-4 py-3.5 text-center">
                        @if($nota)
                        @php
                            $puntMax = $nota->examMateria->puntaje ?? 100;
                            $pct     = $puntMax > 0 ? ($nota->calificacion / $puntMax) * 100 : 0;
                            $notaColor = $pct >= 60 ? 'text-green-700 bg-green-50' : 'text-red-600 bg-red-50';
                        @endphp
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $notaColor }}">
                            {{ number_format($nota->calificacion, 1) }}
                            <span class="font-normal text-gray-400">/ {{ $puntMax }}</span>
                        </span>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    @endforeach
                    <td class="px-5 py-3.5 text-center font-bold
                        {{ $sumaNotas > 0 ? 'text-gray-800' : 'text-gray-300' }}">
                        {{ $sumaNotas > 0 ? number_format($sumaNotas, 1) : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Preferencias de carrera registradas --}}
@if($inscripcion->carrerasInscritas->isNotEmpty())
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
    <h3 class="font-semibold text-gray-800 text-sm mb-3">Preferencias de carrera registradas</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach($inscripcion->carrerasInscritas->sortBy('prioridad') as $ci)
        <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 border border-gray-200">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                {{ $ci->prioridad == 1 ? 'bg-[#283342] text-white' : 'bg-gray-200 text-gray-600' }}">
                {{ $ci->prioridad }}
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">{{ $ci->carrera->nombre ?? 'N/D' }}</p>
                @if($ci->carrera?->modalidad)
                <p class="text-xs text-gray-500">{{ $ci->carrera->modalidad->nombModalidad }}</p>
                @endif
            </div>
            @if($carreraAsignada && $ci->codCarrera === $carreraAsignada->codCarrera)
            <span class="ml-auto text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">Asignada</span>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

@endif

@endsection

@push('styles')
<style>
@media print {
    aside, header, .print\:hidden { display: none !important; }
    body { background: white; }
    main { padding: 0 !important; }
}
</style>
@endpush
