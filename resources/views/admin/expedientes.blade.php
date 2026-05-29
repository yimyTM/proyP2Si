@extends('layouts.app')

@section('title', 'Supervisión de Expedientes')
@section('page-title', 'CU04 (Admin) – Supervisión de Expedientes')

@section('content')
<div class="space-y-5">

    {{-- Alertas --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 bg-white rounded-xl shadow-sm border border-gray-100 p-1 w-fit">
        <a href="{{ request()->fullUrlWithQuery(['seccion' => 'postulantes']) }}"
           class="px-5 py-2 rounded-lg text-sm font-medium transition {{ $seccion === 'postulantes' ? 'text-white shadow-sm' : 'text-gray-500 hover:text-gray-800' }}"
           @if($seccion === 'postulantes') style="background-color:#283342" @endif>
            Postulantes
        </a>
        <a href="{{ request()->fullUrlWithQuery(['seccion' => 'docentes']) }}"
           class="px-5 py-2 rounded-lg text-sm font-medium transition {{ $seccion === 'docentes' ? 'text-white shadow-sm' : 'text-gray-500 hover:text-gray-800' }}"
           @if($seccion === 'docentes') style="background-color:#283342" @endif>
            Docentes
        </a>
    </div>

    {{-- ══ POSTULANTES ══════════════════════════════════════════════════════ --}}
    @if($seccion === 'postulantes')

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Expedientes de Postulantes</h3>
        </div>

        @if($requisitosP->isEmpty())
            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                No hay requisitos de tipo Postulante configurados.
                <a href="{{ route('admin.requisitos.index') }}" class="text-blue-600 hover:underline">Configurar requisitos</a>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Postulante</th>
                        <th class="px-4 py-3">CI</th>
                        @foreach($requisitosP as $req)
                            <th class="px-4 py-3 text-center whitespace-nowrap">
                                {{ $req->nombre }}@if($req->obligatorio)<span class="text-red-400">*</span>@endif
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-center">Faltantes</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($postulantes as $post)
                    @php
                        $entregadosP    = $post->requisitos->keyBy('idReq');
                        $faltantesCount = $requisitosP->filter(fn($r) => !$entregadosP->has($r->idReq))->count();
                        $pid            = 'p-' . $post->idPost;
                        $cols           = $requisitosP->count() + 4;
                    @endphp

                    {{-- Fila normal --}}
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800 whitespace-nowrap">
                            {{ $post->nombre }} {{ $post->apellidos }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $post->ci }}</td>

                        @foreach($requisitosP as $req)
                        @php $reg = $entregadosP->get($req->idReq); @endphp
                        <td class="px-4 py-3 text-center">
                            @if(!$reg)
                                <span class="text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Faltante</span>
                            @elseif($reg->validado)
                                <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Validado</span>
                            @else
                                <span class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">Pendiente</span>
                            @endif
                        </td>
                        @endforeach

                        <td class="px-4 py-3 text-center">
                            @if($faltantesCount === 0)
                                <span class="text-xs text-green-700 font-medium">Completo</span>
                            @else
                                <span class="inline-flex items-center justify-center text-xs font-bold text-white bg-red-500 rounded-full w-6 h-6">{{ $faltantesCount }}</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-right">
                            <button onclick="togglePanel('{{ $pid }}')"
                                    id="btn-{{ $pid }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition font-medium">
                                Editar
                            </button>
                        </td>
                    </tr>

                    {{-- Panel de edición inline --}}
                    <tr id="{{ $pid }}" class="hidden border-t border-dashed border-gray-200 bg-gray-50">
                        <td colspan="{{ $cols }}" class="px-6 py-4">
                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                    Editar estado de requisitos — {{ $post->nombre }} {{ $post->apellidos }}
                                </p>
                                @foreach($requisitosP as $req)
                                @php $reg = $entregadosP->get($req->idReq); @endphp
                                @php
                                    $estadoActual = !$reg ? 'faltante' : ($reg->validado ? 'validado' : 'pendiente');
                                @endphp
                                <div class="flex items-center gap-4 py-2 border-b border-gray-100 last:border-0">
                                    <span class="w-48 text-sm text-gray-700 shrink-0">
                                        {{ $req->nombre }}
                                        @if($req->obligatorio)<span class="text-red-400 text-xs">*</span>@endif
                                    </span>

                                    {{-- Botón Faltante --}}
                                    <form method="POST" action="{{ route('admin.expedientes.postulante.estado', [$post->idPost, $req->idReq]) }}">
                                        @csrf
                                        <input type="hidden" name="estado" value="faltante">
                                        <button type="submit"
                                                @if($estadoActual === 'faltante') disabled @endif
                                                class="text-xs px-3 py-1 rounded-lg border transition
                                                       {{ $estadoActual === 'faltante'
                                                          ? 'bg-red-100 text-red-700 border-red-300 font-semibold cursor-default'
                                                          : 'border-red-200 text-red-500 hover:bg-red-50' }}">
                                            Faltante
                                        </button>
                                    </form>

                                    {{-- Botón Pendiente --}}
                                    <form method="POST" action="{{ route('admin.expedientes.postulante.estado', [$post->idPost, $req->idReq]) }}">
                                        @csrf
                                        <input type="hidden" name="estado" value="pendiente">
                                        <button type="submit"
                                                @if($estadoActual === 'pendiente') disabled @endif
                                                class="text-xs px-3 py-1 rounded-lg border transition
                                                       {{ $estadoActual === 'pendiente'
                                                          ? 'bg-amber-100 text-amber-700 border-amber-300 font-semibold cursor-default'
                                                          : 'border-amber-200 text-amber-600 hover:bg-amber-50' }}">
                                            Pendiente
                                        </button>
                                    </form>

                                    {{-- Botón Validado --}}
                                    <form method="POST" action="{{ route('admin.expedientes.postulante.estado', [$post->idPost, $req->idReq]) }}">
                                        @csrf
                                        <input type="hidden" name="estado" value="validado">
                                        <button type="submit"
                                                @if($estadoActual === 'validado') disabled @endif
                                                class="text-xs px-3 py-1 rounded-lg border transition
                                                       {{ $estadoActual === 'validado'
                                                          ? 'bg-green-100 text-green-700 border-green-300 font-semibold cursor-default'
                                                          : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                                            Validado
                                        </button>
                                    </form>
                                </div>
                                @endforeach

                                <div class="pt-2">
                                    <button onclick="togglePanel('{{ $pid }}')"
                                            class="text-xs text-gray-400 hover:text-gray-600 transition">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="{{ $requisitosP->count() + 4 }}" class="px-6 py-10 text-center text-gray-400 text-sm">
                            No hay postulantes registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ══ DOCENTES ══════════════════════════════════════════════════════════ --}}
    @else

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Expedientes de Docentes</h3>
        </div>

        @if($requisitosD->isEmpty())
            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                No hay requisitos de tipo Docente configurados.
                <a href="{{ route('admin.requisitos.index') }}" class="text-blue-600 hover:underline">Configurar requisitos</a>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Docente</th>
                        <th class="px-4 py-3">CI</th>
                        @foreach($requisitosD as $req)
                            <th class="px-4 py-3 text-center whitespace-nowrap">
                                {{ $req->nombre }}@if($req->obligatorio)<span class="text-red-400">*</span>@endif
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-center">Faltantes</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($docentes as $doc)
                    @php
                        $entregadosD    = $doc->requisitosDocente->keyBy('idReq');
                        $faltantesCount = $requisitosD->filter(fn($r) => !$entregadosD->has($r->idReq))->count();
                        $did            = 'd-' . $doc->codigoDoc;
                        $cols           = $requisitosD->count() + 4;
                    @endphp

                    {{-- Fila normal --}}
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800 whitespace-nowrap">
                            {{ $doc->nombre }} {{ $doc->apellido }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $doc->ci }}</td>

                        @foreach($requisitosD as $req)
                        @php $reg = $entregadosD->get($req->idReq); @endphp
                        <td class="px-4 py-3 text-center">
                            @if(!$reg)
                                <span class="text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Faltante</span>
                            @elseif($reg->validado)
                                <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Validado</span>
                            @else
                                <span class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">Pendiente</span>
                            @endif
                        </td>
                        @endforeach

                        <td class="px-4 py-3 text-center">
                            @if($faltantesCount === 0)
                                <span class="text-xs text-green-700 font-medium">Completo</span>
                            @else
                                <span class="inline-flex items-center justify-center text-xs font-bold text-white bg-red-500 rounded-full w-6 h-6">{{ $faltantesCount }}</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-right">
                            <button onclick="togglePanel('{{ $did }}')"
                                    id="btn-{{ $did }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition font-medium">
                                Editar
                            </button>
                        </td>
                    </tr>

                    {{-- Panel de edición inline --}}
                    <tr id="{{ $did }}" class="hidden border-t border-dashed border-gray-200 bg-gray-50">
                        <td colspan="{{ $cols }}" class="px-6 py-4">
                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                    Editar estado de requisitos — {{ $doc->nombre }} {{ $doc->apellido }}
                                </p>
                                @foreach($requisitosD as $req)
                                @php $reg = $entregadosD->get($req->idReq); @endphp
                                @php
                                    $estadoActual = !$reg ? 'faltante' : ($reg->validado ? 'validado' : 'pendiente');
                                @endphp
                                <div class="flex items-center gap-4 py-2 border-b border-gray-100 last:border-0">
                                    <span class="w-48 text-sm text-gray-700 shrink-0">
                                        {{ $req->nombre }}
                                        @if($req->obligatorio)<span class="text-red-400 text-xs">*</span>@endif
                                    </span>

                                    {{-- Botón Faltante --}}
                                    <form method="POST" action="{{ route('admin.expedientes.docente.estado', [$doc->codigoDoc, $req->idReq]) }}">
                                        @csrf
                                        <input type="hidden" name="estado" value="faltante">
                                        <button type="submit"
                                                @if($estadoActual === 'faltante') disabled @endif
                                                class="text-xs px-3 py-1 rounded-lg border transition
                                                       {{ $estadoActual === 'faltante'
                                                          ? 'bg-red-100 text-red-700 border-red-300 font-semibold cursor-default'
                                                          : 'border-red-200 text-red-500 hover:bg-red-50' }}">
                                            Faltante
                                        </button>
                                    </form>

                                    {{-- Botón Pendiente --}}
                                    <form method="POST" action="{{ route('admin.expedientes.docente.estado', [$doc->codigoDoc, $req->idReq]) }}">
                                        @csrf
                                        <input type="hidden" name="estado" value="pendiente">
                                        <button type="submit"
                                                @if($estadoActual === 'pendiente') disabled @endif
                                                class="text-xs px-3 py-1 rounded-lg border transition
                                                       {{ $estadoActual === 'pendiente'
                                                          ? 'bg-amber-100 text-amber-700 border-amber-300 font-semibold cursor-default'
                                                          : 'border-amber-200 text-amber-600 hover:bg-amber-50' }}">
                                            Pendiente
                                        </button>
                                    </form>

                                    {{-- Botón Validado --}}
                                    <form method="POST" action="{{ route('admin.expedientes.docente.estado', [$doc->codigoDoc, $req->idReq]) }}">
                                        @csrf
                                        <input type="hidden" name="estado" value="validado">
                                        <button type="submit"
                                                @if($estadoActual === 'validado') disabled @endif
                                                class="text-xs px-3 py-1 rounded-lg border transition
                                                       {{ $estadoActual === 'validado'
                                                          ? 'bg-green-100 text-green-700 border-green-300 font-semibold cursor-default'
                                                          : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                                            Validado
                                        </button>
                                    </form>
                                </div>
                                @endforeach

                                <div class="pt-2">
                                    <button onclick="togglePanel('{{ $did }}')"
                                            class="text-xs text-gray-400 hover:text-gray-600 transition">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="{{ $requisitosD->count() + 4 }}" class="px-6 py-10 text-center text-gray-400 text-sm">
                            No hay docentes registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>

    @endif

    <p class="text-xs text-gray-400"><span class="text-red-400 font-bold">*</span> Requisito obligatorio</p>
</div>
@endsection

@push('scripts')
<script>
function togglePanel(id) {
    const panel = document.getElementById(id);
    const btn   = document.getElementById('btn-' + id);
    const open  = panel.classList.toggle('hidden');
    btn.textContent = open ? 'Editar' : 'Cerrar';
}
</script>
@endpush
