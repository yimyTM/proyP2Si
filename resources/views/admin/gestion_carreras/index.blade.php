@extends('layouts.app')

@section('title', 'Cupos por Carrera')
@section('page-title', 'Cupos por Carrera — Gestión #{{ $gestion->idGestion }}')

@section('content')
<div class="space-y-5 max-w-4xl">

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
        <p class="font-medium mb-1">Periodo: {{ $gestion->fecha_ini->format('d/m/Y') }} — {{ $gestion->fecha_fin->format('d/m/Y') }}</p>
        <p>Indique el <strong>máximo de alumnos por grupo</strong> para cada carrera. Puede variar: una carrera con más demanda puede usar grupos más grandes o más pequeños según su criterio.</p>
        <p class="mt-2 text-blue-700">El algoritmo de apertura usará: <code class="bg-blue-100 px-1 rounded">grupos = ⌈inscritos ÷ cupos⌉</code> por cada carrera configurada.</p>
    </div>

    <form method="POST" action="{{ route('admin.gestiones.carreras.update', $gestion) }}">
        @csrf
        @method('PUT')

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Carreras y cupos por grupo</h3>
                <span class="text-xs text-gray-400">Deje vacío las carreras que no participan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Carrera</th>
                            <th class="px-6 py-3">Modalidad</th>
                            <th class="px-6 py-3 w-40">Cupos máx. / grupo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($carreras as $carrera)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $carrera->nombre }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $carrera->modalidad?->nombModalidad ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <input type="number"
                                       name="cupos[{{ $carrera->codCarrera }}]"
                                       value="{{ old('cupos.'.$carrera->codCarrera, $cuposPorCarrera[$carrera->codCarrera] ?? '') }}"
                                       min="1" max="500" placeholder="—"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit"
                    class="px-6 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
                    style="background-color: #283342;">
                Guardar cupos
            </button>
            <a href="{{ route('admin.gestiones.index') }}"
               class="px-6 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                Volver a gestiones
            </a>
        </div>
    </form>

</div>
@endsection
