@extends('layouts.app')

@section('title', 'Reportes')
@section('page-title', 'CU14 – Panel de Reportes')

@section('content')
<div class="space-y-5">

    {{-- Encabezado --}}
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #283342 0%, #3d5068 100%);">
        <h2 class="text-xl font-bold">Panel de Reportes</h2>
        <p class="text-white/60 text-sm mt-1">Seleccione un reporte, aplique filtros y expórtelo en PDF o CSV.</p>
    </div>

    @if(! $gestionModel)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center text-gray-400 text-sm">
            No hay gestiones registradas para generar reportes.
        </div>
    @else

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">

        {{-- ── Panel de filtros (izquierda) ──────────────────────────────── --}}
        <div class="lg:col-span-1 space-y-4">
            <form method="GET" action="{{ route('admin.reportes.index') }}" id="filtros"
                  class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Gestión</label>
                    <select name="gestion" onchange="document.getElementById('filtros').submit()"
                            class="w-full text-sm rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#283342]/20">
                        @foreach($gestiones as $g)
                            <option value="{{ $g->idGestion }}" {{ $gestionModel->idGestion === $g->idGestion ? 'selected' : '' }}>
                                {{ $g->nombre }} ({{ $g->estado }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Reporte</label>
                    <div class="space-y-1">
                        @foreach($reportes as $clave => $titulo)
                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer text-sm transition
                                      {{ $reporteClave === $clave ? 'bg-[#283342] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <input type="radio" name="reporte" value="{{ $clave }}"
                                   onchange="document.getElementById('filtros').submit()"
                                   {{ $reporteClave === $clave ? 'checked' : '' }} class="hidden">
                            <span class="w-5 text-xs opacity-70">{{ $loop->iteration }}.</span>
                            <span>{{ $titulo }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="{{ $filtraGrupo ? '' : 'opacity-40 pointer-events-none' }}">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Grupo (opcional)</label>
                    <select name="grupo" onchange="document.getElementById('filtros').submit()"
                            class="w-full text-sm rounded-lg border-gray-300 px-3 py-2 focus:ring-2 focus:ring-[#283342]/20">
                        <option value="">Todos los grupos</option>
                        @foreach($grupos as $gr)
                            <option value="{{ $gr->codigoG }}" {{ $grupoId === $gr->codigoG ? 'selected' : '' }}>
                                Grupo {{ $gr->numero_grupo }}
                            </option>
                        @endforeach
                    </select>
                    @unless($filtraGrupo)
                        <p class="text-xs text-gray-400 mt-1">Este reporte no se filtra por grupo.</p>
                    @endunless
                </div>
            </form>
        </div>

        {{-- ── Resultado del reporte (derecha) ───────────────────────────── --}}
        <div class="lg:col-span-3 space-y-4">

            {{-- Barra de título + exportación --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $reporte['titulo'] }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $gestionModel->nombre }}</p>
                    </div>
                    @php $params = ['gestion' => $gestionModel->idGestion, 'reporte' => $reporteClave, 'grupo' => $grupoId]; @endphp
                    <div class="flex gap-2">
                        <a href="{{ route('admin.reportes.pdf', $params) }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-red-50 text-red-700 hover:bg-red-100 transition border border-red-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Descargar PDF
                        </a>
                        <a href="{{ route('admin.reportes.export', $params) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition border border-emerald-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Descargar CSV
                        </a>
                    </div>
                </div>

                {{-- Resumen --}}
                @if(!empty($reporte['resumen']))
                <div class="flex flex-wrap gap-3 mt-4">
                    @foreach($reporte['resumen'] as $label => $valor)
                    <div class="px-4 py-2 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-lg font-bold text-gray-800">{{ $valor }}</span>
                        <span class="text-xs text-gray-500 ml-1">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Tabla --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                @if(empty($reporte['filas']))
                    <p class="px-6 py-12 text-center text-gray-400 text-sm">Sin datos para los filtros seleccionados.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                                <th class="px-5 py-3 w-10">#</th>
                                @foreach($reporte['columnas'] as $col)
                                    <th class="px-5 py-3">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($reporte['filas'] as $i => $fila)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-2.5 text-gray-400">{{ $i + 1 }}</td>
                                @foreach($fila as $cIdx => $celda)
                                <td class="px-5 py-2.5 text-gray-700">
                                    @if(isset($reporte['estadoCol']) && $reporte['estadoCol'] === $cIdx)
                                        @php
                                            $cls = match ($celda) {
                                                'Aprobado'  => 'bg-emerald-100 text-emerald-700',
                                                'Reprobado' => 'bg-red-100 text-red-700',
                                                default     => 'bg-amber-100 text-amber-700',
                                            };
                                        @endphp
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $cls }}">{{ $celda }}</span>
                                    @else
                                        {{ $celda }}
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    @endif
</div>
@endsection
