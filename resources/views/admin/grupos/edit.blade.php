@extends('layouts.app')

@section('title', "Editar Grupo «{{ $grupo->numero_grupo }}»")
@section('page-title', "CU06/CU07 – Editar Grupo «{{ $grupo->numero_grupo }}»")

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Alertas --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    {{-- ── CU06: Datos base del grupo ──────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-amber-50 border border-amber-200">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Datos del grupo</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    Turno: {{ $grupo->turno?->nombTurno }} ·
                    Modalidad: {{ $grupo->modalidad?->nombModalidad }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.grupos.update', $grupo->codigoG) }}">
            @csrf @method('PUT')
            @include('admin.grupos._form')
        </form>
    </div>

    {{-- ── CU07: Asignaciones de materia ───────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #283342;">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">Asignaciones de materia</h3>
                <p class="text-xs text-gray-400 mt-0.5">Materia · Docente · Horario · Aula por grupo</p>
            </div>
        </div>

        {{-- Tabla de asignaciones actuales --}}
        @if($grupo->materiGrupos->isNotEmpty())
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-2">Materia</th>
                        <th class="px-4 py-2">Docente</th>
                        <th class="px-4 py-2">Horario</th>
                        <th class="px-4 py-2">Aula</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($grupo->materiGrupos as $mg)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 font-medium text-gray-800">{{ $mg->materia?->nombMateria ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-gray-600">
                            @if($mg->docente)
                                {{ $mg->docente->nombre }} {{ $mg->docente->apellido }}
                            @else
                                <span class="text-gray-300">Sin docente</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs">
                            @if($mg->horario)
                                {{ $mg->horario->dia }}
                                {{ $mg->horario->hora_ini->format('H:i') }}–{{ $mg->horario->hora_fin->format('H:i') }}
                            @else
                                <span class="text-gray-300">Sin horario</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs">
                            {{ $mg->aula ? 'Aula #'.$mg->aula->idAula.' (cap. '.$mg->aula->capacidad.')' : '—' }}
                        </td>
                        <td class="px-4 py-2.5">
                            <form method="POST"
                                  action="{{ route('grupos.materias.destroy', [$grupo->codigoG, $mg->idMateria]) }}"
                                  onsubmit="return confirm('¿Eliminar la asignación de «{{ $mg->materia?->nombMateria }}»?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                        title="Eliminar asignación">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-gray-400 mb-5">Sin asignaciones. Agrega la primera con el formulario de abajo.</p>
        @endif

        {{-- Formulario: agregar nueva asignación --}}
        <div class="border-t pt-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Agregar asignación</p>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                    @foreach($errors->all() as $e)
                        <p class="text-red-600 text-xs">• {{ $e }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('grupos.materias.store', $grupo->codigoG) }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Materia --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Materia <span class="text-red-500">*</span>
                        </label>
                        <select name="idMateria"
                                class="w-full px-3 py-2.5 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                                       {{ $errors->has('idMateria') ? 'border-red-400' : 'border-gray-300' }}">
                            <option value="">Seleccione materia...</option>
                            @foreach($materias as $m)
                                @if(! $grupo->materiGrupos->contains('idMateria', $m->idMateria))
                                <option value="{{ $m->idMateria }}" {{ old('idMateria') == $m->idMateria ? 'selected' : '' }}>
                                    {{ $m->nombMateria }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- Docente (habilitados) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Docente habilitado <span class="text-red-500">*</span>
                        </label>
                        <select name="codigoDoc"
                                class="w-full px-3 py-2.5 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                                       {{ $errors->has('codigoDoc') ? 'border-red-400' : 'border-gray-300' }}">
                            <option value="">Seleccione docente...</option>
                            @foreach($docentesHabilitados as $d)
                                <option value="{{ $d->codigoDoc }}" {{ old('codigoDoc') == $d->codigoDoc ? 'selected' : '' }}>
                                    {{ $d->nombre }} {{ $d->apellido }} (CI: {{ $d->ci }})
                                </option>
                            @endforeach
                        </select>
                        @if($docentesHabilitados->isEmpty())
                            <p class="text-xs text-amber-600 mt-1">No hay docentes con requisitos completamente validados.</p>
                        @endif
                    </div>

                    {{-- Horario --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Horario <span class="text-red-500">*</span>
                        </label>
                        <select name="idHorario"
                                class="w-full px-3 py-2.5 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                                       {{ $errors->has('idHorario') ? 'border-red-400' : 'border-gray-300' }}">
                            <option value="">Seleccione horario...</option>
                            @foreach($horarios as $h)
                                <option value="{{ $h->idHorario }}" {{ old('idHorario') == $h->idHorario ? 'selected' : '' }}>
                                    {{ $h->dia }} · {{ $h->hora_ini->format('H:i') }} – {{ $h->hora_fin->format('H:i') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Aula --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Aula <span class="text-red-500">*</span>
                        </label>
                        <select name="idAula"
                                class="w-full px-3 py-2.5 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                                       {{ $errors->has('idAula') ? 'border-red-400' : 'border-gray-300' }}">
                            <option value="">Seleccione aula...</option>
                            @foreach($aulas as $a)
                                <option value="{{ $a->idAula }}" {{ old('idAula') == $a->idAula ? 'selected' : '' }}>
                                    Aula #{{ $a->idAula }} — cap. {{ $a->capacidad }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
                            style="background-color: #283342;">
                        Agregar asignación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-right">
        <a href="{{ route('admin.grupos.index') }}"
           class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">
            Volver al listado
        </a>
    </div>

</div>
@endsection
