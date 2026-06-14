@extends('layouts.app')

@section('title', 'Apertura de Grupos')
@section('page-title', 'CU09 – Apertura Automática de Grupos')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Explicación --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 text-sm text-blue-700 space-y-2">
        <p class="font-semibold text-blue-800">¿Cómo funciona el algoritmo?</p>
        <p>Los postulantes <strong>se mezclan entre carreras</strong>: todos toman las mismas materias en el CUP.
           Los grupos solo se separan por <strong>modalidad</strong> (Presencial / Virtual).</p>
        <p class="font-mono bg-blue-100 px-2 py-1 rounded inline-block">
            grupos = ⌈ total_inscritos_validados ÷ 70 ⌉ &nbsp;·&nbsp; capacidad fija: <strong>70 alumnos/grupo</strong>
        </p>
        <p>Ejemplo: <strong>700</strong> inscritos presenciales ÷ <strong>70</strong> = <strong>10 grupos</strong>.</p>
    </div>

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm">
            @foreach($errors->all() as $e)
                <p class="text-red-600">{{ $e }}</p>
            @endforeach
        </div>
    @endif

    {{-- Formulario --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 space-y-5">
        <h3 class="font-semibold text-gray-800">Configurar apertura</h3>

        <form id="form-apertura" method="POST" action="{{ route('admin.grupos.apertura.calcular') }}">
            @csrf

            {{-- Gestión --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Gestión académica</label>
                <select id="sel-gestion" name="idGestion"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                    <option value="">Seleccione la gestión...</option>
                    @foreach($gestiones as $g)
                    @php $sel = old('idGestion', $gestionSeleccionada) == $g->idGestion; @endphp
                    <option value="{{ $g->idGestion }}"
                            data-capacidad="{{ $g->capacidad_maxima }}"
                            {{ $sel ? 'selected' : '' }}>
                        Gestión #{{ $g->idGestion }}
                        @if($g->nombre) — {{ $g->nombre }}@endif
                        ({{ $g->fecha_ini->format('d/m/Y') }} al {{ $g->fecha_fin->format('d/m/Y') }})
                        [{{ $g->estado }}]
                    </option>
                    @endforeach
                </select>
                <p id="info-capacidad" class="text-xs text-gray-400 mt-1"></p>
            </div>

            {{-- Preview en vivo --}}
            <div id="preview-panel" class="hidden mb-5 border border-[#283342]/20 rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-[#283342] text-white text-sm font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Vista previa — capacidad 70 alumnos/grupo
                </div>
                <div id="preview-rows" class="divide-y divide-gray-100 bg-white"></div>
                <div class="px-4 py-2 bg-gray-50 text-xs text-gray-500 border-t">
                    * Basado en inscritos con estado <strong>Validado</strong>.
                    Los grupos se crearán al pulsar el botón.
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-5">
                <p class="text-xs text-amber-800">
                    <strong>Atención:</strong> Esta operación crea grupos en la base de datos.
                    Ejecuta una sola vez por gestión para evitar duplicados.
                </p>
            </div>

            <button type="submit"
                    class="w-full py-3 rounded-lg text-white font-semibold text-sm transition hover:opacity-90"
                    style="background-color: #283342;">
                Crear grupos automáticamente (cap. 70)
            </button>
        </form>
    </div>

</div>

@push('scripts')
<script>
const inscritosPorGestion = @json($inscritosPorGestion);
const CAP = 70;

const selGestion = document.getElementById('sel-gestion');
const preview    = document.getElementById('preview-panel');
const previewRows= document.getElementById('preview-rows');
const infoCap    = document.getElementById('info-capacidad');

function recalcularPreview() {
    const gId    = selGestion.value;
    const opt    = selGestion.selectedOptions[0];
    const capMax = opt ? (opt.dataset.capacidad || '') : '';

    infoCap.textContent = capMax
        ? `Capacidad total de la gestión: ${capMax} admitidos`
        : '';

    if (!gId || !inscritosPorGestion[gId]) {
        preview.classList.add('hidden');
        return;
    }

    const data      = inscritosPorGestion[gId];
    let totalGrupos = 0;
    let html        = '';

    for (const [modalidad, inscritos] of Object.entries(data)) {
        const grupos = Math.ceil(inscritos / CAP);
        totalGrupos += grupos;

        if (inscritos === 0) {
            html += `
            <div class="px-4 py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">${modalidad}</p>
                    <p class="text-xs text-gray-400">Sin inscritos validados</p>
                </div>
                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">0 grupos</span>
            </div>`;
        } else {
            const resto  = inscritos % CAP;
            const detalle = resto > 0
                ? `${grupos - 1} grupo(s) de 70 + 1 grupo de ${resto}`
                : `${grupos} grupo(s) de 70`;
            html += `
            <div class="px-4 py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">${modalidad}</p>
                    <p class="text-xs text-gray-400">
                        ⌈${inscritos} ÷ 70⌉ = <strong>${grupos}</strong> grupo(s)
                        &nbsp;·&nbsp; ${detalle}
                    </p>
                </div>
                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">
                    ${grupos} grupo(s)
                </span>
            </div>`;
        }
    }

    html += `
    <div class="px-4 py-2 bg-[#283342]/5 flex justify-between items-center">
        <span class="text-sm font-semibold text-gray-700">Total grupos a crear</span>
        <span class="text-sm font-bold" style="color:#283342">${totalGrupos}</span>
    </div>`;

    previewRows.innerHTML = html;
    preview.classList.remove('hidden');
}

selGestion.addEventListener('change', recalcularPreview);

if (selGestion.value) recalcularPreview();
</script>
@endpush
@endsection
