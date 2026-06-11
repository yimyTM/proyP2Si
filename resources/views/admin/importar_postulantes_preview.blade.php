@extends('layouts.app')

@section('title', 'Resumen de Carga')
@section('page-title', 'Resumen de Carga y Distribución de Grupos')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Resultado de la importación ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-green-700">{{ count($exitosos) }}</p>
            <p class="text-sm text-green-600 mt-1">Postulantes inscritos ahora</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-red-700">{{ count($errores) }}</p>
            <p class="text-sm text-red-600 mt-1">Filas con errores</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center col-span-2 sm:col-span-1">
            <p class="text-3xl font-bold text-blue-700">{{ $totalInscritos }}</p>
            <p class="text-sm text-blue-600 mt-1">Total inscritos en la gestión</p>
        </div>
    </div>

    {{-- Cálculo de grupos ───────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Distribución de grupos — {{ $gestion->nombre }}</h3>
            <p class="text-xs text-gray-400 mt-0.5">Cada grupo admite un máximo de 70 estudiantes.</p>
        </div>

        <div class="p-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center p-4 rounded-xl bg-gray-50">
                <p class="text-2xl font-bold text-gray-800">{{ $gruposNecesarios }}</p>
                <p class="text-xs text-gray-500 mt-1">Grupos necesarios<br><span class="text-gray-400">⌈{{ $totalInscritos }} / 70⌉</span></p>
            </div>
            <div class="text-center p-4 rounded-xl bg-gray-50">
                <p class="text-2xl font-bold text-gray-800">{{ $gruposExistentes }}</p>
                <p class="text-xs text-gray-500 mt-1">Grupos ya existentes</p>
            </div>
            <div class="text-center p-4 rounded-xl {{ $gruposACrear > 0 ? 'bg-amber-50' : 'bg-gray-50' }}">
                <p class="text-2xl font-bold {{ $gruposACrear > 0 ? 'text-amber-700' : 'text-gray-800' }}">{{ $gruposACrear }}</p>
                <p class="text-xs text-gray-500 mt-1">Grupos a crear</p>
            </div>
            <div class="text-center p-4 rounded-xl bg-gray-50">
                <p class="text-2xl font-bold text-gray-800">
                    @if($porGrupoMin === $porGrupoMax){{ $porGrupoMin }}@else{{ $porGrupoMin }}–{{ $porGrupoMax }}@endif
                </p>
                <p class="text-xs text-gray-500 mt-1">Estudiantes por grupo</p>
            </div>
        </div>

        {{-- Confirmación ────────────────────────────────────────────────────── --}}
        @if($totalInscritos === 0)
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-500">No hay inscritos para distribuir.</p>
            </div>
        @else
        <div class="px-6 pb-6 border-t pt-5">
            @if($gruposACrear > 0)
                <p class="text-sm text-gray-600 mb-4">
                    Se crearán <strong>{{ $gruposACrear }}</strong> grupo(s) nuevo(s) y se distribuirán los
                    <strong>{{ $totalInscritos }}</strong> inscritos entre los <strong>{{ $gruposFinales }}</strong> grupos.
                    Elija la modalidad y turno de los grupos nuevos:
                </p>
            @else
                <p class="text-sm text-gray-600 mb-4">
                    No se necesitan grupos nuevos. Se distribuirán los <strong>{{ $totalInscritos }}</strong> inscritos
                    entre los <strong>{{ $gruposFinales }}</strong> grupos existentes.
                </p>
            @endif

            <form method="POST" action="{{ route('admin.importar-postulantes.confirmar') }}"
                  class="flex flex-wrap items-end gap-4"
                  onsubmit="return confirm('¿Confirmar la creación de grupos y distribución de inscritos?')">
                @csrf

                <div class="{{ $gruposACrear > 0 ? '' : 'opacity-50 pointer-events-none' }}">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Modalidad (grupos nuevos)</label>
                    <select name="codeModalidad" class="px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                        @foreach($modalidades as $m)
                            <option value="{{ $m->codeModalidad }}">{{ $m->nombModalidad }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="{{ $gruposACrear > 0 ? '' : 'opacity-50 pointer-events-none' }}">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Turno (grupos nuevos)</label>
                    <select name="idTurno" class="px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                        @foreach($turnos as $t)
                            <option value="{{ $t->idTurno }}">{{ $t->nombTurno }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="px-8 py-2.5 rounded-lg text-white text-sm font-semibold transition hover:opacity-90 shadow-sm"
                        style="background-color: #047857;">
                    ACEPTAR
                </button>
                <a href="{{ route('admin.importar-postulantes') }}"
                   class="px-5 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                    Cargar otro archivo
                </a>
            </form>
        </div>
        @endif
    </div>

    {{-- Errores de importación ──────────────────────────────────────────────── --}}
    @if(count($errores) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-3">Filas no procesadas</h3>
        <ul class="space-y-1">
            @foreach($errores as $err)
                <li class="flex items-start gap-2 text-sm text-red-600"><span class="mt-0.5 shrink-0">✗</span> {{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

</div>
@endsection
