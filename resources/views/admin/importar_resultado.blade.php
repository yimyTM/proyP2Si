@extends('layouts.app')

@section('title', 'Resultado de Importación')
@section('page-title', 'CU04 – Resultado de Importación Masiva')

@section('content')
<div class="space-y-6">

    {{-- Resumen KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-green-700">{{ count($exitosos) }}</p>
            <p class="text-sm text-green-600 mt-1">Usuarios creados</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-red-700">{{ count($errores) }}</p>
            <p class="text-sm text-red-600 mt-1">Filas con errores</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center col-span-2 sm:col-span-1">
            <p class="text-3xl font-bold text-blue-700">{{ count($exitosos) + count($errores) }}</p>
            <p class="text-sm text-blue-600 mt-1">Total procesados</p>
        </div>
    </div>

    {{-- Cuentas generadas --}}
    @if(count($exitosos) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" id="tabla-exitosos">
        <div class="px-6 py-4 border-b flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-semibold text-gray-800">Cuentas generadas</h3>
                <p class="text-xs text-amber-600 mt-0.5">⚠ Guarde estas contraseñas — no se volverán a mostrar</p>
            </div>
            <button onclick="descargarReporte()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded-lg text-white transition hover:opacity-90"
                    style="background-color: #283342;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Descargar reporte
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="tbl-resultado">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">CI</th>
                        <th class="px-6 py-3">Correo</th>
                        <th class="px-6 py-3">Rol</th>
                        <th class="px-6 py-3">Contraseña provisional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($exitosos as $d)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $d['nombre'] }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $d['ci'] }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $d['correo'] }}</td>
                        <td class="px-6 py-3">
                            @php
                                $colores = ['Docente' => 'blue', 'Coordinador' => 'purple', 'Autoridades' => 'amber'];
                                $c = $colores[$d['rol']] ?? 'gray';
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $c }}-50 text-{{ $c }}-700 border border-{{ $c }}-200">
                                {{ $d['rol'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <code class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-gray-800">
                                {{ $d['password'] }}
                            </code>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Errores --}}
    @if(count($errores) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-3">Filas no procesadas</h3>
        <ul class="space-y-1">
            @foreach($errores as $err)
                <li class="flex items-start gap-2 text-sm text-red-600">
                    <span class="mt-0.5 shrink-0">✗</span> {{ $err }}
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    <a href="{{ route('admin.importar-personal') }}"
       class="inline-flex items-center gap-2 px-4 py-2 text-sm rounded-lg text-white transition hover:opacity-90"
       style="background-color: #283342;">
        ← Importar otro archivo
    </a>
</div>

@push('scripts')
<script>
function descargarReporte() {
    const filas = [['Nombre', 'CI', 'Correo', 'Rol', 'Contraseña provisional']];

    document.querySelectorAll('#tbl-resultado tbody tr').forEach(tr => {
        const celdas = tr.querySelectorAll('td');
        filas.push([
            celdas[0]?.innerText.trim() ?? '',
            celdas[1]?.innerText.trim() ?? '',
            celdas[2]?.innerText.trim() ?? '',
            celdas[3]?.innerText.trim() ?? '',
            celdas[4]?.innerText.trim() ?? '',
        ]);
    });

    const csv     = filas.map(r => r.map(c => `"${c.replace(/"/g, '""')}"`).join(',')).join('\n');
    const blob    = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const url     = URL.createObjectURL(blob);
    const link    = document.createElement('a');
    link.href     = url;
    link.download = 'reporte_importacion_' + new Date().toISOString().slice(0,10) + '.csv';
    link.click();
    URL.revokeObjectURL(url);
}
</script>
@endpush
@endsection
