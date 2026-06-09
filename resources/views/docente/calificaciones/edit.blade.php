@extends('layouts.app')

@section('title', 'Calificaciones – ' . $materiaModel?->nombMateria)
@section('page-title', 'CU11 – Registro de Calificaciones')

@section('content')
<div class="space-y-5 max-w-full">

    {{-- Alertas --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Encabezado --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold"
                     style="background-color: #283342;">
                    {{ $grupoModel->numero_grupo ?? '#'.$grupoModel->codigoG }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $materiaModel?->nombMateria }}</h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Grupo {{ $grupoModel->numero_grupo }} ·
                        {{ $grupoModel->modalidad?->nombModalidad }} /
                        {{ $grupoModel->turno?->nombTurno }} ·
                        {{ $gestion?->nombre }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Estado del período --}}
                @if($periodoAbierto)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Período abierto
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-xl text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                        Período cerrado
                    </span>
                @endif
                <a href="{{ route('docente.calificaciones.index') }}"
                   class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">
                    ← Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Banner período cerrado --}}
    @if(! $periodoAbierto)
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-red-700 text-sm font-medium">
                El período de evaluación ha concluido. Las calificaciones están bloqueadas y solo pueden consultarse.
            </p>
        </div>
    @endif

    {{-- Sin exámenes configurados --}}
    @if($examMaterias->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
            <p class="text-gray-500 font-medium">No hay exámenes configurados para esta materia.</p>
            <p class="text-xs text-gray-400 mt-1">El administrador debe crear los exámenes en la gestión activa.</p>
        </div>

    {{-- Sin estudiantes --}}
    @elseif($inscripciones->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
            <p class="text-gray-500 font-medium">Este grupo no tiene estudiantes asignados aún.</p>
        </div>

    @else
    {{-- Tabla de calificaciones --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">
                Planilla de notas
                <span class="ml-2 text-xs font-normal text-gray-400">
                    {{ $inscripciones->count() }} estudiante(s)
                </span>
            </h3>
            <div class="text-xs text-gray-400 space-x-3">
                @foreach($examMaterias as $em)
                    <span class="font-medium text-gray-600">
                        {{ $em->examen->descripcion }}
                    </span>
                    <span class="text-gray-300">({{ number_format($em->examen->ponderacion, 0) }}%)</span>
                @endforeach
            </div>
        </div>

        <form id="formCalif"
              method="POST"
              action="{{ route('docente.calificaciones.update', [$grupoModel->codigoG, $materiaModel->idMateria]) }}">
            @csrf

            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="tablaNotas"
                       data-total-pond="{{ $totalPonderacion }}">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-3 w-8">#</th>
                            <th class="px-4 py-3">Estudiante</th>
                            @foreach($examMaterias as $em)
                                <th class="px-4 py-3 text-center min-w-[120px]">
                                    <div class="font-semibold">{{ $em->examen->descripcion }}</div>
                                    <div class="text-gray-400 font-normal normal-case">
                                        pond. {{ number_format($em->examen->ponderacion, 0) }}%
                                    </div>
                                </th>
                            @endforeach
                            <th class="px-4 py-3 text-center min-w-[100px]">Promedio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="cuerpoNotas">
                        @foreach($inscripciones as $i => $insc)
                        @php
                            $nombre = $insc->postulante
                                ? $insc->postulante->nombre . ' ' . $insc->postulante->apellidos
                                : 'Sin nombre';
                        @endphp
                        <tr class="hover:bg-gray-50 transition" data-row="{{ $insc->idInscripcion }}">
                            <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $nombre }}</td>

                            @foreach($examMaterias as $em)
                            @php
                                $nota = $notasMap[$insc->idInscripcion][$em->idEx_materia] ?? null;
                                $val  = $nota ? number_format((float)$nota->calificacion, 2, '.', '') : '';
                            @endphp
                            <td class="px-4 py-3">
                                <input
                                    type="number"
                                    name="notas[{{ $insc->idInscripcion }}][{{ $em->idEx_materia }}]"
                                    value="{{ $val }}"
                                    min="0" max="100" step="0.01"
                                    data-original="{{ $val }}"
                                    data-pond="{{ $em->examen->ponderacion }}"
                                    @if(! $periodoAbierto) readonly @endif
                                    class="nota-input w-full px-2.5 py-1.5 border rounded-lg text-center text-sm outline-none transition
                                           @if(! $periodoAbierto)
                                               bg-gray-50 text-gray-500 cursor-not-allowed border-gray-200
                                           @else
                                               border-gray-300 focus:ring-2 focus:ring-[#283342]/30 focus:border-[#283342]
                                           @endif"
                                    placeholder="0–100">
                                <p class="error-msg text-red-500 text-xs mt-0.5 hidden text-center"></p>
                            </td>
                            @endforeach

                            {{-- Columna promedio (calculada por JS) --}}
                            <td class="px-4 py-3 text-center">
                                @php
                                    $sumW = 0; $sumV = 0;
                                    foreach($examMaterias as $em) {
                                        $n = $notasMap[$insc->idInscripcion][$em->idEx_materia] ?? null;
                                        if ($n !== null) {
                                            $sumW += (float)$em->examen->ponderacion;
                                            $sumV += (float)$n->calificacion * (float)$em->examen->ponderacion;
                                        }
                                    }
                                    $prom = $sumW > 0 ? number_format($sumV / $sumW, 2) : null;
                                @endphp
                                <span class="promedio-cell font-semibold
                                    @if($prom !== null)
                                        @if((float)$prom >= 60) text-emerald-600
                                        @else text-red-600 @endif
                                    @else text-gray-300 @endif">
                                    {{ $prom ?? '—' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer del formulario --}}
            @if($periodoAbierto)
            <div class="px-6 py-5 border-t bg-gray-50 space-y-4">

                {{-- Errores de validación --}}
                @if($errors->any())
                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl">
                        @foreach($errors->all() as $e)
                            <p class="text-red-600 text-xs">• {{ $e }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Campo motivo --}}
                <div id="motivoWrapper" class="{{ $errors->has('motivo') ? '' : 'hidden' }}">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Motivo de modificación
                        <span class="text-red-500">*</span>
                        <span class="font-normal text-gray-400">(requerido al editar notas existentes)</span>
                    </label>
                    <textarea name="motivo" id="motivo" rows="2"
                              placeholder="Explique el motivo del cambio (ej: corrección de error de digitación)..."
                              class="w-full px-3 py-2 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                                     {{ $errors->has('motivo') ? 'border-red-400' : 'border-gray-300' }}"
                              >{{ old('motivo') }}</textarea>
                </div>

                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-400" id="estadoMsg">
                        Ingrese notas en el rango 0 – 100.
                    </p>
                    <button type="submit" id="btnGuardar"
                            class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90 disabled:opacity-40 disabled:cursor-not-allowed"
                            style="background-color: #283342;">
                        Guardar calificaciones
                    </button>
                </div>
            </div>
            @endif
        </form>
    </div>

    {{-- Leyenda --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-xs text-blue-700 space-y-1">
        <p class="font-semibold text-blue-800">Información sobre el promedio ponderado:</p>
        <p>
            Promedio = (Σ calificación × ponderación) ÷ (Σ ponderaciones).
            Solo se muestra cuando al menos una nota está registrada.
            Aprobado ≥ 51 puntos.
        </p>
    </div>
    @endif

</div>

{{-- JavaScript: validación en tiempo real + recálculo de promedio --}}
<script>
(function () {
    const tabla        = document.getElementById('tablaNotas');
    const motivoWrap   = document.getElementById('motivoWrapper');
    const btnGuardar   = document.getElementById('btnGuardar');
    const estadoMsg    = document.getElementById('estadoMsg');
    const motivoField  = document.getElementById('motivo');
    const totalPond    = tabla ? parseFloat(tabla.dataset.totalPond) : 0;

    if (!tabla) return;

    function calcPromedioRow(row) {
        const inputs = row.querySelectorAll('.nota-input');
        let sumW = 0, sumV = 0;
        inputs.forEach(inp => {
            const v = parseFloat(inp.value);
            const p = parseFloat(inp.dataset.pond);
            if (!isNaN(v) && inp.value.trim() !== '') {
                sumW += p;
                sumV += v * p;
            }
        });
        return sumW > 0 ? (sumV / sumW).toFixed(2) : null;
    }

    function updatePromedioRow(row) {
        const cell = row.querySelector('.promedio-cell');
        if (!cell) return;
        const prom = calcPromedioRow(row);
        if (prom !== null) {
            const val = parseFloat(prom);
            cell.textContent = prom;
            cell.className = 'promedio-cell font-semibold ' +
                (val >= 60 ? 'text-emerald-600' : 'text-red-600');
        } else {
            cell.textContent = '—';
            cell.className = 'promedio-cell font-semibold text-gray-300';
        }
    }

    function checkModificaciones() {
        let hayMod = false;
        tabla.querySelectorAll('.nota-input').forEach(inp => {
            const original = inp.dataset.original;
            const current  = inp.value.trim();
            if (original !== '' && current !== '' && current !== original) {
                hayMod = true;
            }
        });
        return hayMod;
    }

    function checkErrores() {
        let hayError = false;
        tabla.querySelectorAll('.nota-input').forEach(inp => {
            const v = parseFloat(inp.value);
            const err = inp.nextElementSibling;
            if (inp.value.trim() !== '' && (isNaN(v) || v < 0 || v > 100)) {
                inp.classList.add('border-red-400');
                if (err) { err.textContent = 'Valor fuera de rango (0–100).'; err.classList.remove('hidden'); }
                hayError = true;
            } else {
                inp.classList.remove('border-red-400');
                if (err) { err.classList.add('hidden'); }
            }
        });
        return hayError;
    }

    function actualizarUI() {
        const hayError = checkErrores();
        const hayMod   = checkModificaciones();

        // Mostrar/ocultar motivo
        if (motivoWrap) {
            if (hayMod) {
                motivoWrap.classList.remove('hidden');
            } else {
                // Ocultar solo si no tiene error de validación ya visible
                if (!motivoWrap.querySelector('.text-red-600')) {
                    motivoWrap.classList.add('hidden');
                }
            }
        }

        // Estado del botón
        if (btnGuardar) {
            btnGuardar.disabled = hayError;
        }

        if (estadoMsg) {
            if (hayError) {
                estadoMsg.textContent = 'Corrija los valores fuera de rango antes de guardar.';
                estadoMsg.className = 'text-xs text-red-500';
            } else if (hayMod) {
                estadoMsg.textContent = 'Hay modificaciones pendientes. Complete el motivo para guardar.';
                estadoMsg.className = 'text-xs text-amber-600';
            } else {
                estadoMsg.textContent = 'Ingrese notas en el rango 0 – 100.';
                estadoMsg.className = 'text-xs text-gray-400';
            }
        }
    }

    // Validar motivo obligatorio al enviar
    const form = document.getElementById('formCalif');
    if (form) {
        form.addEventListener('submit', function (e) {
            const hayMod   = checkModificaciones();
            const hayError = checkErrores();

            if (hayError) {
                e.preventDefault();
                return;
            }

            if (hayMod && motivoField && motivoField.value.trim() === '') {
                e.preventDefault();
                motivoWrap.classList.remove('hidden');
                motivoField.classList.add('border-red-400');
                motivoField.focus();
                if (estadoMsg) {
                    estadoMsg.textContent = 'El motivo es obligatorio al modificar notas existentes.';
                    estadoMsg.className = 'text-xs text-red-500';
                }
            }
        });
    }

    // Escuchar cambios en todos los inputs de notas
    tabla.querySelectorAll('.nota-input').forEach(inp => {
        inp.addEventListener('input', function () {
            const row = this.closest('tr');
            updatePromedioRow(row);
            actualizarUI();
        });
    });

    // Inicializar estado
    actualizarUI();
})();
</script>
@endsection
