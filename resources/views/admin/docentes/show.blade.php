@extends('layouts.app')

@section('title', $docente->nombre_completo)
@section('page-title', 'Detalle del Docente')

@section('content')
<div class="space-y-5 max-w-4xl">

    {{-- Flash ─────────────────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">{{ $docente->nombre_completo }}</h2>
            <p class="text-sm text-gray-500">CI: {{ $docente->ci }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.docentes.edit', $docente) }}"
               class="px-4 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
               style="background-color: #283342;">Editar</a>
            <a href="{{ route('admin.docentes.index') }}"
               class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">Volver al listado</a>
        </div>
    </div>

    {{-- Información personal ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Información personal</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Teléfono</dt>
                <dd class="font-medium text-gray-800">{{ $docente->nroTelefono ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Dirección</dt>
                <dd class="font-medium text-gray-800">{{ $docente->direccion ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Carga horaria</dt>
                <dd class="font-medium text-gray-800">{{ $docente->carga_horaria ? $docente->carga_horaria . ' hrs' : '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Formación académica ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Formación académica</h3>
        @if($docente->formAcademicas->isEmpty())
            <p class="text-sm text-gray-400">Sin formación académica registrada.</p>
        @else
        <ul class="space-y-2">
            @foreach($docente->formAcademicas as $f)
            <li class="flex items-center gap-2 text-sm text-gray-700">
                <svg class="w-4 h-4 text-[#283342]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                </svg>
                {{ $f->nombProfesion }}
                @if($f->nroProfesion)<span class="text-xs text-gray-400">({{ $f->nroProfesion }})</span>@endif
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    {{-- Requisitos documentales ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Requisitos documentales</h3>
        @if($docente->requisitosDocente->isEmpty())
            <p class="text-sm text-gray-400">Sin requisitos registrados.</p>
        @else
        <div class="space-y-2">
            @foreach($docente->requisitosDocente as $rd)
            <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 text-sm">
                <span class="text-gray-700">{{ $rd->requisito?->nombre ?? 'Requisito #' . $rd->idReq }}</span>
                <div class="flex items-center gap-2">
                    @if($rd->validado)
                        <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Validado</span>
                    @elseif($rd->entregado)
                        <span class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">Entregado, sin validar</span>
                    @else
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Pendiente</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Contratación (CU15) ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Contratación</h3>

        @if($contratado)
            <div class="flex items-center gap-2 text-sm text-green-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span><strong>Contratado</strong> en {{ $gestionActiva->nombre }}.</span>
            </div>
        @elseif(! $gestionActiva)
            <p class="text-sm text-amber-700">No hay gestión académica abierta. Abra una gestión para poder contratar.</p>
        @elseif(! $requisitosOk)
            <p class="text-sm text-amber-700">Faltan requisitos documentales por validar. Valídelos antes de contratar.</p>
        @else
            <p class="text-sm text-gray-500 mb-3">Todos los requisitos están validados. Puede contratar al docente para <strong>{{ $gestionActiva->nombre }}</strong>.</p>
            <form method="POST" action="{{ route('admin.docentes.contratar', $docente) }}"
                  onsubmit="return confirm('¿Contratar a {{ $docente->nombre_completo }} para {{ $gestionActiva->nombre }}?')">
                @csrf
                <button type="submit"
                        class="px-5 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
                        style="background-color: #047857;">
                    Contratar para gestión activa
                </button>
            </form>
        @endif
    </div>

    {{-- Cuenta de acceso (gated: requiere contrato) ─────────────────────────── --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Cuenta de acceso</h3>

        @if($docente->usuario)
            <p class="text-sm text-gray-700 mb-3">
                <span class="text-green-700 font-medium">Cuenta activa</span> — {{ $docente->usuario->correo }}
            </p>
            <form method="POST" action="{{ route('admin.docentes.provisionar-cuenta', $docente) }}"
                  onsubmit="return confirm('¿Generar una nueva contraseña para este docente?')">
                @csrf
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                    Restablecer contraseña
                </button>
            </form>
        @elseif($contratado)
            <p class="text-sm text-gray-500 mb-3">El docente está contratado. Ingrese un correo para generar sus credenciales provisionales.</p>
            <form method="POST" action="{{ route('admin.docentes.provisionar-cuenta', $docente) }}" class="flex flex-wrap gap-2 items-start">
                @csrf
                <input type="email" name="correo" value="{{ old('correo') }}" required placeholder="correo@ficct.edu.bo"
                       class="flex-1 min-w-[220px] px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                <button type="submit"
                        class="px-5 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
                        style="background-color: #283342;">
                    Crear cuenta
                </button>
            </form>
        @else
            <p class="text-sm text-amber-700">Contrate al docente para habilitar la creación de su cuenta de acceso.</p>
        @endif
    </div>

    {{-- Grupos asignados ───────────────────────────────────────────────────── --}}
    @if($docente->grupos->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Grupos asignados ({{ $docente->grupos->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Grupo</th>
                        <th class="px-6 py-3">Turno</th>
                        <th class="px-6 py-3">Modalidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($docente->grupos as $grupo)
                    <tr>
                        <td class="px-6 py-3">Grupo #{{ $grupo->codigoG }}</td>
                        <td class="px-6 py-3">{{ $grupo->turno?->nombTurno ?? '—' }}</td>
                        <td class="px-6 py-3">{{ $grupo->modalidad?->nombModalidad ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.docentes.destroy', $docente) }}"
          onsubmit="return confirm('¿Eliminar permanentemente a {{ $docente->nombre_completo }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-red-600 hover:underline">Eliminar docente</button>
    </form>

</div>
@endsection
