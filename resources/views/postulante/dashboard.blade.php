@extends('layouts.app')

@section('title', 'Mis Datos – FICCT')
@section('page-title', 'Mis Datos Personales')

@section('content')
@php
    $iniciales = strtoupper(
        mb_substr($postulante?->nombre ?? Auth::user()->nombreCompleto, 0, 1) .
        mb_substr($postulante?->apellidos ?? '', 0, 1)
    );
@endphp

@if(!$postulante)
{{-- Sin perfil aún --}}
<div class="max-w-lg mx-auto mt-10">
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-8 text-center">
        <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <p class="font-bold text-amber-800 mb-1">Perfil incompleto</p>
        <p class="text-sm text-amber-600 mb-4">Aún no has completado tu registro como postulante.</p>
        <a href="{{ route('postulante.expediente') }}"
           class="inline-block px-5 py-2 rounded-lg text-white text-sm font-semibold transition hover:opacity-90"
           style="background:#283342;">Completar mi registro</a>
    </div>
</div>

@else
<div class="max-w-4xl space-y-6">

    {{-- Tarjeta de perfil --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Banner --}}
        <div class="px-6 py-5 flex items-center gap-4"
             style="background: linear-gradient(135deg, #283342 0%, #1a2430 100%);">
            @if($postulante->foto)
            <div class="w-14 h-14 rounded-full shrink-0 overflow-hidden border-2 border-white/30">
                <img src="{{ Storage::url($postulante->foto) }}"
                     alt="{{ $postulante->nombre_completo }}"
                     class="w-full h-full object-cover">
            </div>
            @else
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-xl shrink-0"
                 style="background: rgba(255,255,255,.15);">
                {{ $iniciales }}
            </div>
            @endif
            <div>
                <p class="text-white font-bold text-lg leading-tight">{{ $postulante->nombre_completo }}</p>
                <p class="text-white/60 text-sm">Postulante FICCT</p>
            </div>
            @if($inscripcion?->gestion)
            <div class="ml-auto text-right hidden sm:block">
                <div class="flex items-center gap-1.5 justify-end">
                    <span class="w-2 h-2 rounded-full {{ $inscripcion->gestion->estado === 'Abierta' ? 'bg-green-400' : 'bg-gray-400' }}"></span>
                    <span class="text-white/70 text-xs font-medium">{{ $inscripcion->gestion->nombre }}</span>
                </div>
                <span class="text-white/40 text-xs">Gestión activa</span>
            </div>
            @endif
        </div>

        {{-- Datos en grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2">

            {{-- Columna izquierda --}}
            <div class="divide-y divide-gray-100 border-r border-gray-100">
                @foreach([
                    ['Cédula de Identidad', $postulante->ci],
                    ['Nombre(s)',           $postulante->nombre],
                    ['Apellido(s)',         $postulante->apellidos],
                    ['Fecha de nacimiento', $postulante->fecha_nacimiento?->format('d/m/Y') ?? '—'],
                    ['Sexo',               $postulante->sexo === 'M' ? 'Masculino' : ($postulante->sexo === 'F' ? 'Femenino' : '—')],
                ] as [$label, $valor])
                <div class="px-5 py-3.5 flex justify-between items-center gap-4">
                    <span class="text-xs text-gray-400 shrink-0">{{ $label }}</span>
                    <span class="text-sm font-medium text-gray-800 text-right">{{ $valor }}</span>
                </div>
                @endforeach
            </div>

            {{-- Columna derecha --}}
            <div class="divide-y divide-gray-100">
                @foreach([
                    ['Correo',     Auth::user()->correo],
                    ['Teléfono',   $postulante->nroTelefono ?? '—'],
                    ['Ciudad',     $postulante->ciudad ?? '—'],
                    ['Colegio',    $postulante->colegio_procedencia ?? '—'],
                    ['Dirección',  $postulante->direccion ?? '—'],
                ] as [$label, $valor])
                <div class="px-5 py-3.5 flex justify-between items-start gap-4">
                    <span class="text-xs text-gray-400 shrink-0">{{ $label }}</span>
                    <span class="text-sm font-medium text-gray-800 text-right break-all">{{ $valor }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Resumen rápido --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Pago --}}
        @php
            $pagoResumen = $pago;
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Pago de inscripción</p>
            @if(!$pagoResumen)
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-amber-400 animate-pulse shrink-0"></span>
                    <div>
                        <p class="text-sm font-bold text-amber-700">Pendiente</p>
                        <p class="text-xs text-gray-400">Sin pago registrado</p>
                    </div>
                </div>
            @elseif(in_array($pagoResumen->estado, ['pagado','aprobado']))
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-green-500 shrink-0"></span>
                    <div>
                        <p class="text-sm font-bold text-green-700">Pagado</p>
                        <p class="text-xs text-gray-400">Bs {{ number_format($pagoResumen->monto, 2) }} · {{ $pagoResumen->fecha }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-red-400 animate-pulse shrink-0"></span>
                    <div>
                        <p class="text-sm font-bold text-red-600">{{ ucfirst($pagoResumen->estado) }}</p>
                        <p class="text-xs text-gray-400">Bs {{ number_format($pagoResumen->monto, 2) }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Inscripción --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Inscripción</p>
            @if(!$inscripcion)
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-gray-300 shrink-0"></span>
                    <div>
                        <p class="text-sm font-bold text-gray-500">Sin inscripción</p>
                        <a href="{{ route('postulante.expediente') }}" class="text-xs text-blue-600 hover:underline">Registrarme</a>
                    </div>
                </div>
            @else
                @php
                    [$dot, $txt] = match($inscripcion->estado) {
                        'Validado'  => ['bg-green-500', 'text-green-700'],
                        'Rechazado' => ['bg-red-400',   'text-red-600'],
                        default     => ['bg-amber-400 animate-pulse', 'text-amber-700'],
                    };
                @endphp
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full {{ $dot }} shrink-0"></span>
                    <div>
                        <p class="text-sm font-bold {{ $txt }}">{{ $inscripcion->estado }}</p>
                        <p class="text-xs text-gray-400">{{ $inscripcion->fecha?->format('d/m/Y') }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Notas --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Promedio</p>
            @if($inscripcion?->promedio !== null)
                @php
                    $prom = (float) $inscripcion->promedio;
                    $promColor = $prom >= 60 ? 'text-green-700' : 'text-red-600';
                @endphp
                <div class="flex items-end gap-1.5">
                    <span class="text-3xl font-extrabold {{ $promColor }}">{{ number_format($prom, 1) }}</span>
                    <span class="text-xs text-gray-400 mb-1">/ 100</span>
                </div>
                <p class="text-xs mt-1 font-semibold {{ $prom >= 60 ? 'text-green-600' : 'text-red-500' }}">
                    {{ $inscripcion->resultado ?? '' }}
                </p>
            @else
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-blue-300 animate-pulse shrink-0"></span>
                    <p class="text-sm font-bold text-blue-600">En proceso</p>
                </div>
            @endif
        </div>

    </div>

</div>
@endif
@endsection
