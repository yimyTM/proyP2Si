{{-- Partial: formulario de grupo (reutilizado en create y edit) --}}
@php
    $edicion = isset($grupo);
    $old = fn(string $k, $def = null) => old($k, $edicion ? $grupo->$k ?? $def : $def);
@endphp

@if($errors->any())
    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
        @foreach($errors->all() as $e)
            <p class="text-red-600 text-sm">• {{ $e }}</p>
        @endforeach
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Capacidad --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Capacidad <span class="text-red-500">*</span>
        </label>
        <input type="number" name="capacidad" min="1" max="200"
               value="{{ $old('capacidad', '') }}"
               placeholder="Ej: 40"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
    </div>

    {{-- Modalidad --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Modalidad <span class="text-red-500">*</span>
        </label>
        <select name="codeModalidad"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <option value="">Seleccione...</option>
            @foreach($modalidades as $m)
                <option value="{{ $m->codeModalidad }}"
                    {{ old('codeModalidad', $edicion ? $grupo->codeModalidad : '') == $m->codeModalidad ? 'selected' : '' }}>
                    {{ $m->nombModalidad }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Turno --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Turno <span class="text-red-500">*</span>
        </label>
        <select name="idTurno"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <option value="">Seleccione...</option>
            @foreach($turnos as $t)
                <option value="{{ $t->idTurno }}"
                    {{ old('idTurno', $edicion ? $grupo->idTurno : '') == $t->idTurno ? 'selected' : '' }}>
                    {{ $t->nombTurno }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Horario --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Horario</label>
        <select name="idHorario"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <option value="">Sin horario asignado</option>
            @foreach($horarios as $h)
                <option value="{{ $h->idHorario }}"
                    {{ old('idHorario', $edicion ? ($horarioActual ?? '') : '') == $h->idHorario ? 'selected' : '' }}>
                    {{ $h->dia }} · {{ $h->hora_ini->format('H:i') }} – {{ $h->hora_fin->format('H:i') }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Aula --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Aula</label>
        <select name="idAula"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <option value="">Sin aula asignada</option>
            @foreach($aulas as $a)
                <option value="{{ $a->idAula }}"
                    {{ old('idAula', $edicion ? ($aulaActual ?? '') : '') == $a->idAula ? 'selected' : '' }}>
                    Aula #{{ $a->idAula }} — {{ $a->capacidad }} cupos ({{ $a->cantSillas }} sillas / {{ $a->cantMesas }} mesas)
                </option>
            @endforeach
        </select>
    </div>

    {{-- Materia --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Materia</label>
        <select name="idMateria"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <option value="">Sin materia asignada</option>
            @foreach($materias as $m)
                <option value="{{ $m->idMateria }}"
                    {{ old('idMateria', $edicion ? ($materiaActual ?? '') : '') == $m->idMateria ? 'selected' : '' }}>
                    {{ $m->nombMateria }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Docente --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Docente asignado</label>
        <select name="codigoDoc"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
            <option value="">Sin docente asignado</option>
            @foreach($docentes as $d)
                <option value="{{ $d->codigoDoc }}"
                    {{ old('codigoDoc', $edicion ? ($docenteActual ?? '') : '') == $d->codigoDoc ? 'selected' : '' }}>
                    {{ $d->nombre }} {{ $d->apellido }} (CI: {{ $d->ci }})
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="flex gap-3 mt-6">
    <button type="submit"
            class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90"
            style="background-color: #283342;">
        {{ $edicion ? 'Actualizar Grupo' : 'Crear Grupo' }}
    </button>
    <a href="{{ route('admin.grupos.index') }}"
       class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm hover:bg-gray-50 transition">
        Cancelar
    </a>
</div>
