@extends('layouts.app')

@section('title', 'Gestión de Requisitos')
@section('page-title', 'Gestión de Requisitos')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

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

    {{-- Formulario: Nuevo requisito --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Agregar nuevo requisito</h3>

        @if($errors->hasAny(['nombre', 'tipo']) && !request()->has('_edit'))
            @foreach(['nombre', 'tipo'] as $campo)
                @error($campo)
                    <p class="text-red-600 text-sm mb-1">{{ $message }}</p>
                @enderror
            @endforeach
        @endif

        <form method="POST" action="{{ route('admin.requisitos.store') }}" class="space-y-3">
            @csrf
            <div class="flex gap-3">
                <input type="text" name="nombre" value="{{ old('nombre') }}"
                       placeholder="Ej: Certificado de notas…"
                       class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30 focus:border-transparent">

                <select name="tipo"
                        class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30 focus:border-transparent">
                    <option value="">Tipo…</option>
                    <option value="P" {{ old('tipo') === 'P' ? 'selected' : '' }}>Postulante</option>
                    <option value="D" {{ old('tipo') === 'D' ? 'selected' : '' }}>Docente</option>
                </select>

                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90 whitespace-nowrap"
                        style="background-color: #283342;">
                    + Agregar
                </button>
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                <input type="checkbox" name="obligatorio" value="1"
                       {{ old('obligatorio') ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-[#283342] focus:ring-[#283342]">
                Obligatorio
            </label>
        </form>
    </div>

    {{-- Lista de requisitos --}}
    @foreach([['P', 'Postulante', 'blue'], ['D', 'Docente', 'purple']] as [$tipo, $label, $color])
    @php $grupo = $requisitos->where('tipo', $tipo); @endphp

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center gap-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                {{ $tipo === 'P' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                {{ $tipo }}
            </span>
            <h3 class="font-semibold text-gray-800">
                Requisitos para {{ $label }}
                <span class="ml-2 text-sm font-normal text-gray-400">({{ $grupo->count() }})</span>
            </h3>
        </div>

        @if($grupo->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-gray-400 text-sm">No hay requisitos de tipo {{ $label }} registrados.</p>
            </div>
        @else
        <ul class="divide-y divide-gray-100">
            @foreach($grupo as $req)
            <li class="px-6 py-4" id="item-{{ $req->idReq }}">

                {{-- Vista normal --}}
                <div id="view-{{ $req->idReq }}" class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                             style="background-color: #EEF0F2;">
                            <svg class="w-4 h-4" fill="none" stroke="#283342" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $req->nombre }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $req->obligatorio ? 'Obligatorio' : 'Opcional' }}
                                &middot;
                                @php $total = $tipo === 'P' ? $req->requisito_postulantes_count : $req->requisito_docentes_count; @endphp
                                {{ $total }} asignación{{ $total !== 1 ? 'es' : '' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button onclick="activarEdicion({{ $req->idReq }})"
                                class="p-2 rounded-lg text-gray-400 hover:text-[#283342] hover:bg-gray-100 transition"
                                title="Editar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>

                        @php $sinUsos = ($req->requisito_postulantes_count + $req->requisito_docentes_count) === 0; @endphp
                        @if($sinUsos)
                        <form method="POST" action="{{ route('admin.requisitos.destroy', $req->idReq) }}"
                              onsubmit="return confirm('¿Eliminar el requisito «{{ $req->nombre }}»?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                    title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @else
                        <span class="p-2 text-gray-200 cursor-not-allowed" title="Tiene asignaciones activas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Formulario de edición inline --}}
                <div id="edit-{{ $req->idReq }}" class="hidden mt-3 space-y-3">
                    <form method="POST"
                          action="{{ route('admin.requisitos.update', $req->idReq) }}"
                          class="space-y-3">
                        @csrf @method('PUT')
                        <input type="hidden" name="_edit" value="{{ $req->idReq }}">

                        <div class="flex gap-3">
                            <input type="text" name="nombre"
                                   value="{{ old('nombre', $req->nombre) }}"
                                   class="flex-1 px-4 py-2 border border-[#283342] rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">

                            <select name="tipo"
                                    class="px-4 py-2 border border-[#283342] rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                                <option value="P" {{ old('tipo', $req->tipo) === 'P' ? 'selected' : '' }}>Postulante</option>
                                <option value="D" {{ old('tipo', $req->tipo) === 'D' ? 'selected' : '' }}>Docente</option>
                            </select>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                            <input type="checkbox" name="obligatorio" value="1"
                                   {{ old('obligatorio', $req->obligatorio) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-[#283342] focus:ring-[#283342]">
                            Obligatorio
                        </label>

                        <div class="flex gap-2">
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl text-white text-sm font-medium transition hover:opacity-90"
                                    style="background-color: #283342;">
                                Guardar
                            </button>
                            <button type="button"
                                    onclick="cancelarEdicion({{ $req->idReq }})"
                                    class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">
                                Cancelar
                            </button>
                        </div>
                    </form>

                    @if($errors->hasAny(['nombre', 'tipo']) && request()->input('_edit') == $req->idReq)
                        @foreach(['nombre', 'tipo'] as $campo)
                            @error($campo)
                                <p class="text-red-600 text-xs">{{ $message }}</p>
                            @enderror
                        @endforeach
                    @endif
                </div>

            </li>
            @endforeach
        </ul>
        @endif
    </div>
    @endforeach

    <p class="text-xs text-gray-400 text-center">
        Los requisitos con asignaciones activas no pueden eliminarse.
    </p>
</div>
@endsection

@push('scripts')
<script>
function activarEdicion(id) {
    document.getElementById('view-' + id).classList.add('hidden');
    document.getElementById('edit-' + id).classList.remove('hidden');
    document.querySelector('#edit-' + id + ' input[name="nombre"]').focus();
}
function cancelarEdicion(id) {
    document.getElementById('edit-' + id).classList.add('hidden');
    document.getElementById('view-' + id).classList.remove('hidden');
}
</script>
@endpush
