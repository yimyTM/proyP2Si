@extends('layouts.app')

@section('title', 'Registrar Aula')
@section('page-title', 'CU03 – Registrar Nueva Aula')

@section('content')
<div class="max-w-lg mx-auto">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #283342;">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Nueva aula</h3>
                <p class="text-xs text-gray-400 mt-0.5">El ID se asigna automáticamente</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.aulas.store') }}">
            @csrf
            @php $edicion = false; $aula = null; @endphp
            @include('admin.aulas._form')

            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
                        style="background-color: #283342;">
                    Registrar aula
                </button>
                <a href="{{ route('admin.aulas.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
