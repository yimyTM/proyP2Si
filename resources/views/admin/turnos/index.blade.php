@extends('layouts.app')

@section('title', 'Gestión de Turnos')
@section('page-title', 'Gestión de Turnos')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Alertas --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Formulario: Nuevo turno --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Agregar nuevo turno</h3>

        @if($errors->has('nombTurno') && !request()->has('_edit'))
            <p class="text-red-600 text-sm mb-3">{{ $errors->first('nombTurno') }}</p>
        @endif

        <form method="POST" action="{{ route('admin.turnos.store') }}" class="flex gap-3">
            @csrf
            <input type="text" name="nombTurno" value="{{ old('nombTurno') }}"
                   placeholder="Ej: Mañana, Tarde, Noche…"
                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30 focus:border-transparent">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90 whitespace-nowrap"
                    style="background-color: #283342;">
                + Agregar
            </button>
        </form>
    </div>

    {{-- Lista de turnos --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">
                Turnos registrados
                <span class="ml-2 text-sm font-normal text-gray-400">({{ $turnos->count() }})</span>
            </h3>
        </div>

        @if($turnos->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-400 text-sm">No hay turnos registrados aún.</p>
            </div>
        @else
        <ul class="divide-y divide-gray-100">
            @foreach($turnos as $turno)
            <li class="px-6 py-4" id="item-{{ $turno->idTurno }}">

                {{-- Vista normal --}}
                <div id="view-{{ $turno->idTurno }}" class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                             style="background-color: #EEF0F2;">
                            <svg class="w-4 h-4" fill="none" stroke="#283342" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $turno->nombTurno }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $turno->grupos_count }} grupo{{ $turno->grupos_count !== 1 ? 's' : '' }} asignado{{ $turno->grupos_count !== 1 ? 's' : '' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="activarEdicion({{ $turno->idTurno }})"
                                class="p-2 rounded-lg text-gray-400 hover:text-[#283342] hover:bg-gray-100 transition"
                                title="Editar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        @if($turno->grupos_count === 0)
                        <form method="POST" action="{{ route('admin.turnos.destroy', $turno->idTurno) }}"
                              onsubmit="return confirm('¿Eliminar el turno «{{ $turno->nombTurno }}»?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                    title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @else
                        <span class="p-2 text-gray-200 cursor-not-allowed" title="Tiene grupos asignados">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Formulario de edición inline (oculto por defecto) --}}
                <div id="edit-{{ $turno->idTurno }}" class="hidden mt-3">
                    <form method="POST"
                          action="{{ route('admin.turnos.update', $turno->idTurno) }}"
                          class="flex gap-3">
                        @csrf @method('PUT')
                        <input type="hidden" name="_edit" value="1">
                        <input type="text" name="nombTurno"
                               value="{{ old('nombTurno', $turno->nombTurno) }}"
                               class="flex-1 px-4 py-2 border border-[#283342] rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30"
                               autofocus>
                        <button type="submit"
                                class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90"
                                style="background-color: #283342;">
                            Guardar
                        </button>
                        <button type="button"
                                onclick="cancelarEdicion({{ $turno->idTurno }})"
                                class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                    </form>
                    @if($errors->has('nombTurno') && request()->has('_edit'))
                        <p class="text-red-600 text-xs mt-1">{{ $errors->first('nombTurno') }}</p>
                    @endif
                </div>

            </li>
            @endforeach
        </ul>
        @endif
    </div>

    <p class="text-xs text-gray-400 text-center">
        Los turnos con grupos asignados no pueden eliminarse hasta reasignar o eliminar esos grupos.
    </p>
</div>
@endsection

@push('scripts')
<script>
function activarEdicion(id) {
    document.getElementById('view-' + id).classList.add('hidden');
    document.getElementById('edit-' + id).classList.remove('hidden');
    document.querySelector('#edit-' + id + ' input[name="nombTurno"]').focus();
}
function cancelarEdicion(id) {
    document.getElementById('edit-' + id).classList.add('hidden');
    document.getElementById('view-' + id).classList.remove('hidden');
}
</script>
@endpush
