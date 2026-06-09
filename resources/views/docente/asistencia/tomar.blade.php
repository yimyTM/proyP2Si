@extends('layouts.app')

@section('title', "Asistencia – Grupo {{ $grupo->numero_grupo ?? '#'.$grupo->codigoG }}")
@section('page-title', "CU10 – Asistencia · Grupo {{ $grupo->numero_grupo ?? '#'.$grupo->codigoG }}")

@section('content')
@php
    $yaRegistrada   = session('ya_registrada') || ($sesionHoy && ! request('modificar'));
    $modoModificar  = (bool) $sesionHoy;
    $estadosHoy     = $sesionHoy ? $sesionHoy->detalles->keyBy('idPost') : collect();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── Columna principal ─────────────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Alertas --}}
        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- CU10-EX: Gestión cerrada --}}
        @if($gestionCerrada)
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>El período académico ha concluido. No se pueden registrar nuevas asistencias.</span>
            </div>
        @endif

        {{-- CU10-EX: Ya registrada hoy --}}
        @if(! $gestionCerrada && $sesionHoy)
            <div class="flex items-center justify-between gap-3 px-5 py-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Ya registró asistencia para este grupo hoy ({{ $sesionHoy->fecha->format('d/m/Y') }}, {{ $sesionHoy->detalles->count() }} alumno(s)). ¿Desea modificarla?</span>
                </div>
            </div>
        @endif

        {{-- Info del grupo --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold shrink-0"
                     style="background-color: #283342;">
                    {{ $grupo->numero_grupo ?? '#'.$grupo->codigoG }}
                </div>
                <div class="flex-1 text-sm space-y-2">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div>
                            <p class="text-xs text-gray-400">Modalidad</p>
                            <p class="font-medium text-gray-800">{{ $grupo->modalidad?->nombModalidad ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Turno</p>
                            <p class="font-medium text-gray-800">{{ $grupo->turno?->nombTurno ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Capacidad</p>
                            <p class="font-medium text-gray-800">{{ $grupo->capacidad }} cupos</p>
                        </div>
                    </div>
                    @if($grupo->materiGrupos->isNotEmpty())
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach($grupo->materiGrupos as $mg)
                            <span class="inline-flex items-center gap-1.5 text-xs bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full border border-blue-100">
                                <span class="font-medium">{{ $mg->materia?->nombMateria ?? '—' }}</span>
                                @if($mg->horario)
                                    <span class="text-blue-300">·</span>
                                    <span>{{ $mg->horario->dia }} {{ $mg->horario->hora_ini->format('H:i') }}–{{ $mg->horario->hora_fin->format('H:i') }}</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Formulario --}}
        @if(! $gestionCerrada)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <form method="POST" action="{{ route('docente.asistencia.store') }}" id="formAsistencia">
                @csrf
                <input type="hidden" name="codigoG" value="{{ $grupo->codigoG }}">
                @if($modoModificar)
                    <input type="hidden" name="_modificar" value="1">
                @endif

                {{-- Fecha y observación --}}
                <div class="px-6 py-5 border-b grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Fecha de la sesión <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="fecha"
                               value="{{ old('fecha', now()->toDateString()) }}"
                               max="{{ now()->toDateString() }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Observación (opcional)</label>
                        <input type="text" name="observacion"
                               value="{{ old('observacion', $sesionHoy?->observacion) }}"
                               placeholder="Ej: Clase recuperatoria, examen parcial…"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                    </div>
                </div>

                {{-- Controles globales --}}
                <div class="px-6 py-3 bg-gray-50 border-b flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-700">
                        {{ $postulantes->count() }} estudiante(s)
                        <span id="contadorPresentes" class="ml-2 text-xs text-green-600 font-semibold"></span>
                    </p>
                    <div class="flex gap-2">
                        <button type="button" onclick="marcarTodos('presente')"
                                class="text-xs px-3 py-1.5 rounded-lg bg-green-100 hover:bg-green-200 text-green-700 transition font-medium">
                            ✓ Todos presentes
                        </button>
                        <button type="button" onclick="marcarTodos('ausente')"
                                class="text-xs px-3 py-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition font-medium">
                            ✗ Todos ausentes
                        </button>
                    </div>
                </div>

                {{-- Lista de estudiantes --}}
                <div class="divide-y divide-gray-100">
                    @forelse($postulantes as $i => $postulante)
                    @php
                        $estadoPrevio = $estadosHoy->get($postulante->idPost)?->estado ?? null;
                        $pct          = $porcentajes[$postulante->idPost] ?? null;
                    @endphp
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0"
                                 style="background-color: hsl({{ ($i * 47) % 360 }}, 60%, 50%);">
                                {{ strtoupper(substr($postulante->nombre, 0, 1)) }}{{ strtoupper(substr($postulante->apellidos, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ $postulante->apellidos }}, {{ $postulante->nombre }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    CI: {{ $postulante->ci }}
                                    @if($pct !== null)
                                        <span class="ml-2 {{ $pct >= 70 ? 'text-green-600' : 'text-amber-600' }} font-medium">
                                            {{ $pct }}% asist.
                                        </span>
                                    @elseif($totalSesiones === 0)
                                        <span class="ml-2 text-gray-300">sin sesiones aún</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2" role="group">
                            @foreach(['presente' => ['bg-green-500','border-green-600','P'], 'tardanza' => ['bg-amber-500','border-amber-600','T'], 'ausente' => ['bg-red-500','border-red-600','A']] as $estado => [$bg, $border, $lbl])
                            <label class="cursor-pointer">
                                <input type="radio"
                                       name="asistencia[{{ $postulante->idPost }}]"
                                       value="{{ $estado }}"
                                       class="hidden peer"
                                       {{ $estadoPrevio === $estado ? 'checked' : '' }}
                                       onchange="actualizarContador()">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold border-2 transition-all
                                             text-gray-300 border-gray-200
                                             peer-checked:text-white peer-checked:{{ $bg }} peer-checked:{{ $border }}"
                                      title="{{ ucfirst($estado) }}">
                                    {{ $lbl }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-10 text-center text-gray-400 text-sm">
                        <svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Este grupo no tiene estudiantes asignados aún.
                    </div>
                    @endforelse
                </div>

                @if($postulantes->isNotEmpty())
                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-between">
                    <a href="{{ route('docente.asistencia.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-800 transition">← Volver a mis grupos</a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
                            style="background-color: #283342;">
                        {{ $modoModificar ? 'Modificar asistencia' : 'Guardar asistencia' }}
                    </button>
                </div>
                @else
                <div class="px-6 py-4 border-t">
                    <a href="{{ route('docente.asistencia.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-800 transition">← Volver a mis grupos</a>
                </div>
                @endif
            </form>
        </div>
        @else
        {{-- Gestión cerrada: solo lectura --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-center text-gray-400 text-sm">
            <a href="{{ route('docente.asistencia.index') }}"
               class="text-sm text-gray-500 hover:text-gray-800 transition">← Volver a mis grupos</a>
        </div>
        @endif
    </div>

    {{-- ── Sidebar: historial ─────────────────────────────────────────────────── --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b">
                <h3 class="font-semibold text-gray-800 text-sm">Sesiones anteriores</h3>
                <p class="text-xs text-gray-400 mt-0.5">Últimas 10 clases registradas</p>
            </div>

            @forelse($sesionesAnteriores as $sesion)
            <div class="px-5 py-3 border-b last:border-0">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            {{ $sesion->fecha->isoFormat('ddd D MMM') }}
                        </p>
                        @if($sesion->observacion)
                            <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[150px]">{{ $sesion->observacion }}</p>
                        @endif
                    </div>
                    <div class="text-right text-xs">
                        <span class="text-green-600 font-medium">{{ $sesion->totalPresentes() }} P</span>
                        <span class="text-gray-300 mx-1">/</span>
                        <span class="text-red-500 font-medium">{{ $sesion->totalAusentes() }} A</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-xs">
                Sin sesiones registradas aún.
            </div>
            @endforelse
        </div>

        {{-- Leyenda --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-600 mb-3">Leyenda de estados</p>
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-green-500 text-white text-xs font-bold flex items-center justify-center">P</span>
                    <span class="text-sm text-gray-700">Presente</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-500 text-white text-xs font-bold flex items-center justify-center">T</span>
                    <span class="text-sm text-gray-700">Tardanza</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-red-500 text-white text-xs font-bold flex items-center justify-center">A</span>
                    <span class="text-sm text-gray-700">Ausente</span>
                </div>
            </div>
            @if($totalSesiones > 0)
            <div class="mt-4 pt-4 border-t">
                <p class="text-xs text-gray-400">% ≥ 70 = habilitado para examen</p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function marcarTodos(estado) {
    document.querySelectorAll(`input[type="radio"][value="${estado}"]`).forEach(r => {
        r.checked = true;
    });
    actualizarContador();
}

function actualizarContador() {
    const presentes = document.querySelectorAll('input[type="radio"][value="presente"]:checked').length;
    const tardanzas = document.querySelectorAll('input[type="radio"][value="tardanza"]:checked').length;
    const total     = presentes + tardanzas;
    const span      = document.getElementById('contadorPresentes');
    if (span) {
        span.textContent = total > 0 ? `— ${total} presente(s)/tardanza(s)` : '';
    }
}
</script>
@endpush
