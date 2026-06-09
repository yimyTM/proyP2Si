@extends('layouts.app')

@section('title', 'Registrar Expediente')
@section('page-title', 'CU05 – Registrar Postulante / Expediente Digital')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Alertas --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Resumen si ya tiene expediente --}}
    @isset($inscripcionExistente)
    @php
        $estadoColor = match($inscripcionExistente->estado) {
            'Validado'  => ['bg-green-50','border-green-200','text-green-800','text-green-700'],
            'Rechazado' => ['bg-red-50','border-red-200','text-red-800','text-red-700'],
            default     => ['bg-amber-50','border-amber-200','text-amber-800','text-amber-700'],
        };
    @endphp
    <div class="rounded-xl p-5 border {{ $estadoColor[0] }} {{ $estadoColor[1] }}">
        <div class="flex items-center gap-3 mb-3">
            @if($inscripcionExistente->estado === 'Validado')
                <span class="{{ $estadoColor[2] }} text-xl">✓</span>
            @elseif($inscripcionExistente->estado === 'Rechazado')
                <span class="{{ $estadoColor[2] }} text-xl">✗</span>
            @else
                <span class="{{ $estadoColor[2] }} text-xl">⏳</span>
            @endif
            <h3 class="font-semibold {{ $estadoColor[2] }}">
                Expediente {{ $inscripcionExistente->estado }}
            </h3>
        </div>
        <p class="text-sm {{ $estadoColor[3] }}">
            Fecha de registro: <strong>{{ $inscripcionExistente->fecha->format('d/m/Y') }}</strong>
        </p>

        @if($inscripcionExistente->estado === 'Rechazado' && $inscripcionExistente->motivo_rechazo)
            <p class="text-sm mt-2 {{ $estadoColor[3] }}">
                <strong>Motivo del rechazo:</strong> {{ $inscripcionExistente->motivo_rechazo }}
            </p>
        @endif

        <ul class="mt-3 space-y-1">
            @foreach($inscripcionExistente->carrerasInscritas->sortBy('prioridad') as $ci)
            <li class="text-sm {{ $estadoColor[3] }}">
                Opción {{ $ci->prioridad }}: <strong>{{ $ci->carrera->nombre }}</strong>
            </li>
            @endforeach
        </ul>

        {{-- Estado de documentos --}}
        <div class="mt-4 grid grid-cols-2 gap-2">
            @foreach($documentos as $nombre => $estado)
            <div class="flex items-center gap-2 text-xs {{ $estadoColor[3] }}">
                @if($estado['validado'])
                    <span class="text-green-600">✓</span> {{ $nombre }} <span class="text-green-600">(Validado)</span>
                @elseif($estado['entregado'])
                    <span class="text-amber-600">⏳</span> {{ $nombre }} <span class="text-amber-600">(En revisión)</span>
                @else
                    <span class="text-red-500">✗</span> {{ $nombre }} <span class="text-red-500">(Faltante)</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endisset

    {{-- Formulario solo si no hay expediente --}}
    @unless(isset($inscripcionExistente))

    @unless($gestionActiva)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-amber-700 text-sm">
        Las inscripciones no están habilitadas en este momento.
        Espera la apertura de la convocatoria.
    </div>
    @else

    <form method="POST" action="{{ route('postulante.expediente.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Errores globales --}}
        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                @foreach($errors->all() as $e)
                    <p class="text-red-600 text-sm">• {{ $e }}</p>
                @endforeach
            </div>
        @endif

        {{-- ── Datos personales ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">Datos personales</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                @foreach([
                    ['nombre',           'Nombre(s)',           'text',  $postulante->nombre,     true],
                    ['apellidos',        'Apellido(s)',          'text',  $postulante->apellidos,  true],
                    ['ci',               'Cédula de Identidad', 'text',  $postulante->ci,         true],
                    ['correo',           'Correo electrónico',  'email', Auth::user()->correo,    true],
                    ['fecha_nacimiento', 'Fecha de nacimiento', 'date',  $postulante->fecha_nacimiento?->format('Y-m-d'), true],
                    ['nroTelefono',      'Teléfono',            'text',  $postulante->nroTelefono, false],
                    ['ciudad',           'Ciudad',              'text',  $postulante->ciudad,      true],
                    ['colegio_procedencia','Colegio de procedencia','text',$postulante->colegio_procedencia, true],
                    ['direccion',        'Dirección',           'text',  $postulante->direccion,  false],
                ] as [$name, $label, $type, $val, $req])
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        {{ $label }} @if($req)<span class="text-red-500">*</span>@endif
                    </label>
                    <input type="{{ $type }}" name="{{ $name }}"
                           value="{{ old($name, $val) }}"
                           class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                                  {{ $errors->has($name) ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    @error($name)
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endforeach

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sexo <span class="text-red-500">*</span></label>
                    <select name="sexo"
                            class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                                   {{ $errors->has('sexo') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        <option value="">Seleccione...</option>
                        <option value="M" {{ old('sexo', $postulante->sexo) === 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sexo', $postulante->sexo) === 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                    @error('sexo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ── Opciones de carrera ─────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-1">Opciones de carrera</h3>
            <p class="text-xs text-gray-400 mb-4">Selecciona en orden de preferencia. La 2da opción es opcional.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">1ra opción <span class="text-red-500">*</span></label>
                    <select name="carrera_primera"
                            class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30
                                   {{ $errors->has('carrera_primera') ? 'border-red-400' : 'border-gray-300' }}">
                        <option value="">Seleccione carrera...</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera->codCarrera }}" {{ old('carrera_primera') == $carrera->codCarrera ? 'selected' : '' }}>
                                {{ $carrera->nombre }} ({{ $carrera->modalidad->nombModalidad ?? '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('carrera_primera')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">2da opción (opcional)</label>
                    <select name="carrera_segunda"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                        <option value="">Ninguna</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera->codCarrera }}" {{ old('carrera_segunda') == $carrera->codCarrera ? 'selected' : '' }}>
                                {{ $carrera->nombre }} ({{ $carrera->modalidad->nombModalidad ?? '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('carrera_segunda')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ── Documentos requeridos ───────────────────────────────────────── --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-1">Documentos requeridos</h3>
            <p class="text-xs text-gray-400 mb-4">
                Adjunta cada documento en formato <strong>PDF, JPG o PNG</strong> (máx. 5 MB por archivo).
            </p>

            <div class="space-y-4">
                @foreach([
                    ['doc_titulo_bachiller',         'Título de Bachiller'],
                    ['doc_libreta_escolar',          'Libreta Escolar'],
                    ['doc_cedula_identidad',         'Cédula de Identidad'],
                    ['doc_formulario_preinscripcion','Formulario de Preinscripción impreso'],
                ] as [$field, $label])
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $label }} <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="{{ $field }}" accept=".pdf,.jpg,.jpeg,.png"
                           class="block w-full text-sm text-gray-600
                                  file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                  file:text-xs file:font-semibold file:text-white file:cursor-pointer
                                  file:transition file:hover:opacity-90"
                           style="--file-bg: #283342;"
                           onchange="mostrarNombre(this, '{{ $field }}-nombre')">
                    <p id="{{ $field }}-nombre" class="text-xs text-[#283342] mt-1 hidden"></p>
                    @error($field)
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-3">
                <span class="text-red-500 font-bold">*</span> Todos los documentos son obligatorios para el registro del expediente.
            </p>
        </div>

        <button type="submit"
                class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90"
                style="background-color: #283342;">
            Guardar postulante
        </button>
    </form>

    @endunless
    @endunless

</div>
@endsection

@push('scripts')
<script>
function mostrarNombre(input, labelId) {
    const label = document.getElementById(labelId);
    if (input.files[0]) {
        label.textContent = '✓ ' + input.files[0].name;
        label.classList.remove('hidden');
    }
}

// Estilo del botón file input via CSS variable (fallback cross-browser)
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.style.setProperty('--tw-ring-color', '#283342');
});
</script>
<style>
input[type="file"]::file-selector-button {
    background-color: #283342;
    color: white;
    padding: 0.375rem 0.875rem;
    border-radius: 0.5rem;
    border: none;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.15s;
}
input[type="file"]::file-selector-button:hover {
    opacity: 0.85;
}
</style>
@endpush
