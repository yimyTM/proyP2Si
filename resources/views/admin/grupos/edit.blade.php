@extends('layouts.app')

@section('title', "Editar Grupo #{{ $grupo->codigoG }}")
@section('page-title', "Editar Grupo #{{ $grupo->codigoG }}")

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-amber-50 border border-amber-200">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Editar Grupo #{{ $grupo->codigoG }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    Modalidad: {{ $grupo->modalidad?->nombModalidad }} ·
                    Turno: {{ $grupo->turno?->nombTurno }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.grupos.update', $grupo->codigoG) }}">
            @csrf @method('PUT')
            @include('admin.grupos._form')
        </form>
    </div>
</div>
@endsection
