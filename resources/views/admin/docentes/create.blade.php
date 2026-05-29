@extends('layouts.app')

@section('title', 'Nuevo Docente')
@section('page-title', 'Registrar Docente')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <form method="POST" action="{{ route('admin.docentes.store') }}" class="space-y-4">
            @csrf
            @include('admin.docentes._form')

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
                        style="background-color: #283342;">
                    Guardar docente
                </button>
                <a href="{{ route('admin.docentes.index') }}"
                   class="px-6 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
