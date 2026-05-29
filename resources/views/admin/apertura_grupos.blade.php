@extends('layouts.app')

@section('title', 'Apertura de Grupos')
@section('page-title', 'CU09 – Apertura Automática de Grupos Académicos')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Explicación del algoritmo --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
        <h3 class="font-semibold text-blue-800 mb-2">¿Cómo funciona el algoritmo?</h3>
        <div class="text-sm text-blue-700 space-y-2">
            <p><strong>Para cada carrera</strong> el sistema calcula automáticamente:</p>
            <ol class="list-decimal list-inside space-y-1 ml-2">
                <li>Cuenta los postulantes con inscripción <em>validada</em> que eligieron esa carrera como 1ra opción.</li>
                <li>Calcula cuántos grupos abrir según los <strong>cupos configurados por carrera</strong>: <code class="bg-blue-100 px-1 rounded">grupos = ⌈inscritos ÷ cupos⌉</code></li>
                <li>Distribuye los cupos de forma equitativa entre los grupos.</li>
            </ol>
            <p class="mt-2 italic text-blue-600">
                Ejemplo: 115 inscritos, máximo 40/grupo → 3 grupos (1 de 39 y 2 de 38).
            </p>
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
            @foreach($errors->all() as $e)
                <p class="text-red-600 text-sm">{{ $e }}</p>
            @endforeach
        </div>
    @endif

    {{-- Formulario de apertura --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-5">Configurar apertura</h3>

        <form method="POST" action="{{ route('admin.grupos.index') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Gestión académica</label>
                <select name="idGestion" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                    <option value="">Seleccione la gestión...</option>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->idGestion }}" {{ old('idGestion') == $g->idGestion ? 'selected' : '' }}>
                            Gestión #{{ $g->idGestion }} —
                            {{ $g->fecha_ini->format('d/m/Y') }} al {{ $g->fecha_fin->format('d/m/Y') }}
                            [{{ $g->estado }}] — {{ $g->carreras_count }} carrera(s) con cupos
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Turno</label>
                <select name="idTurno" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                    <option value="">Seleccione el turno...</option>
                    @foreach($turnos as $t)
                        <option value="{{ $t->idTurno }}" {{ old('idTurno') == $t->idTurno ? 'selected' : '' }}>
                            {{ $t->nombTurno }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-5">
                <p class="text-xs text-amber-800">
                    <strong>⚠ Atención:</strong> Esta operación creará grupos en la base de datos.
                    Asegúrate de ejecutarla una sola vez por gestión para evitar duplicados.
                </p>
            </div>

            <button type="submit"
                    class="w-full py-3 rounded-lg text-white font-semibold text-sm transition hover:opacity-90"
                    style="background-color: #283342;">
                Ejecutar algoritmo de apertura
            </button>
        </form>
    </div>

</div>
@endsection
