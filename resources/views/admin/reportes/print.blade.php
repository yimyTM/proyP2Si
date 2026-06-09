<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte – {{ $gestionModel->nombre }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #1f2937; background: #fff; padding: 30px; }
        h1 { font-size: 18px; color: #283342; border-bottom: 2px solid #283342; padding-bottom: 8px; margin-bottom: 6px; }
        h2 { font-size: 13px; color: #374151; margin: 20px 0 8px; text-transform: uppercase; letter-spacing: 0.05em; }
        .subtitle { font-size: 11px; color: #6b7280; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        th { background: #283342; color: #fff; text-align: left; padding: 6px 10px; font-size: 11px; }
        td { padding: 5px 10px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) td { background: #f9fafb; }
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
        .kpi { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; text-align: center; }
        .kpi .value { font-size: 24px; font-weight: 700; color: #283342; }
        .kpi .label { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 600; }
        .badge-emerald { background: #d1fae5; color: #065f46; }
        .badge-red     { background: #fee2e2; color: #991b1b; }
        .badge-amber   { background: #fef3c7; color: #92400e; }
        .no-print { margin-bottom: 16px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 16px; }
        }
        .bar-container { background: #e5e7eb; border-radius: 4px; height: 8px; width: 100%; }
        .bar-fill { height: 8px; border-radius: 4px; background: #283342; }
    </style>
</head>
<body>

    {{-- Botón imprimir (oculto al imprimir) --}}
    <div class="no-print" style="display:flex;gap:10px;margin-bottom:20px;">
        <button onclick="window.print()"
                style="padding:8px 18px;background:#283342;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;">
            🖨 Imprimir / Guardar PDF
        </button>
        <button onclick="window.close()"
                style="padding:8px 18px;background:#f3f4f6;color:#374151;border:1px solid #d1d5db;border-radius:6px;cursor:pointer;font-size:13px;">
            ✕ Cerrar
        </button>
    </div>

    <h1>Reporte Institucional — {{ $gestionModel->nombre }}</h1>
    <p class="subtitle">
        Período: {{ $gestionModel->fecha_ini->format('d/m/Y') }} – {{ $gestionModel->fecha_fin->format('d/m/Y') }}
        &nbsp;|&nbsp; Estado: <strong>{{ $gestionModel->estado }}</strong>
        &nbsp;|&nbsp; Generado: {{ now()->format('d/m/Y H:i') }}
    </p>

    {{-- KPIs --}}
    <h2>Resumen general</h2>
    <div class="kpi-grid">
        <div class="kpi">
            <div class="value">{{ $totalInscritos }}</div>
            <div class="label">Total inscritos</div>
        </div>
        <div class="kpi" style="border-color:#a7f3d0;">
            <div class="value" style="color:#065f46;">{{ $aprobados }}</div>
            <div class="label">Aprobados</div>
        </div>
        <div class="kpi" style="border-color:#fecaca;">
            <div class="value" style="color:#991b1b;">{{ $reprobados }}</div>
            <div class="label">Reprobados</div>
        </div>
        <div class="kpi" style="border-color:#fde68a;">
            <div class="value" style="color:#92400e;">{{ $enCurso }}</div>
            <div class="label">En curso / Sin resultado</div>
        </div>
    </div>

    {{-- Rendimiento por materia --}}
    @if($promPorMateria->isNotEmpty())
    <h2>Rendimiento promedio por materia</h2>
    <table>
        <thead>
            <tr>
                <th>Materia</th>
                <th>Promedio (%)</th>
                <th>Calificaciones registradas</th>
                <th>Nivel</th>
            </tr>
        </thead>
        <tbody>
            @foreach($promPorMateria as $row)
            @php $pct = round($row->pct_promedio ?? 0, 1); @endphp
            <tr>
                <td>{{ $row->nombMateria }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span>{{ $pct }}%</span>
                        <div class="bar-container" style="width:80px;">
                            <div class="bar-fill" style="width:{{ min($pct, 100) }}%;background:{{ $pct >= 60 ? '#10b981' : '#ef4444' }};"></div>
                        </div>
                    </div>
                </td>
                <td>{{ $row->total_notas }}</td>
                <td>
                    <span class="badge {{ $pct >= 60 ? 'badge-emerald' : 'badge-red' }}">
                        {{ $pct >= 60 ? 'Bueno' : 'Bajo' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Admisión por carrera --}}
    @if($admisionPorCarrera->isNotEmpty())
    <h2>Distribución de admitidos por carrera</h2>
    <table>
        <thead>
            <tr>
                <th>Carrera</th>
                <th>Admitidos (1ª opción)</th>
                <th>Reubicados (2ª opción)</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($admisionPorCarrera as $row)
            <tr>
                <td>{{ $row->nombre }}</td>
                <td>{{ $row->admitidos }}</td>
                <td>{{ $row->reubicados }}</td>
                <td><strong>{{ $row->admitidos + $row->reubicados }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Grupos --}}
    @if($grupos->isNotEmpty())
    <h2>Grupos activos y ocupación</h2>
    <table>
        <thead>
            <tr>
                <th>Grupo</th>
                <th>Turno</th>
                <th>Capacidad</th>
                <th>Inscritos</th>
                <th>Ocupación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grupos as $g)
            <tr>
                <td>Grupo {{ $g->numero_grupo }}</td>
                <td>{{ $g->turno?->nombre ?? '–' }}</td>
                <td>{{ $g->capacidad }}</td>
                <td>{{ $g->totalInscritos }}</td>
                <td>
                    <span class="badge {{ $g->pctOcupacion >= 90 ? 'badge-red' : ($g->pctOcupacion >= 70 ? 'badge-amber' : 'badge-emerald') }}">
                        {{ $g->pctOcupacion }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Asistencia --}}
    @if($asistenciaGrupos->isNotEmpty())
    <h2>Asistencia promedio por grupo</h2>
    <table>
        <thead>
            <tr>
                <th>Grupo</th>
                <th>Total registros</th>
                <th>Presentes</th>
                <th>Ausentes</th>
                <th>% Asistencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($asistenciaGrupos as $row)
            <tr>
                <td>Grupo {{ $row->numero_grupo }}</td>
                <td>{{ $row->total }}</td>
                <td>{{ $row->presentes }}</td>
                <td>{{ $row->ausentes }}</td>
                <td>
                    <span class="badge {{ $row->pct >= 80 ? 'badge-emerald' : ($row->pct >= 60 ? 'badge-amber' : 'badge-red') }}">
                        {{ $row->pct }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

</body>
</html>
