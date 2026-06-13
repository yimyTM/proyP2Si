@extends('layouts.app')

@section('title', 'Solicitudes de materia')
@section('page-title', 'Solicitudes de Materia de Docentes')

@section('content')
<div class="space-y-5">

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Filtros por estado --}}
    @php
        $tabs = [
            null        => ['Todas',      $conteos->sum()],
            'pendiente' => ['Pendientes', $conteos['pendiente'] ?? 0],
            'aceptado'  => ['Aceptadas',  $conteos['aceptado'] ?? 0],
            'rechazado' => ['Rechazadas', $conteos['rechazado'] ?? 0],
        ];
    @endphp
    <div class="flex flex-wrap gap-2">
        @foreach($tabs as $val => [$label, $count])
        <a href="{{ route('admin.solicitudes.index', $val ? ['estado' => $val] : []) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition border
                  {{ $estado === $val ? 'bg-[#283342] text-white border-[#283342]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            {{ $label }}
            <span class="ml-1 text-xs {{ $estado === $val ? 'text-white/70' : 'text-gray-400' }}">({{ $count }})</span>
        </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($solicitudes->isEmpty())
            <p class="px-6 py-12 text-center text-gray-400 text-sm">No hay solicitudes para este filtro.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Docente</th>
                        <th class="px-6 py-3">Materia</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($solicitudes as $s)
                    @php
                        $badge = match ($s->estado) {
                            'aceptado'  => 'bg-green-100 text-green-700',
                            'rechazado' => 'bg-red-100 text-red-700',
                            default     => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $s->nombre }} {{ $s->apellido }}</td>
                        <td class="px-6 py-3 text-gray-700">{{ $s->nombMateria }}</td>
                        <td class="px-6 py-3">
                            <span class="text-xs px-2.5 py-0.5 rounded-full {{ $badge }}">{{ ucfirst($s->estado) }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center justify-end gap-2">
                                @if($s->estado !== 'aceptado')
                                <form method="POST" action="{{ route('admin.solicitudes.aceptar', [$s->codigoDoc, $s->idMateria]) }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium transition">
                                        Aceptar
                                    </button>
                                </form>
                                @endif
                                @if($s->estado !== 'rechazado')
                                <form method="POST" action="{{ route('admin.solicitudes.rechazar', [$s->codigoDoc, $s->idMateria]) }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-red-50 text-red-600 font-medium transition border border-gray-200">
                                        Rechazar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
