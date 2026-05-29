@extends('layouts.app')

@section('title', 'Asignación Docente')
@section('page-title', 'CU10 – Asignación Docente y Carga Logística')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Formulario de asignación --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">Nueva asignación</h3>

            {{-- Colisiones detectadas --}}
            @if($errors->has('colision'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-xs font-semibold text-red-700 mb-1">⛔ Colisión de horario detectada:</p>
                    @foreach($errors->get('colision') as $col)
                        @foreach((array) $col as $msg)
                            <p class="text-xs text-red-600 mt-1">• {{ $msg }}</p>
                        @endforeach
                    @endforeach
                </div>
            @endif

            @php
                $otrosErrores = collect($errors->messages())->except('colision')->flatten();
            @endphp
            @if($otrosErrores->isNotEmpty())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    @foreach($otrosErrores as $e)
                        <p class="text-red-600 text-sm">{{ $e }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.asignacion-docente.store') }}">
                @csrf

                @foreach([
                    ['codigoG',   'Grupo',   $grupos,   'codigoG',   fn($g) => "Grupo #{$g->codigoG} — {$g->modalidad?->nombModalidad} / {$g->turno?->nombTurno}"],
                    ['codigoDoc', 'Docente', $docentes, 'codigoDoc', fn($d) => "{$d->nombre} {$d->apellido}"],
                    ['idAula',    'Aula',    $aulas,    'idAula',    fn($a) => "Aula #{$a->idAula} (cap. {$a->capacidad})"],
                    ['idHorario', 'Horario', $horarios, 'idHorario', fn($h) => "{$h->dia} {$h->hora_ini->format('H:i')}–{$h->hora_fin->format('H:i')}"],
                    ['idMateria', 'Materia', $materias, 'idMateria', fn($m) => $m->nombMateria],
                ] as [$name, $label, $col, $pk, $text])
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                    <select name="{{ $name }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                        <option value="">Seleccione {{ strtolower($label) }}...</option>
                        @foreach($col as $item)
                            <option value="{{ $item->$pk }}" {{ old($name) == $item->$pk ? 'selected' : '' }}>
                                {{ $text($item) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endforeach

                <button type="submit"
                        class="w-full py-3 rounded-lg text-white font-semibold text-sm transition hover:opacity-90"
                        style="background-color: #283342;">
                    Guardar asignación
                </button>
            </form>
        </div>

        {{-- Leyenda de colisiones --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <p class="text-xs font-semibold text-blue-800 mb-2">¿Cómo funciona la validación?</p>
            <p class="text-xs text-blue-700">
                El sistema verifica en tiempo real que:<br>
                <strong>① El docente</strong> no tenga ya otro grupo en el mismo día y horario.<br>
                <strong>② El aula</strong> no esté ocupada por otro grupo al mismo tiempo.<br><br>
                Fórmula de solapamiento: dos horarios chocan si
                <code class="bg-blue-100 px-1 rounded">A_ini &lt; B_fin AND B_ini &lt; A_fin</code>
                en el mismo día.
            </p>
        </div>
    </div>

    {{-- Tabla de grupos con sus asignaciones actuales --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-800">Estado actual de los grupos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-3">Grupo</th>
                            <th class="px-4 py-3">Modalidad</th>
                            <th class="px-4 py-3">Docente(s)</th>
                            <th class="px-4 py-3">Horario(s)</th>
                            <th class="px-4 py-3">Aula(s)</th>
                            <th class="px-4 py-3">Materia(s)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($grupos as $g)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">#{{ $g->codigoG }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $g->modalidad?->nombModalidad }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @forelse($g->docentes as $d)
                                    <span class="block">{{ $d->nombre }} {{ $d->apellido }}</span>
                                @empty <span class="text-gray-300">Sin docente</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @forelse($g->horarios as $h)
                                    <span class="block">{{ $h->dia }} {{ $h->hora_ini->format('H:i') }}–{{ $h->hora_fin->format('H:i') }}</span>
                                @empty <span class="text-gray-300">Sin horario</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @forelse($g->aulas as $a)
                                    <span class="block">Aula #{{ $a->idAula }}</span>
                                @empty <span class="text-gray-300">Sin aula</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @forelse($g->materias as $m)
                                    <span class="block">{{ $m->nombMateria }}</span>
                                @empty <span class="text-gray-300">Sin materia</span>
                                @endforelse
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">
                                No hay grupos creados. Primero ejecuta el CU09 para abrir grupos.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
