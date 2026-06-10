<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Inscripción – Paso 3 | FICCT</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<script>
    tailwind.config = {
        theme: { extend: {
            colors: { 'ficct': '#001e40', 'ficct-c': '#003366' },
            fontFamily: { sans: ['Inter','sans-serif'] }
        }}
    }
</script>
<style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen">

{{-- Navbar --}}
<nav class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <span class="font-bold text-[#001e40] text-lg">FICCT Portal</span>
    <span class="text-sm text-gray-500">{{ Auth::user()->nombreCompleto }}</span>
</nav>

{{-- Wizard steps --}}
<div class="max-w-3xl mx-auto px-4 pt-8 pb-4">
    <div class="flex items-center gap-0">
        @foreach([['1','Datos personales',true],['2','Documentos',true],['3','Pago',false]] as [$n,$label,$done])
        <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                    {{ $done ? 'bg-green-500 text-white' : 'bg-[#001e40] text-white' }}">
                    @if($done)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    @else
                    {{ $n }}
                    @endif
                </div>
                <span class="text-sm font-medium {{ $done ? 'text-green-600' : 'text-[#001e40]' }} hidden sm:block">{{ $label }}</span>
            </div>
            @if(!$loop->last)
            <div class="flex-1 h-0.5 mx-3 {{ $done ? 'bg-green-400' : 'bg-gray-200' }}"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- Contenido --}}
<div class="max-w-3xl mx-auto px-4 pb-12">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #001e40 0%, #003366 100%);">
            <h1 class="text-xl font-bold text-white">Pago de matrícula</h1>
            <p class="text-white/60 text-sm mt-1">Último paso para confirmar tu inscripción.</p>
        </div>

        <div class="p-8 flex flex-col items-center text-center gap-6">
            {{-- Ícono --}}
            <div class="w-20 h-20 rounded-full bg-amber-100 flex items-center justify-center">
                <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-800">Módulo de pago en desarrollo</h2>
                <p class="text-gray-500 mt-2 max-w-md leading-relaxed">
                    El módulo de pago estará disponible próximamente. Tu inscripción ha sido registrada correctamente con estado <strong class="text-amber-600">Pendiente</strong>.
                </p>
            </div>

            {{-- Resumen del postulante --}}
            <div class="w-full bg-gray-50 rounded-xl border border-gray-200 p-5 text-left space-y-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Resumen de inscripción</p>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Postulante</span>
                    <span class="font-semibold text-gray-800">{{ $postulante->nombre }} {{ $postulante->apellidos }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">CI</span>
                    <span class="font-semibold text-gray-800">{{ $postulante->ci }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Estado</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Pendiente de pago
                    </span>
                </div>
            </div>

            {{-- Info de pago --}}
            <div class="w-full bg-blue-50 border border-blue-200 rounded-xl p-5 text-left">
                <p class="text-sm font-semibold text-blue-800 mb-2">¿Cómo pagar?</p>
                <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                    <li>Transfiere a la cuenta institucional FICCT</li>
                    <li>Sube el comprobante en el módulo "Verificar Pago"</li>
                    <li>El administrador validará tu pago en 24–48 horas</li>
                </ul>
            </div>

            {{-- Acciones --}}
            <div class="flex flex-col sm:flex-row gap-3 w-full">
                <a href="{{ route('postulante.dashboard') }}"
                   class="flex-1 text-center px-6 py-3 bg-[#001e40] hover:bg-[#003366] text-white font-semibold rounded-lg transition text-sm">
                    Ir a mi Panel
                </a>
                <a href="{{ route('verificar-pago') }}"
                   class="flex-1 text-center px-6 py-3 border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition text-sm">
                    Verificar Pago
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
