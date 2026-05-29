@extends('layouts.app')

@section('title', 'Editar Postulante')
@section('page-title', 'Editar Postulante')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <form method="POST" action="{{ route('admin.postulantes.update', $postulante) }}" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.postulantes._form', ['postulante' => $postulante])

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
                        style="background-color: #283342;">
                    Actualizar postulante
                </button>
                <a href="{{ route('admin.postulantes.show', $postulante) }}"
                   class="px-6 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
