<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        .header { border-bottom: 2px solid #283342; padding-bottom: 8px; margin-bottom: 12px; }
        .header h1 { font-size: 16px; color: #283342; }
        .header .meta { font-size: 9px; color: #6b7280; margin-top: 3px; }
        .resumen { margin-bottom: 12px; }
        .chip { display: inline-block; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 8px; margin: 0 4px 4px 0; font-size: 9px; }
        .chip strong { font-size: 12px; color: #283342; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #283342; color: #fff; text-align: left; padding: 5px 7px; font-size: 9px; }
        td { padding: 4px 7px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .num { color: #9ca3af; }
        .badge { padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: bold; }
        .b-aprob { background: #d1fae5; color: #065f46; }
        .b-reprob { background: #fee2e2; color: #991b1b; }
        .b-curso { background: #fef3c7; color: #92400e; }
        .footer { margin-top: 14px; font-size: 8px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $reporte['titulo'] }}</h1>
        <div class="meta">
            Gestión: <strong>{{ $gestionModel->nombre }}</strong> ({{ $gestionModel->estado }})
            &nbsp;|&nbsp; Generado: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    @if(!empty($reporte['resumen']))
    <div class="resumen">
        @foreach($reporte['resumen'] as $label => $valor)
            <span class="chip"><strong>{{ $valor }}</strong> {{ $label }}</span>
        @endforeach
    </div>
    @endif

    @if(empty($reporte['filas']))
        <p style="color:#9ca3af;font-size:10px;">Sin datos para los filtros seleccionados.</p>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:24px;">#</th>
                @foreach($reporte['columnas'] as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($reporte['filas'] as $i => $fila)
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                @foreach($fila as $cIdx => $celda)
                <td>
                    @if(isset($reporte['estadoCol']) && $reporte['estadoCol'] === $cIdx)
                        @php
                            $cls = match ($celda) {
                                'Aprobado'  => 'b-aprob',
                                'Reprobado' => 'b-reprob',
                                default     => 'b-curso',
                            };
                        @endphp
                        <span class="badge {{ $cls }}">{{ $celda }}</span>
                    @else
                        {{ $celda }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Sistema de Admisión FICCT — {{ $reporte['titulo'] }} — Página generada el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
