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
                    <h3 class="font-semibold text-gray-800">{{ $item['carrera'] }}</h3>
                    <p class="text-xs text-gray-400">{{ $item['modalidad'] }}</p>
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
                    <p>Inscritos validados (1ra opción): <strong>{{ $item['inscritos'] }}</strong></p>
                    <p>Cupos máx. por grupo (esta carrera): <strong>{{ $item['cuposMax'] }}</strong></p>
                    <p>Grupos necesarios: ceil({{ $item['inscritos'] }} ÷ {{ $item['cuposMax'] }}) = <strong>{{ $item['numGrupos'] }}</strong></p>
                    @php
                        $base  = (int) floor($item['inscritos'] / $item['numGrupos']);
                        $resto = $item['inscritos'] % $item['numGrupos'];
                    @endphp
                    <p>Capacidad base: floor({{ $item['inscritos'] }} ÷ {{ $item['numGrupos'] }}) = <strong>{{ $base }}</strong></p>
                    <p>Grupos con +1 alumno: <strong>{{ $resto }}</strong></p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($item['grupos'] as $g)
                    <div class="border border-gray-200 rounded-lg px-4 py-2 text-center min-w-[100px]">
                        <p class="text-xs text-gray-400">Grupo #{{ $g['codigoG'] }}</p>
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
