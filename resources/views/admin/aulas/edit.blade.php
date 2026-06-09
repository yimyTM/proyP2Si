@extends('layouts.app')

@section('title', "Editar Aula #{{ $aula->idAula }}")
@section('page-title', "CU03 – Editar Aula #{{ $aula->idAula }}")

@section('content')
<div class="max-w-lg mx-auto space-y-5">

    {{-- Alertas --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Banner de asignaciones activas --}}
    @if($asignaciones > 0)
        <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm bg-blue-50 border border-blue-200 text-blue-700">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Esta aula tiene <strong class="mx-1">{{ $asignaciones }}</strong> asignación(es) activa(s) en grupos.
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-amber-50 border border-amber-200">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Aula #{{ $aula->idAula }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">Capacidad actual: {{ $aula->capacidad }} cupos</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.aulas.update', $aula->idAula) }}">
            @csrf @method('PUT')
            @php $edicion = true; @endphp
            @include('admin.aulas._form')

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
                        style="background-color: #283342;">
                    Guardar cambios
                </button>
                <a href="{{ route('admin.aulas.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    {{-- Eliminar (solo si sin asignaciones) --}}
    @if($asignaciones === 0)
    <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5">
        <p class="text-sm font-semibold text-red-700 mb-1">Zona de eliminación</p>
        <p class="text-xs text-gray-500 mb-4">Esta aula no tiene asignaciones activas y puede eliminarse.</p>
        <form method="POST"
              action="{{ route('admin.aulas.destroy', $aula->idAula) }}"
              onsubmit="return confirm('¿Eliminar permanentemente el Aula #{{ $aula->idAula }}?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="px-5 py-2 rounded-xl border border-red-300 text-red-700 text-sm hover:bg-red-50 transition font-medium">
                Eliminar aula
            </button>
        </form>
    </div>
    @endif

</div>
@endsection
