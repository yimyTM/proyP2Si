@extends('layouts.app')

@section('title', 'Nuevo Docente')
@section('page-title', 'Registrar y Contratar Docente')

@section('content')
<div class="max-w-3xl space-y-5">

    {{-- Precondición: gestión abierta ──────────────────────────────────────── --}}
    @if($gestionActiva)
        <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-3 text-sm text-blue-700 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Gestión activa: <strong>{{ $gestionActiva->nombre }}</strong>. El docente podrá contratarse tras validar sus requisitos.
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 text-sm text-amber-700 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            No hay gestión académica abierta. Puede registrar al docente, pero no contratarlo hasta abrir una gestión.
        </div>
    @endif

    <form method="POST" action="{{ route('admin.docentes.store') }}" class="space-y-5">
        @csrf

        {{-- 1. Datos personales ───────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full text-white text-xs flex items-center justify-center" style="background-color:#283342;">1</span>
                Datos personales
            </h3>
            @include('admin.docentes._form')
        </div>

        {{-- 2. Formación académica ─────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-1 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full text-white text-xs flex items-center justify-center" style="background-color:#283342;">2</span>
                Formación académica
            </h3>
            <p class="text-xs text-gray-400 mb-4">Seleccione los títulos existentes o agregue nuevos (licenciatura/ingeniería, maestría, diplomado).</p>

            @if($formaciones->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                @foreach($formaciones as $f)
                <label class="flex items-start gap-2 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="formaciones[]" value="{{ $f->idForm }}"
                           {{ in_array($f->idForm, old('formaciones', [])) ? 'checked' : '' }}
                           class="mt-0.5 w-4 h-4 rounded border-gray-300" style="accent-color:#283342;">
                    <span class="text-sm text-gray-700 leading-snug">
                        {{ $f->nombProfesion }}
                        @if($f->nroProfesion)<span class="block text-xs text-gray-400">{{ $f->nroProfesion }}</span>@endif
                    </span>
                </label>
                @endforeach
            </div>
            @endif

            <div id="nuevas-profesiones" class="space-y-2"></div>
            <button type="button" onclick="agregarProfesion()"
                    class="mt-2 text-xs px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                + Agregar otra profesión
            </button>
        </div>

        {{-- 3. Requisitos documentales ─────────────────────────────────────── --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-1 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full text-white text-xs flex items-center justify-center" style="background-color:#283342;">3</span>
                Requisitos documentales
            </h3>
            <p class="text-xs text-gray-400 mb-4">Marque los documentos entregados y valídelos. Solo se podrá contratar cuando todos estén validados.</p>

            @if($requisitosDoc->isEmpty())
                <p class="text-sm text-gray-400">No hay requisitos de tipo docente definidos.</p>
            @else
            <div class="space-y-2">
                @foreach($requisitosDoc as $req)
                <div class="flex flex-wrap items-center gap-4 p-3 rounded-lg border border-gray-100">
                    <span class="text-sm text-gray-700 flex-1 min-w-[180px]">
                        {{ $req->nombre }}
                        @if($req->obligatorio)<span class="text-red-500">*</span>@endif
                    </span>
                    <label class="flex items-center gap-1.5 text-xs text-gray-600">
                        <input type="checkbox" name="requisitos[{{ $req->idReq }}][entregado]" value="1"
                               class="w-4 h-4 rounded border-gray-300" style="accent-color:#283342;">
                        Entregado
                    </label>
                    <label class="flex items-center gap-1.5 text-xs text-gray-600">
                        <input type="checkbox" name="requisitos[{{ $req->idReq }}][validado]" value="1"
                               class="w-4 h-4 rounded border-gray-300" style="accent-color:#047857;">
                        Validado
                    </label>
                    <input type="date" name="requisitos[{{ $req->idReq }}][fecha_entrega]"
                           class="px-2 py-1 border border-gray-300 rounded-lg text-xs outline-none focus:ring-2 focus:ring-[#283342]/30">
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Acciones ───────────────────────────────────────────────────────── --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
                    style="background-color: #283342;">
                Registrar docente
            </button>
            <a href="{{ route('admin.docentes.index') }}"
               class="px-6 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                Cancelar
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let profIdx = 0;
function agregarProfesion() {
    const cont = document.getElementById('nuevas-profesiones');
    const row = document.createElement('div');
    row.className = 'flex gap-2 items-center';
    row.innerHTML = `
        <input type="text" name="nuevas_profesiones[${profIdx}][nombProfesion]" placeholder="Nombre de la profesión (ej. Maestría en...)"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
        <input type="text" name="nuevas_profesiones[${profIdx}][nroProfesion]" placeholder="N° registro (opcional)"
               class="w-40 px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm px-2">✕</button>
    `;
    cont.appendChild(row);
    profIdx++;
}
</script>
@endpush
@endsection
