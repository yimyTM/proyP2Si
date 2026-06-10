<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Inscripción – Paso 1 | FICCT</title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<script>
    tailwind.config = {
        theme: { extend: {
            colors: { 'ficct': '#001e40', 'ficct-c': '#003366', 'ficct-a': '#799dd6' },
            fontFamily: { sans: ['Inter','sans-serif'] }
        }}
    }
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .field-label { @apply block text-sm font-medium text-gray-700 mb-1; }
    .field-input { @apply w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-[#001e40] focus:ring-1 focus:ring-[#001e40] outline-none transition; }
    .field-error { @apply text-xs text-red-600 mt-1; }
</style>
</head>
<body class="bg-gray-50 min-h-screen">

{{-- Navbar mínimo --}}
<nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <a href="{{ route('home') }}" class="flex items-center gap-2 text-[#001e40] font-bold text-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        FICCT Portal
    </a>
    <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-[#001e40] transition">¿Ya tienes cuenta? Inicia sesión</a>
</nav>

{{-- Wizard steps --}}
<div class="max-w-3xl mx-auto px-4 pt-8 pb-4">
    <div class="flex items-center gap-0">
        @foreach([['1','Datos personales',true],['2','Documentos',false],['3','Pago',false]] as [$n,$label,$active])
        <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $active ? 'bg-[#001e40] text-white' : 'bg-gray-200 text-gray-400' }}">
                    {{ $n }}
                </div>
                <span class="text-sm font-medium {{ $active ? 'text-[#001e40]' : 'text-gray-400' }} hidden sm:block">{{ $label }}</span>
            </div>
            @if(!$loop->last)
            <div class="flex-1 h-0.5 mx-3 {{ $active ? 'bg-gray-200' : 'bg-gray-200' }}"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- Formulario --}}
<div class="max-w-3xl mx-auto px-4 pb-12">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #001e40 0%, #003366 100%);">
            <h1 class="text-xl font-bold text-white">Datos personales</h1>
            <p class="text-white/60 text-sm mt-1">Completa tu información para crear tu cuenta de postulante.</p>
            @if($gestion)
            <span class="inline-flex items-center gap-1.5 mt-2 text-xs font-medium bg-white/10 text-white/80 px-2.5 py-1 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                Gestión activa: {{ $gestion->nombre }}
            </span>
            @else
            <span class="inline-block mt-2 text-xs font-medium bg-yellow-400/20 text-yellow-200 px-2.5 py-1 rounded-full">
                Sin gestión activa — igual puedes registrarte
            </span>
            @endif
        </div>

        <form method="POST" action="{{ route('registro.store') }}" class="p-6 space-y-6">
            @csrf

            {{-- Errores globales --}}
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Sección: Datos personales --}}
            <div class="space-y-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Datos personales</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Nombre(s) <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Diego Ramiro"
                               class="field-input @error('nombre') border-red-400 @enderror"/>
                        @error('nombre') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">Apellidos <span class="text-red-500">*</span></label>
                        <input type="text" name="apellidos" value="{{ old('apellidos') }}" placeholder="Ej: Flores Aguilar"
                               class="field-input @error('apellidos') border-red-400 @enderror"/>
                        @error('apellidos') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="field-label">Carnet de Identidad <span class="text-red-500">*</span></label>
                        <input type="text" name="ci" value="{{ old('ci') }}" placeholder="Ej: 9876543"
                               class="field-input @error('ci') border-red-400 @enderror"/>
                        @error('ci') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
                               class="field-input"/>
                    </div>
                    <div>
                        <label class="field-label">Sexo</label>
                        <select name="sexo" class="field-input">
                            <option value="">-- Seleccionar --</option>
                            <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Sección: Contacto y ubicación --}}
            <div class="border-t border-gray-100 pt-5 space-y-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Contacto y ubicación</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Correo electrónico <span class="text-red-500">*</span></label>
                        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="tu@correo.com"
                               class="field-input @error('correo') border-red-400 @enderror"/>
                        @error('correo') <p class="field-error">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-400 mt-1">Será tu usuario para iniciar sesión.</p>
                    </div>
                    <div>
                        <label class="field-label">Teléfono / Celular</label>
                        <input type="text" name="nroTelefono" value="{{ old('nroTelefono') }}" placeholder="Ej: 70000000"
                               class="field-input"/>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Ciudad</label>
                        <input type="text" name="ciudad" value="{{ old('ciudad') }}" placeholder="Ej: Santa Cruz de la Sierra"
                               class="field-input"/>
                    </div>
                    <div>
                        <label class="field-label">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion') }}" placeholder="Av. / Calle..."
                               class="field-input"/>
                    </div>
                </div>

                <div>
                    <label class="field-label">Colegio de procedencia</label>
                    <input type="text" name="colegio_procedencia" value="{{ old('colegio_procedencia') }}"
                           placeholder="Nombre del colegio donde terminaste el bachillerato"
                           class="field-input"/>
                </div>
            </div>

            {{-- Sección: Contraseña --}}
            <div class="border-t border-gray-100 pt-5 space-y-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Contraseña de acceso</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">Contraseña <span class="text-red-500">*</span></label>
                        <input type="password" name="password" placeholder="Mínimo 8 caracteres"
                               class="field-input @error('password') border-red-400 @enderror"/>
                        @error('password') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">Confirmar contraseña <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" placeholder="Repite tu contraseña"
                               class="field-input"/>
                    </div>
                </div>
            </div>

            {{-- Preferencias de carrera --}}
            <div class="border-t border-gray-100 pt-6">
                <h3 class="text-base font-semibold text-gray-800 mb-1">Preferencias de carrera</h3>
                <p class="text-sm text-gray-500 mb-4">Selecciona tus dos opciones en orden de preferencia. Debes elegir opciones distintas.</p>

                @if($carreras->isEmpty())
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-sm text-amber-700">
                    No hay carreras disponibles en este momento. Puedes completar el registro y seleccionarlas más tarde.
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="field-label">1ª Opción <span class="text-red-500">*</span></label>
                        <select name="carrera_opcion1" class="field-input @error('carrera_opcion1') border-red-400 @enderror">
                            <option value="">-- Seleccionar carrera --</option>
                            @foreach($carreras as $carrera)
                            <option value="{{ $carrera->codCarrera }}" {{ old('carrera_opcion1') == $carrera->codCarrera ? 'selected' : '' }}>
                                {{ $carrera->nombre }}
                                @if($carrera->modalidad) ({{ $carrera->modalidad->nombModalidad ?? '' }}) @endif
                            </option>
                            @endforeach
                        </select>
                        @error('carrera_opcion1') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label">2ª Opción <span class="text-red-500">*</span></label>
                        <select name="carrera_opcion2" class="field-input @error('carrera_opcion2') border-red-400 @enderror">
                            <option value="">-- Seleccionar carrera --</option>
                            @foreach($carreras as $carrera)
                            <option value="{{ $carrera->codCarrera }}" {{ old('carrera_opcion2') == $carrera->codCarrera ? 'selected' : '' }}>
                                {{ $carrera->nombre }}
                                @if($carrera->modalidad) ({{ $carrera->modalidad->nombModalidad ?? '' }}) @endif
                            </option>
                            @endforeach
                        </select>
                        @error('carrera_opcion2') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif
            </div>

            {{-- Botón submit --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                    ← Volver al inicio
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-[#001e40] hover:bg-[#003366] text-white font-semibold rounded-lg transition shadow-sm text-sm">
                    Continuar
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
