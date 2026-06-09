@extends('layouts.app')

@section('title', 'Reportes y Analíticas')
@section('page-title', 'CU14 – Reportes y Analíticas')

@section('content')
<div class="space-y-6">

    {{-- Encabezado --}}
    <div class="rounded-2xl p-6 text-white"
         style="background: linear-gradient(135deg, #283342 0%, #3d5068 100%);">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold">Reportes y Analíticas</h2>
                <p class="text-white/60 text-sm mt-1">
                    Indicadores institucionales y tablero de control por gestión académica.
                </p>
            </div>
            {{-- Selector de gestión --}}
            @if(isset($gestiones) && $gestiones->count() > 1)
            <form method="GET" action="{{ route('admin.reportes.index') }}" class="flex items-center gap-2">
                <select name="gestion" onchange="this.form.submit()"
                        class="text-gray-800 bg-white text-sm rounded-lg px-3 py-2 border-0 focus:ring-2 focus:ring-white/50">
                    @foreach($gestiones as $g)
                        <option value="{{ $g->idGestion }}"
                            {{ (isset($gestionModel) && $gestionModel->idGestion === $g->idGestion) ? 'selected' : '' }}>
                            {{ $g->nombre }} ({{ $g->estado }})
                        </option>
                    @endforeach
                </select>
            </form>
            @endif
        </div>
    </div>

    {{-- Sin gestiones --}}
    @if(!isset($gestionModel) || !$gestionModel)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <p class="text-gray-400 text-sm">No hay datos suficientes para generar los indicadores de la gestión actual.</p>
    </div>
    @else

    {{-- Barra de acciones --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span class="font-semibold text-gray-800">{{ $gestionModel->nombre }}</span>
            @if($gestionModel->estado === 'Abierta')
                <span class="inline-flex items-center gap-1 text-xs font-medium bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Abierta
                </span>
            @else
                <span class="text-xs font-medium bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                    {{ $gestionModel->estado }}
                </span>
            @endif
            <span>{{ $gestionModel->fecha_ini->format('d/m/Y') }} – {{ $gestionModel->fecha_fin->format('d/m/Y') }}</span>
        </div>
        <div class="flex gap-2 no-print">
            <a href="{{ route('admin.reportes.csv', $gestionModel->idGestion) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition border border-emerald-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar Excel / CSV
            </a>
            <a href="{{ route('admin.reportes.imprimir', $gestionModel->idGestion) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition border border-blue-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir / PDF
            </a>
        </div>
    </div>

    {{-- ── KPI Cards ───────────────────────────────────────────────────── --}}
    @php
        $tasaAprobacion = $totalInscritos > 0 ? round($aprobados / $totalInscritos * 100) : 0;
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total inscritos --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total inscritos</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalInscritos }}</p>
            <p class="text-xs text-gray-400 mt-1">en esta gestión</p>
        </div>
        {{-- Aprobados --}}
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-5">
            <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider">Aprobados</p>
            <p class="text-3xl font-bold text-emerald-700 mt-1">{{ $aprobados }}</p>
            <p class="text-xs text-emerald-500 mt-1">{{ $tasaAprobacion }}% de tasa de aprobación</p>
        </div>
        {{-- Reprobados --}}
        <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-5">
            <p class="text-xs font-medium text-red-500 uppercase tracking-wider">Reprobados</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $reprobados }}</p>
            @php $tasaReprobacion = $totalInscritos > 0 ? round($reprobados / $totalInscritos * 100) : 0; @endphp
            <p class="text-xs text-red-400 mt-1">{{ $tasaReprobacion }}% tasa de reprobación</p>
        </div>
        {{-- En curso --}}
        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-5">
            <p class="text-xs font-medium text-amber-600 uppercase tracking-wider">En curso</p>
            <p class="text-3xl font-bold text-amber-700 mt-1">{{ $enCurso }}</p>
            <p class="text-xs text-amber-500 mt-1">sin resultado definitivo</p>
        </div>
    </div>

    {{-- ── Gráficos principales ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Doughnut: Distribución por resultado --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribución por resultado académico</h3>
            @if($totalInscritos > 0)
            <div class="flex items-center justify-center" style="height:220px;">
                <canvas id="chartResultados"></canvas>
            </div>
            <div class="flex justify-center gap-4 mt-3 flex-wrap">
                <span class="flex items-center gap-1.5 text-xs text-gray-600">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>Aprobados ({{ $aprobados }})
                </span>
                <span class="flex items-center gap-1.5 text-xs text-gray-600">
                    <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>Reprobados ({{ $reprobados }})
                </span>
                <span class="flex items-center gap-1.5 text-xs text-gray-600">
                    <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>En curso ({{ $enCurso }})
                </span>
            </div>
            @else
            <div class="flex items-center justify-center h-48 text-gray-400 text-sm">Sin datos registrados</div>
            @endif
        </div>

        {{-- Bar: Promedio por materia --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Rendimiento promedio por materia (%)</h3>
            @if($promPorMateria->isNotEmpty())
            <div style="height:220px;">
                <canvas id="chartMaterias"></canvas>
            </div>
            @else
            <div class="flex items-center justify-center h-48 text-gray-400 text-sm">Sin calificaciones registradas</div>
            @endif
        </div>
    </div>

    {{-- ── Admisión por carrera ────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribución de admitidos por carrera</h3>
        @if($admisionPorCarrera->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
            <div style="height:240px;">
                <canvas id="chartAdmision"></canvas>
            </div>
            <div class="space-y-3">
                @foreach($admisionPorCarrera as $row)
                @php $total = $row->admitidos + $row->reubicados; @endphp
                <div>
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span class="font-medium truncate max-w-[200px]" title="{{ $row->nombre }}">{{ $row->nombre }}</span>
                        <span>{{ $total }} admitidos</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 flex overflow-hidden">
                        @if($row->admitidos > 0)
                        <div class="h-2.5 bg-blue-500" style="width:{{ $total > 0 ? round($row->admitidos/$total*100) : 0 }}%"></div>
                        @endif
                        @if($row->reubicados > 0)
                        <div class="h-2.5 bg-indigo-400" style="width:{{ $total > 0 ? round($row->reubicados/$total*100) : 0 }}%"></div>
                        @endif
                    </div>
                    <div class="flex gap-3 mt-1">
                        <span class="text-xs text-blue-600">Admitidos: {{ $row->admitidos }}</span>
                        <span class="text-xs text-indigo-500">Reubicados: {{ $row->reubicados }}</span>
                    </div>
                </div>
                @endforeach
                <div class="flex gap-4 pt-2 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-500 inline-block"></span>Admitido (1ª opción)</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-indigo-400 inline-block"></span>Reubicado (2ª opción)</span>
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center justify-center h-24 text-gray-400 text-sm">
            La admisión por carrera no ha sido procesada para esta gestión. Ejecute primero CU13.
        </div>
        @endif
    </div>

    {{-- ── Grupos: ocupación y asistencia ─────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Ocupación por grupo --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Grupos activos – Ocupación</h3>
            @if($grupos->isNotEmpty())
            <div class="space-y-4">
                @foreach($grupos as $g)
                @php
                    $color = $g->pctOcupacion >= 90 ? 'bg-red-500' : ($g->pctOcupacion >= 70 ? 'bg-amber-400' : 'bg-blue-500');
                @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">Grupo {{ $g->numero_grupo }}
                            @if($g->turno)
                            <span class="text-xs text-gray-400 font-normal">· {{ $g->turno->nombre }}</span>
                            @endif
                        </span>
                        <span class="text-gray-500 text-xs">{{ $g->totalInscritos }}/{{ $g->capacidad }} — {{ $g->pctOcupacion }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all {{ $color }}"
                             style="width: {{ min($g->pctOcupacion, 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex items-center justify-center h-24 text-gray-400 text-sm">Sin grupos registrados para esta gestión.</div>
            @endif
        </div>

        {{-- Asistencia por grupo --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Asistencia promedio por grupo (%)</h3>
            @if($asistenciaGrupos->isNotEmpty())
            <div style="height:200px;">
                <canvas id="chartAsistencia"></canvas>
            </div>
            <div class="mt-3 space-y-1">
                @foreach($asistenciaGrupos as $row)
                <div class="flex justify-between text-xs text-gray-500">
                    <span>Grupo {{ $row->numero_grupo }}</span>
                    <span>{{ $row->presentes }}/{{ $row->total }} presencias — <strong>{{ $row->pct }}%</strong></span>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex items-center justify-center h-24 text-gray-400 text-sm">Sin registros de asistencia para esta gestión.</div>
            @endif
        </div>
    </div>

    {{-- ── Comparativo histórico ───────────────────────────────────────── --}}
    @if(isset($historico) && $historico->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Análisis comparativo histórico (gestiones cerradas)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-2 pr-4 text-xs font-semibold text-gray-500 uppercase">Gestión</th>
                        <th class="text-center py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Inscritos</th>
                        <th class="text-center py-2 px-3 text-xs font-semibold text-emerald-600 uppercase">Aprobados</th>
                        <th class="text-center py-2 px-3 text-xs font-semibold text-red-500 uppercase">Reprobados</th>
                        <th class="text-center py-2 px-3 text-xs font-semibold text-blue-600 uppercase">Admitidos</th>
                        <th class="text-center py-2 px-3 text-xs font-semibold text-gray-500 uppercase">Tasa aprob.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historico as $h)
                    @php $tasa = $h->inscritos > 0 ? round($h->aprobados/$h->inscritos*100) : 0; @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                        <td class="py-3 pr-4 font-medium text-gray-700">{{ $h->gestion->nombre }}</td>
                        <td class="py-3 px-3 text-center text-gray-600">{{ $h->inscritos }}</td>
                        <td class="py-3 px-3 text-center text-emerald-700 font-semibold">{{ $h->aprobados }}</td>
                        <td class="py-3 px-3 text-center text-red-600">{{ $h->reprobados }}</td>
                        <td class="py-3 px-3 text-center text-blue-700">{{ $h->admitidos }}</td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $tasa >= 60 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $tasa }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif(isset($historico))
    <div class="bg-gray-50 rounded-2xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-400">
        No hay gestiones anteriores disponibles para el análisis comparativo.
    </div>
    @endif

    @endif {{-- end if gestionModel --}}

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = 'system-ui, sans-serif';
    Chart.defaults.font.size   = 11;

    @if(isset($gestionModel) && $gestionModel && isset($totalInscritos) && $totalInscritos > 0)
    // Doughnut – Resultado académico
    new Chart(document.getElementById('chartResultados'), {
        type: 'doughnut',
        data: {
            labels: ['Aprobados', 'Reprobados', 'En curso'],
            datasets: [{
                data: [{{ $aprobados }}, {{ $reprobados }}, {{ $enCurso }}],
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                borderWidth: 2,
                borderColor: '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });
    @endif

    @if(isset($promPorMateria) && $promPorMateria->isNotEmpty())
    // Bar – Promedio por materia
    new Chart(document.getElementById('chartMaterias'), {
        type: 'bar',
        data: {
            labels: @json($promPorMateria->pluck('nombMateria')),
            datasets: [{
                label: 'Promedio (%)',
                data: @json($promPorMateria->map(fn($r) => round($r->pct_promedio ?? 0, 1))),
                backgroundColor: '#3b82f6',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { callback: v => v + '%' },
                    grid: { color: '#f3f4f6' }
                },
                x: { grid: { display: false } }
            }
        }
    });
    @endif

    @if(isset($admisionPorCarrera) && $admisionPorCarrera->isNotEmpty())
    // Bar apilado – Admisión por carrera
    new Chart(document.getElementById('chartAdmision'), {
        type: 'bar',
        data: {
            labels: @json($admisionPorCarrera->pluck('nombre')),
            datasets: [
                {
                    label: 'Admitidos',
                    data: @json($admisionPorCarrera->pluck('admitidos')),
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                },
                {
                    label: 'Reubicados',
                    data: @json($admisionPorCarrera->pluck('reubicados')),
                    backgroundColor: '#818cf8',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } },
            scales: {
                x: { stacked: true, grid: { display: false } },
                y: { stacked: true, beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { precision: 0 } }
            }
        }
    });
    @endif

    @if(isset($asistenciaGrupos) && $asistenciaGrupos->isNotEmpty())
    // Bar – Asistencia por grupo
    new Chart(document.getElementById('chartAsistencia'), {
        type: 'bar',
        data: {
            labels: @json($asistenciaGrupos->map(fn($r) => 'Grupo ' . $r->numero_grupo)),
            datasets: [{
                label: '% Asistencia',
                data: @json($asistenciaGrupos->pluck('pct')),
                backgroundColor: @json($asistenciaGrupos->map(fn($r) => $r->pct >= 80 ? '#10b981' : ($r->pct >= 60 ? '#f59e0b' : '#ef4444'))),
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { callback: v => v + '%' },
                    grid: { color: '#f3f4f6' }
                },
                x: { grid: { display: false } }
            }
        }
    });
    @endif
</script>
@endpush
