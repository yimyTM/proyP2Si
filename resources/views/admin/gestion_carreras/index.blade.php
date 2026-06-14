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

    {{-- Panel: Crear grupos automáticamente ─────────────────────────────────── --}}
    @if(session('ofrecer_apertura') && $gestion->estaAbierta())
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
        <div class="flex items-start gap-3 mb-4">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-semibold text-emerald-800">Cupos guardados — ¿Crear grupos ahora?</p>
                <p class="text-sm text-emerald-700 mt-0.5">
                    Los postulantes se mezclan entre carreras. El algoritmo creará grupos por modalidad
                    usando la capacidad máxima de la gestión
                    (<code class="bg-emerald-100 px-1 rounded">grupos = ⌈total_inscritos ÷ capacidad_máxima⌉</code>).
                    Selecciona el turno y ejecuta.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.grupos.apertura.calcular') }}" class="flex flex-wrap items-end gap-3">
            @csrf
            <input type="hidden" name="idGestion" value="{{ $gestion->idGestion }}">

            <div>
                <label class="block text-xs font-medium text-emerald-800 mb-1">Cap. máx. por grupo</label>
                <input type="number" name="capacidad_por_grupo" value="70" min="1" max="500"
                       class="w-28 px-3 py-2 border border-emerald-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
            </div>

            <div>
                <label class="block text-xs font-medium text-emerald-800 mb-1">Turno</label>
                <select name="idTurno"
                        class="px-3 py-2 border border-emerald-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                    <option value="">Seleccione...</option>
                    @foreach(\App\Models\Turno::all() as $t)
                        <option value="{{ $t->idTurno }}">{{ $t->nombTurno }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">
                Crear grupos
            </button>

            <a href="{{ route('admin.grupos.apertura', ['gestion' => $gestion->idGestion]) }}"
               class="px-5 py-2 rounded-lg bg-white border border-emerald-300 hover:bg-emerald-50 text-emerald-700 text-sm font-medium transition">
                Ver preview completo
            </a>
        </form>
    </div>
    @endif

</div>
@endsection
