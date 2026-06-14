@extends('layouts.app')

@section('title', 'Resultado – Apertura de Grupos')
@section('page-title', 'CU09 – Resultado de la Apertura')

@section('content')
<div class="space-y-6">

    <div class="rounded-xl p-6 text-white" style="background-color: #283342;">
        <h2 class="text-xl font-bold mb-1">Apertura completada</h2>
        <p class="text-white/70 text-sm">
            Gestión #{{ $gestion->idGestion }} —
            {{ $gestion->fecha_ini->format('d/m/Y') }} al {{ $gestion->fecha_fin->format('d/m/Y') }}
        </p>
        <div class="mt-4">
            <div class="bg-white/10 rounded-lg p-3 text-center inline-block min-w-[140px]">
                <p class="text-3xl font-bold">{{ $totalGrupos }}</p>
                <p class="text-xs text-white/60 mt-1">Grupos creados en total</p>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($resumen as $item)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-800">Modalidad: {{ $item['modalidad'] }}</h3>
                    <p class="text-xs text-gray-400">Grupos mixtos — postulantes de todas las carreras en esta modalidad</p>
                </div>
                <span class="text-sm font-medium {{ $item['numGrupos'] > 0 ? 'text-green-700' : 'text-gray-400' }}">
                    {{ $item['numGrupos'] }} grupo(s)
                </span>
            </div>

            @if($item['numGrupos'] === 0)
                <p class="px-6 py-3 text-sm text-gray-400 italic">{{ $item['mensaje'] }}</p>
            @else
            <div class="px-6 py-4">
                <div class="bg-gray-50 rounded-lg p-3 mb-4 text-xs text-gray-600 font-mono space-y-1">
                    <p>Inscritos validados ({{ $item['modalidad'] }}): <strong>{{ $item['inscritos'] }}</strong></p>
                    <p>Capacidad máx. por grupo: <strong>{{ $item['capacidadPorGrupo'] }}</strong></p>
                    <p>Grupos creados: ⌈{{ $item['inscritos'] }} ÷ {{ $item['capacidadPorGrupo'] }}⌉ = <strong>{{ $item['numGrupos'] }}</strong></p>
                    @php
                        $resto = $item['inscritos'] % $item['capacidadPorGrupo'];
                    @endphp
                    <p>Distribución: {{ $resto > 0
                        ? ($item['numGrupos'] - 1).' grupo(s) de '.$item['capacidadPorGrupo'].' + 1 grupo de '.$resto
                        : $item['numGrupos'].' grupo(s) de '.$item['capacidadPorGrupo'] }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($item['grupos'] as $g)
                    <div class="border border-gray-200 rounded-lg px-4 py-2 text-center min-w-[100px]">
                        <p class="text-xs text-gray-500 font-medium">{{ $g['numero_grupo'] }}</p>
                        <p class="text-lg font-bold text-gray-800">{{ $g['capacidad'] }}</p>
                        <p class="text-xs text-gray-400">alumnos</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <a href="{{ route('admin.grupos.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded-lg text-white transition hover:opacity-90"
       style="background-color: #283342;">
        ← Volver
    </a>
</div>
@endsection
