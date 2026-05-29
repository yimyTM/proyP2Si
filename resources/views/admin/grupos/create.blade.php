@extends('layouts.app')

@section('title', 'Nuevo Grupo')
@section('page-title', 'Crear Nuevo Grupo')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #283342;">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Nuevo Grupo Académico</h3>
                <p class="text-xs text-gray-400 mt-0.5">Configura manualmente todos los campos del grupo</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.grupos.store') }}">
            @csrf
            @include('admin.grupos._form')
        </form>
    </div>
</div>
@endsection
