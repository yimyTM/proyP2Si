@extends('layouts.app')

@section('title', 'Mi Expediente Digital')
@section('page-title', 'CU04 – Expediente Digital')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Alertas --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Si ya completó el expediente, mostrar resumen --}}
    @isset($inscripcionExistente)
    <div class="bg-green-50 border border-green-200 rounded-xl p-5">
        <div class="flex items-center gap-3 mb-3">
            <span class="text-green-600 text-xl">✓</span>
            <h3 class="font-semibold text-green-800">Expediente registrado</h3>
        </div>
        <p class="text-sm text-green-700">
            Estado: <strong>{{ ucfirst($inscripcionExistente->estado) }}</strong> |
            Fecha: {{ $inscripcionExistente->fecha->format('d/m/Y') }}
        </p>
        <ul class="mt-2 space-y-1">
            @foreach($inscripcionExistente->carrerasInscritas->sortBy('prioridad') as $ci)
            <li class="text-sm text-green-700">
                Opción {{ $ci->prioridad }}: <strong>{{ $ci->carrera->nombre }}</strong>
            </li>
            @endforeach
        </ul>
        @if($tituloEntregado)
        <p class="text-xs text-green-600 mt-2">
            Título de Bachiller: {{ $tituloEntregado->validado ? '✓ Validado' : 'En revisión' }}
        </p>
        @endif
    </div>
    @endisset

    @unless(isset($inscripcionExistente))

    @unless($gestionActiva)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-amber-700 text-sm">
        No hay ningún período de admisión activo en este momento. Espera la apertura de la convocatoria.
    </div>
    @else

    <form method="POST" action="{{ route('postulante.expediente.store') }}">
        @csrf

        {{-- Datos personales --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">Datos personales</h3>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    @foreach($errors->all() as $e)
                        <p class="text-red-600 text-sm">• {{ $e }}</p>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach([
                    ['nombre',           'Nombre(s)',          'text',  $postulante->nombre],
                    ['apellidos',        'Apellido(s)',         'text',  $postulante->apellidos],
                    ['ci',               'Cédula de Identidad','text',  $postulante->ci],
                    ['fecha_nacimiento', 'Fecha de nacimiento','date',  $postulante->fecha_nacimiento?->format('Y-m-d')],
                    ['nroTelefono',      'Teléfono',           'text',  $postulante->nroTelefono],
                    ['ciudad',           'Ciudad',             'text',  $postulante->ciudad],
                    ['colegio_procedencia','Colegio de procedencia','text',$postulante->colegio_procedencia],
                    ['direccion',        'Dirección',          'text',  $postulante->direccion],
                ] as [$name, $label, $type, $val])
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                    <input type="{{ $type }}" name="{{ $name }}"
                           value="{{ old($name, $val) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                </div>
                @endforeach

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sexo</label>
                    <select name="sexo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                        <option value="">Seleccione...</option>
                        <option value="M" {{ old('sexo', $postulante->sexo) === 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sexo', $postulante->sexo) === 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Opciones de carrera --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mt-4">
            <h3 class="font-semibold text-gray-800 mb-1">Opciones de carrera</h3>
            <p class="text-xs text-gray-400 mb-4">Selecciona en orden de preferencia. La 2da opción es opcional.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">1ra opción <span class="text-red-500">*</span></label>
                    <select name="carrera_primera" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                        <option value="">Seleccione carrera...</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera->codCarrera }}" {{ old('carrera_primera') == $carrera->codCarrera ? 'selected' : '' }}>
                                {{ $carrera->nombre }} ({{ $carrera->modalidad->nombModalidad ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">2da opción (opcional)</label>
                    <select name="carrera_segunda" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#283342]/30">
                        <option value="">Ninguna</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera->codCarrera }}" {{ old('carrera_segunda') == $carrera->codCarrera ? 'selected' : '' }}>
                                {{ $carrera->nombre }} ({{ $carrera->modalidad->nombModalidad ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Título de Bachiller --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mt-4">
            <h3 class="font-semibold text-gray-800 mb-1">Título de Bachiller</h3>
            <p class="text-xs text-gray-400 mb-4">Confirma que dispones del documento original para presentarlo presencialmente.</p>

            @error('titulo_confirmado')
                <p class="text-red-600 text-sm mb-3">• {{ $message }}</p>
            @enderror

            <label class="flex items-center gap-3 cursor-pointer select-none">
                <input type="checkbox" name="titulo_confirmado" value="1"
                       {{ old('titulo_confirmado') ? 'checked' : '' }}
                       class="w-5 h-5 rounded border-gray-300 text-[#283342] focus:ring-[#283342]">
                <span class="text-sm text-gray-700">Confirmo que tengo el <strong>Título de Bachiller</strong> original y lo presentaré cuando sea requerido.</span>
            </label>
        </div>

        <button type="submit"
                class="w-full mt-4 py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90"
                style="background-color: #283342;">
            Guardar expediente
        </button>
    </form>

    @endunless
    @endunless

</div>
@endsection
