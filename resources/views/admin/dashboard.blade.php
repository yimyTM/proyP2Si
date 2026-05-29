@extends('layouts.app')

@section('title', 'Dashboard – Administrador')
@section('page-title', 'Panel de Administración')

@push('styles')
<style>
    .kpi-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
</style>
@endpush

@section('content')

{{-- ── Bienvenida ─────────────────────────────────────────────────────────── --}}
<div class="rounded-2xl p-6 mb-6 flex items-center justify-between"
     style="background: linear-gradient(135deg, #283342 0%, #3d5068 100%);">
    <div>
        <h2 class="text-xl font-bold text-white">
            Bienvenido, {{ Auth::user()->nombreCompleto }} 👋
        </h2>
        <p class="text-white/60 text-sm mt-1">
            {{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }} &nbsp;·&nbsp; Administrador FICCT
        </p>
    </div>
    <div class="hidden md:flex items-center gap-3">
        <button onclick="abrirModalImportar()"
                class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-lg transition border border-white/10 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Importar personal
        </button>
        <a href="{{ route('admin.grupos.create') }}"
           class="px-4 py-2 bg-white text-xs font-semibold rounded-lg transition hover:bg-gray-100"
           style="color: #283342;">
            + Nuevo grupo
        </a>
    </div>
</div>

{{-- ── Modal: Carga Masiva de Personal ────────────────────────────────────────── --}}
<div id="modalImportar"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"
     onclick="if(event.target===this) cerrarModalImportar()">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden"
         onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b" style="background-color: #283342;">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <h3 class="text-white font-semibold">Importar Personal Docente</h3>
            </div>
            <button onclick="cerrarModalImportar()"
                    class="text-white/60 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Instrucciones --}}
        <div class="px-6 pt-5 pb-3">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 text-xs text-blue-700">
                Sube un archivo <strong>CSV</strong> con las columnas:
                <code class="bg-blue-100 px-1 rounded">nombre, apellido, ci, correo, nroTelefono, direccion</code>
                (las 3 primeras son obligatorias).
                <a href="{{ route('admin.importar-personal.plantilla') }}"
                   class="block mt-1.5 font-medium underline">Descargar plantilla →</a>
            </div>

            @if($errors->any() && session()->has('_importar'))
                <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                    @foreach($errors->all() as $e)
                        <p class="text-red-600 text-xs">{{ $e }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.importar-personal.store') }}"
                  enctype="multipart/form-data" id="formImportar">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Archivo CSV</label>
                    <label id="dropZoneModal"
                           class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl py-8 cursor-pointer hover:border-[#283342] transition">
                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-gray-500">Arrastra o <span class="text-[#283342] font-medium underline">selecciona</span></p>
                        <p id="nombreArchivoModal" class="text-xs text-gray-400 mt-1">Sin archivo seleccionado</p>
                        <input type="file" id="archivoModal" name="archivo" accept=".csv,text/csv" class="hidden"
                               onchange="document.getElementById('nombreArchivoModal').textContent = this.files[0]?.name ?? 'Sin archivo'">
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="button"
                            onclick="cerrarModalImportar()"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
                            style="background-color: #283342;">
                        Importar
                    </button>
                </div>
            </form>
        </div>
        <p class="text-center text-xs text-gray-400 pb-4">Las cuentas se crean automáticamente si el docente tiene correo.</p>
    </div>
</div>

{{-- ── KPI Cards ──────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $kpiItems = [
            ['label' => 'Postulantes', 'value' => $kpis['totalPostulantes'], 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => '#3B82F6', 'bg' => '#EFF6FF'],
            ['label' => 'Docentes',    'value' => $kpis['totalDocentes'],    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',                                                                                                    'color' => '#8B5CF6', 'bg' => '#F5F3FF'],
            ['label' => 'Pagos aprobados', 'value' => $kpis['pagosAprobados'], 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                            'color' => '#10B981', 'bg' => '#ECFDF5'],
            ['label' => 'Grupos activos', 'value' => $kpis['totalGrupos'],    'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => '#F59E0B', 'bg' => '#FFFBEB'],
        ];
    @endphp

    @foreach($kpiItems as $k)
    <div class="kpi-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ $k['label'] }}</p>
                <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $k['value'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                 style="background-color: {{ $k['bg'] }}">
                <svg class="w-5 h-5" fill="none" stroke="{{ $k['color'] }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $k['icon'] }}"/>
                </svg>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Gráficas fila 1: Barras + Dona ────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Gráfica de barras: Inscritos por carrera --}}
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-semibold text-gray-800">Inscritos por carrera</h3>
                <p class="text-xs text-gray-400 mt-0.5">1ra opción registrada</p>
            </div>
        </div>
        <div class="relative h-56">
            <canvas id="chartCarreras"></canvas>
        </div>
    </div>

    {{-- Gráfica de dona: Estado de pagos --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="mb-5">
            <h3 class="font-semibold text-gray-800">Estado de pagos</h3>
            <p class="text-xs text-gray-400 mt-0.5">Distribución actual</p>
        </div>
        <div class="relative h-44 flex items-center justify-center">
            <canvas id="chartPagos"></canvas>
        </div>
        {{-- Leyenda custom --}}
        <div class="mt-4 space-y-1.5" id="legendaPagos"></div>
    </div>
</div>

{{-- ── Gráfica fila 2: Línea de registros + Bitácora ─────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Gráfica de línea: registros últimos 7 días --}}
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-semibold text-gray-800">Nuevos postulantes</h3>
                <p class="text-xs text-gray-400 mt-0.5">Últimos 7 días</p>
            </div>
        </div>
        <div class="relative h-48">
            <canvas id="chartSemana"></canvas>
        </div>
    </div>

    {{-- Bitácora reciente --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col">
        <div class="mb-4">
            <h3 class="font-semibold text-gray-800">Bitácora reciente</h3>
            <p class="text-xs text-gray-400 mt-0.5">Últimas 6 acciones</p>
        </div>
        <div class="flex-1 space-y-2.5 overflow-hidden">
            @php
                $eventos = Auth::user()->bitacoras()->latest()->take(6)->get();
            @endphp
            @forelse($eventos as $ev)
            <div class="flex gap-3 items-start">
                <div class="w-1.5 h-1.5 rounded-full mt-1.5 shrink-0" style="background-color: #283342;"></div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-700 leading-snug truncate">{{ $ev->descripcion }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">
                        {{ $ev->fecha->format('d/m') }} · {{ substr($ev->hora, 0, 5) }}
                        · {{ $ev->direccionIP }}
                    </p>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center mt-6">Sin eventos aún.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Paleta de colores FICCT ──────────────────────────────────────────────────
const PRIMARY   = '#283342';
const COLORS    = ['#3B82F6','#10B981','#F59E0B','#8B5CF6','#EF4444','#06B6D4','#F97316','#EC4899'];

// Configuración global de Chart.js
Chart.defaults.font.family = 'ui-sans-serif, system-ui, sans-serif';
Chart.defaults.font.size   = 11;
Chart.defaults.color       = '#9CA3AF';
Chart.defaults.plugins.legend.display = false;

// ── 1. Gráfica de BARRAS: inscritos por carrera ───────────────────────────────
const dataCarreras = @json($inscritosPorCarrera);

new Chart(document.getElementById('chartCarreras'), {
    type: 'bar',
    data: {
        labels: dataCarreras.map(d => d.nombre.length > 20 ? d.nombre.substring(0,20)+'…' : d.nombre),
        datasets: [{
            data:            dataCarreras.map(d => d.total),
            backgroundColor: dataCarreras.map((_, i) => COLORS[i % COLORS.length] + 'CC'),
            borderColor:     dataCarreras.map((_, i) => COLORS[i % COLORS.length]),
            borderWidth:     1.5,
            borderRadius:    6,
            borderSkipped:   false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { tooltip: { callbacks: {
            label: ctx => ` ${ctx.parsed.y} inscrito${ctx.parsed.y !== 1 ? 's' : ''}`
        }}},
        scales: {
            x: { grid: { display: false }, ticks: { maxRotation: 30 } },
            y: { grid: { color: '#F3F4F6' }, beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// ── 2. Gráfica de DONA: estado de pagos ──────────────────────────────────────
const dataPagos  = @json($estadosPago);
const pagoLabels = Object.keys(dataPagos).map(k => k.charAt(0).toUpperCase() + k.slice(1));
const pagoValues = Object.values(dataPagos);
const pagoColors = { 'aprobado': '#10B981', 'pendiente': '#F59E0B', 'rechazado': '#EF4444' };

new Chart(document.getElementById('chartPagos'), {
    type: 'doughnut',
    data: {
        labels: pagoLabels,
        datasets: [{
            data:            pagoValues,
            backgroundColor: Object.keys(dataPagos).map(k => pagoColors[k] ?? '#9CA3AF'),
            borderWidth:     2,
            borderColor:     '#ffffff',
            hoverOffset:     4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            tooltip: { callbacks: {
                label: ctx => ` ${ctx.label}: ${ctx.parsed} pago${ctx.parsed !== 1 ? 's' : ''}`
            }}
        }
    }
});

// Leyenda custom de la dona
const legendaEl = document.getElementById('legendaPagos');
Object.keys(dataPagos).forEach((estado, i) => {
    legendaEl.innerHTML += `
        <div class="flex items-center justify-between text-xs">
            <span class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full" style="background:${pagoColors[estado] ?? '#9CA3AF'}"></span>
                <span class="text-gray-600 capitalize">${estado}</span>
            </span>
            <span class="font-semibold text-gray-800">${pagoValues[i]}</span>
        </div>`;
});

// ── 3. Gráfica de LÍNEA: nuevos postulantes por día ──────────────────────────
const diasSemana    = @json($diasSemana->values());
const totalesSemana = @json($totalesSemana->values());

new Chart(document.getElementById('chartSemana'), {
    type: 'line',
    data: {
        labels: diasSemana,
        datasets: [{
            data:            totalesSemana,
            borderColor:     PRIMARY,
            backgroundColor: PRIMARY + '15',
            borderWidth:     2.5,
            pointBackgroundColor: PRIMARY,
            pointRadius:     4,
            pointHoverRadius: 6,
            fill:            true,
            tension:         0.4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { tooltip: { callbacks: {
            label: ctx => ` ${ctx.parsed.y} registro${ctx.parsed.y !== 1 ? 's' : ''}`
        }}},
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: '#F3F4F6' }, beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// ── Modal Importar Personal ───────────────────────────────────────────────────
function abrirModalImportar() {
    const m = document.getElementById('modalImportar');
    m.classList.remove('hidden');
    m.classList.add('flex');
}
function cerrarModalImportar() {
    const m = document.getElementById('modalImportar');
    m.classList.remove('flex');
    m.classList.add('hidden');
}
// Cerrar modal con tecla Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModalImportar(); });
</script>
@endpush
