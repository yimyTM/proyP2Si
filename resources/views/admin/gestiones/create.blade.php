@extends('layouts.app')

@section('title', 'Nueva Gestión')
@section('page-title', 'Crear Gestión Académica')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500 mb-4">La gestión se creará en estado <strong>Cerrada</strong>. Luego configurará los <strong>cupos por carrera</strong> (pueden ser distintos para cada una).</p>
        <form method="POST" action="{{ route('admin.gestiones.store') }}" class="space-y-4">
            @csrf
            @include('admin.gestiones._form')

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
                        style="background-color: #283342;">
                    Crear gestión
                </button>
                <a href="{{ route('admin.gestiones.index') }}"
                   class="px-6 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
