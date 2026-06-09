@extends('layouts.app')

@section('title', 'Consultar Expedientes')
@section('page-title', 'CU08 – Consultar y Gestionar Expedientes')

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
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Banner de gestión --}}
    @if($gestionVista)
        @if($soloLectura)
            <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm bg-amber-50 border border-amber-200 text-amber-700">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Gestión <strong class="mx-1">{{ $gestionVista->nombre ?? "ID {$gestionVista->idGestion}" }}</strong>
                está <strong class="mx-1">Cerrada</strong> · Solo lectura — no se permite edición
            </div>
        @else
            <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-medium text-white" style="background-color: #283342;">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Gestión activa: <strong class="ml-1">{{ $gestionVista->nombre ?? "ID {$gestionVista->idGestion}" }}</strong>
                &nbsp;·&nbsp; Mostrando expedientes de esta gestión
            </div>
        @endif
    @else
        <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm bg-amber-50 border border-amber-200 text-amber-700">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            No hay expedientes registrados en la gestión actual.
        </div>
    @endif

    {{-- Filtros de búsqueda --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.expedientes') }}">
            <input type="hidden" name="seccion" value="{{ $seccion }}">
            <div class="flex flex-wrap items-end gap-3">

                <div class="flex-1 min-w-[110px]">
                    <label class="block text-xs text-gray-500 mb-1">CI</label>
                    <input type="text" name="buscar_ci" value="{{ $buscarCi }}" placeholder="Buscar CI..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                </div>

                <div class="flex-1 min-w-[110px]">
                    <label class="block text-xs text-gray-500 mb-1">Nombre</label>
                    <input type="text" name="buscar_nombre" value="{{ $buscarNombre }}" placeholder="Nombre..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                </div>

                <div class="flex-1 min-w-[110px]">
                    <label class="block text-xs text-gray-500 mb-1">Apellido</label>
                    <input type="text" name="buscar_apellido" value="{{ $buscarApellido }}" placeholder="Apellido..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1">Estado</label>
                    <select name="estado_expediente"
                            class="px-3 py-2 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                        <option value="">Todos</option>
                        <option value="Validado"  {{ $estadoFiltro === 'Validado'  ? 'selected' : '' }}>Validado</option>
                        <option value="Pendiente" {{ $estadoFiltro === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="Faltante"  {{ $estadoFiltro === 'Faltante'  ? 'selected' : '' }}>Faltante</option>
                    </select>
                </div>

                @if($seccion === 'postulantes')
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Carrera</label>
                    <select name="carrera"
                            class="px-3 py-2 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                        <option value="">Todas</option>
                        @foreach($carreras as $c)
                            <option value="{{ $c->codCarrera }}" {{ $carreraFiltro == $c->codCarrera ? 'selected' : '' }}>
                                {{ $c->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <button type="submit"
                        class="px-4 py-2 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
                        style="background-color:#283342;">
                    Buscar
                </button>

                @if($hayFiltros)
                <a href="{{ route('admin.expedientes', ['seccion' => $seccion]) }}"
                   class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">
                    Limpiar
                </a>
                @endif
            </div>
        </form>
    </div>

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

    {{-- ══ POSTULANTES ══════════════════════════════════════════════════════════ --}}
    @if($seccion === 'postulantes')

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Expedientes de Postulantes</h3>
            <span class="text-xs text-gray-400">{{ $postulantes->count() }} resultado(s)</span>
        </div>

        @if($requisitosP->isEmpty())
            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                No hay requisitos de tipo Postulante configurados.
                <a href="{{ route('admin.requisitos.index') }}" class="text-blue-600 hover:underline">Configurar requisitos</a>
            </div>
        @elseif($postulantes->isEmpty())
            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                @if($hayFiltros)
                    No se encontraron expedientes con los criterios de búsqueda ingresados.
                @elseif(! $gestionVista)
                    No hay expedientes registrados en la gestión actual.
                @else
                    No hay postulantes registrados.
                @endif
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
                        <th class="px-4 py-3 text-center whitespace-nowrap">Inscripción</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($postulantes as $post)
                    @php
                        $entregadosP    = $post->requisitos->keyBy('idReq');
                        $faltantesCount = $requisitosP->filter(fn($r) => !$entregadosP->has($r->idReq))->count();
                        $inscripcion    = $post->inscripciones->first();
                        $pid            = 'p-' . $post->idPost;
                        $cols           = $requisitosP->count() + 5;
                    @endphp

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

                        <td class="px-4 py-3 text-center">
                            @if(! $inscripcion)
                                <span class="text-xs text-gray-400">Sin inscripción</span>
                            @elseif($inscripcion->estado === 'Validado')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200">Validado</span>
                            @elseif($inscripcion->estado === 'Rechazado')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-200">Rechazado</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Pendiente</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-right">
                            <button onclick="togglePanel('{{ $pid }}')"
                                    id="btn-{{ $pid }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition font-medium">
                                {{ $soloLectura ? 'Ver detalle' : 'Editar' }}
                            </button>
                        </td>
                    </tr>

                    {{-- Panel inline --}}
                    <tr id="{{ $pid }}" class="hidden border-t border-dashed border-gray-200 bg-gray-50">
                        <td colspan="{{ $cols }}" class="px-6 py-4">
                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                    @if($soloLectura)
                                        Detalle del expediente (solo lectura) —
                                    @else
                                        Editar estado de requisitos —
                                    @endif
                                    {{ $post->nombre }} {{ $post->apellidos }}
                                </p>

                                @if($soloLectura)
                                    {{-- Modo lectura: solo badges, sin formularios --}}
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($requisitosP as $req)
                                        @php $reg = $entregadosP->get($req->idReq); @endphp
                                        <span class="text-sm text-gray-700">
                                            {{ $req->nombre }}:
                                            @if(!$reg)
                                                <span class="text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Faltante</span>
                                            @elseif($reg->validado)
                                                <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Validado</span>
                                            @else
                                                <span class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">Pendiente</span>
                                            @endif
                                        </span>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Modo edición --}}
                                    @foreach($requisitosP as $req)
                                    @php
                                        $reg = $entregadosP->get($req->idReq);
                                        $estadoActual = !$reg ? 'faltante' : ($reg->validado ? 'validado' : 'pendiente');
                                    @endphp
                                    <div class="flex items-center gap-4 py-2 border-b border-gray-100 last:border-0">
                                        <span class="w-48 text-sm text-gray-700 shrink-0">
                                            {{ $req->nombre }}
                                            @if($req->obligatorio)<span class="text-red-400 text-xs">*</span>@endif
                                        </span>

                                        <form method="POST" action="{{ route('admin.expedientes.postulante.estado', [$post->idPost, $req->idReq]) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="faltante">
                                            <button type="submit" @if($estadoActual === 'faltante') disabled @endif
                                                    class="text-xs px-3 py-1 rounded-lg border transition
                                                           {{ $estadoActual === 'faltante' ? 'bg-red-100 text-red-700 border-red-300 font-semibold cursor-default' : 'border-red-200 text-red-500 hover:bg-red-50' }}">
                                                Faltante
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.expedientes.postulante.estado', [$post->idPost, $req->idReq]) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="pendiente">
                                            <button type="submit" @if($estadoActual === 'pendiente') disabled @endif
                                                    class="text-xs px-3 py-1 rounded-lg border transition
                                                           {{ $estadoActual === 'pendiente' ? 'bg-amber-100 text-amber-700 border-amber-300 font-semibold cursor-default' : 'border-amber-200 text-amber-600 hover:bg-amber-50' }}">
                                                Pendiente
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.expedientes.postulante.estado', [$post->idPost, $req->idReq]) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="validado">
                                            <button type="submit" @if($estadoActual === 'validado') disabled @endif
                                                    class="text-xs px-3 py-1 rounded-lg border transition
                                                           {{ $estadoActual === 'validado' ? 'bg-green-100 text-green-700 border-green-300 font-semibold cursor-default' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                                                Validado
                                            </button>
                                        </form>
                                    </div>
                                    @endforeach

                                    {{-- Resolución del expediente --}}
                                    @if($inscripcion && $inscripcion->estado === 'Pendiente')
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Resolución del expediente</p>
                                        <div class="flex flex-wrap items-start gap-4">
                                            <form method="POST"
                                                  action="{{ route('admin.expedientes.validar', $inscripcion->idInscripcion) }}"
                                                  onsubmit="return confirm('¿Validar el expediente de {{ $post->nombre }} {{ $post->apellidos }}?')">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="text-xs px-4 py-2 rounded-lg border border-green-300 text-green-700 bg-green-50 hover:bg-green-100 transition font-semibold">
                                                    ✓ Validar expediente
                                                </button>
                                            </form>

                                            <div class="flex-1 min-w-[220px]">
                                                <form method="POST"
                                                      action="{{ route('admin.expedientes.rechazar', $inscripcion->idInscripcion) }}">
                                                    @csrf @method('PATCH')
                                                    <div class="flex gap-2 items-start">
                                                        <input type="text" name="motivo_rechazo" placeholder="Motivo del rechazo (opcional)"
                                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-xs outline-none focus:ring-1 focus:ring-red-300">
                                                        <button type="submit"
                                                                class="text-xs px-4 py-2 rounded-lg border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 transition font-semibold whitespace-nowrap">
                                                            ✗ Rechazar
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @elseif($inscripcion && $inscripcion->estado === 'Rechazado' && $inscripcion->motivo_rechazo)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-xs text-red-600"><strong>Motivo del rechazo:</strong> {{ $inscripcion->motivo_rechazo }}</p>
                                    </div>
                                    @endif
                                @endif

                                <div class="pt-3">
                                    <button onclick="togglePanel('{{ $pid }}')"
                                            class="text-xs text-gray-400 hover:text-gray-600 transition">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ══ DOCENTES ══════════════════════════════════════════════════════════════ --}}
    @else

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Expedientes de Docentes</h3>
            <span class="text-xs text-gray-400">{{ $docentes->count() }} resultado(s)</span>
        </div>

        @if($requisitosD->isEmpty())
            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                No hay requisitos de tipo Docente configurados.
                <a href="{{ route('admin.requisitos.index') }}" class="text-blue-600 hover:underline">Configurar requisitos</a>
            </div>
        @elseif($docentes->isEmpty())
            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                @if($hayFiltros)
                    No se encontraron expedientes con los criterios de búsqueda ingresados.
                @elseif(! $gestionVista)
                    No hay expedientes registrados en la gestión actual.
                @else
                    No hay docentes registrados.
                @endif
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
                    @foreach($docentes as $doc)
                    @php
                        $entregadosD    = $doc->requisitosDocente->keyBy('idReq');
                        $faltantesCount = $requisitosD->filter(fn($r) => !$entregadosD->has($r->idReq))->count();
                        $did            = 'd-' . $doc->codigoDoc;
                        $cols           = $requisitosD->count() + 4;
                    @endphp

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
                                {{ $soloLectura ? 'Ver detalle' : 'Editar' }}
                            </button>
                        </td>
                    </tr>

                    {{-- Panel inline --}}
                    <tr id="{{ $did }}" class="hidden border-t border-dashed border-gray-200 bg-gray-50">
                        <td colspan="{{ $cols }}" class="px-6 py-4">
                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                    @if($soloLectura)
                                        Detalle del expediente (solo lectura) —
                                    @else
                                        Editar estado de requisitos —
                                    @endif
                                    {{ $doc->nombre }} {{ $doc->apellido }}
                                </p>

                                @if($soloLectura)
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($requisitosD as $req)
                                        @php $reg = $entregadosD->get($req->idReq); @endphp
                                        <span class="text-sm text-gray-700">
                                            {{ $req->nombre }}:
                                            @if(!$reg)
                                                <span class="text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Faltante</span>
                                            @elseif($reg->validado)
                                                <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Validado</span>
                                            @else
                                                <span class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">Pendiente</span>
                                            @endif
                                        </span>
                                        @endforeach
                                    </div>
                                @else
                                    @foreach($requisitosD as $req)
                                    @php
                                        $reg = $entregadosD->get($req->idReq);
                                        $estadoActual = !$reg ? 'faltante' : ($reg->validado ? 'validado' : 'pendiente');
                                    @endphp
                                    <div class="flex items-center gap-4 py-2 border-b border-gray-100 last:border-0">
                                        <span class="w-48 text-sm text-gray-700 shrink-0">
                                            {{ $req->nombre }}
                                            @if($req->obligatorio)<span class="text-red-400 text-xs">*</span>@endif
                                        </span>

                                        <form method="POST" action="{{ route('admin.expedientes.docente.estado', [$doc->codigoDoc, $req->idReq]) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="faltante">
                                            <button type="submit" @if($estadoActual === 'faltante') disabled @endif
                                                    class="text-xs px-3 py-1 rounded-lg border transition
                                                           {{ $estadoActual === 'faltante' ? 'bg-red-100 text-red-700 border-red-300 font-semibold cursor-default' : 'border-red-200 text-red-500 hover:bg-red-50' }}">
                                                Faltante
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.expedientes.docente.estado', [$doc->codigoDoc, $req->idReq]) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="pendiente">
                                            <button type="submit" @if($estadoActual === 'pendiente') disabled @endif
                                                    class="text-xs px-3 py-1 rounded-lg border transition
                                                           {{ $estadoActual === 'pendiente' ? 'bg-amber-100 text-amber-700 border-amber-300 font-semibold cursor-default' : 'border-amber-200 text-amber-600 hover:bg-amber-50' }}">
                                                Pendiente
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.expedientes.docente.estado', [$doc->codigoDoc, $req->idReq]) }}">
                                            @csrf
                                            <input type="hidden" name="estado" value="validado">
                                            <button type="submit" @if($estadoActual === 'validado') disabled @endif
                                                    class="text-xs px-3 py-1 rounded-lg border transition
                                                           {{ $estadoActual === 'validado' ? 'bg-green-100 text-green-700 border-green-300 font-semibold cursor-default' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                                                Validado
                                            </button>
                                        </form>
                                    </div>
                                    @endforeach
                                @endif

                                <div class="pt-2">
                                    <button onclick="togglePanel('{{ $did }}')"
                                            class="text-xs text-gray-400 hover:text-gray-600 transition">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
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
    const isNowHidden = panel.classList.toggle('hidden');
    if (btn) {
        if (!btn.dataset.openLabel) btn.dataset.openLabel = btn.textContent.trim();
        btn.textContent = isNowHidden ? btn.dataset.openLabel : 'Cerrar';
    }
}
</script>
@endpush
