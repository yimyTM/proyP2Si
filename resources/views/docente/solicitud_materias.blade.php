@extends('layouts.app')

@section('title', 'Solicitar materias')
@section('page-title', 'Solicitud de Materias a Dictar')

@section('content')
<div class="max-w-2xl space-y-5">

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 text-sm text-blue-700">
        Marca las materias que deseas dictar. Cada solicitud queda <strong>pendiente</strong> hasta que un coordinador o
        administrador la <strong>acepte</strong>. Solo podrás ser asignado a una materia si tu solicitud fue aceptada.
    </div>

    <form method="POST" action="{{ route('docente.solicitud-materias.store') }}">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-800">Materias disponibles</h3>
                <p class="text-xs text-gray-400 mt-0.5">Docente: {{ $docente->nombre_completo }}</p>
            </div>

            <div class="divide-y divide-gray-50">
                @foreach($materias as $m)
                @php
                    $estado = $solicitudes[$m->idMateria] ?? null;
                    $badge = match ($estado) {
                        'aceptado'  => ['bg-green-100 text-green-700', 'Aceptada'],
                        'rechazado' => ['bg-red-100 text-red-700', 'Rechazada'],
                        'pendiente' => ['bg-amber-100 text-amber-700', 'Pendiente'],
                        default     => null,
                    };
                    $checked  = in_array($estado, ['pendiente', 'aceptado'], true);
                    $bloqueada = $estado === 'aceptado'; // no puede retirar una aceptada
                @endphp
                <label class="flex items-center justify-between gap-3 px-6 py-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="materias[]" value="{{ $m->idMateria }}"
                               {{ $checked ? 'checked' : '' }} {{ $bloqueada ? 'disabled' : '' }}
                               class="w-4 h-4 rounded border-gray-300" style="accent-color:#283342;">
                        @if($bloqueada)
                            {{-- mantener el valor si está deshabilitado para no perder la aceptada --}}
                            <input type="hidden" name="materias[]" value="{{ $m->idMateria }}">
                        @endif
                        <span class="text-sm font-medium text-gray-800">{{ $m->nombMateria }}</span>
                    </div>
                    @if($badge)
                        <span class="text-xs px-2.5 py-0.5 rounded-full {{ $badge[0] }}">{{ $badge[1] }}</span>
                    @else
                        <span class="text-xs text-gray-300">Sin solicitar</span>
                    @endif
                </label>
                @endforeach
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
                <p class="text-xs text-gray-400">Las materias aceptadas no se pueden retirar desde aquí.</p>
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg text-white text-sm font-semibold transition hover:opacity-90"
                        style="background-color: #283342;">
                    Guardar solicitudes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
