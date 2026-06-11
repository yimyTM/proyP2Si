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

            @if(session('error'))
                <div class="w-full p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
            @endif

            @if($yaPagado)
                {{-- Ya pagó --}}
                <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Inscripción pagada</h2>
                    <p class="text-gray-500 mt-2">Tu pago ya fue registrado. ¡Bienvenido!</p>
                </div>
                <a href="{{ route('postulante.dashboard') }}"
                   class="w-full text-center px-6 py-3 bg-[#001e40] hover:bg-[#003366] text-white font-semibold rounded-lg transition text-sm">
                    Ir a mi Panel
                </a>
            @else
                {{-- Ícono --}}
                <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-[#001e40]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Pago de inscripción</h2>
                    <p class="text-gray-500 mt-2 max-w-md leading-relaxed">
                        Completa el pago seguro con tarjeta para confirmar tu inscripción.
                    </p>
                </div>

                {{-- Resumen --}}
                <div class="w-full bg-gray-50 rounded-xl border border-gray-200 p-5 text-left space-y-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Resumen</p>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Postulante</span>
                        <span class="font-semibold text-gray-800">{{ $postulante->nombre }} {{ $postulante->apellidos }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">CI</span>
                        <span class="font-semibold text-gray-800">{{ $postulante->ci }}</span>
                    </div>
                    @if($gestion)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Gestión</span>
                        <span class="font-semibold text-gray-800">{{ $gestion->nombre }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm border-t border-gray-200 pt-3">
                        <span class="text-gray-500">Monto a pagar</span>
                        <span class="font-bold text-[#001e40] text-lg">{{ number_format($monto, 2) }} {{ $moneda }}</span>
                    </div>
                </div>

                {{-- Botón de pago Stripe --}}
                <form method="POST" action="{{ route('registro.pago.checkout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-[#635bff] hover:bg-[#5147e6] text-white font-semibold rounded-lg transition text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13.5 9.5c0-.6.5-.9 1.3-.9 1.2 0 2.6.4 3.7 1V6.1c-1.3-.5-2.5-.7-3.7-.7-3 0-5 1.6-5 4.2 0 4.1 5.6 3.5 5.6 5.3 0 .7-.6 1-1.5 1-1.3 0-3-.5-4.3-1.3v3.5c1.4.6 2.9.9 4.3.9 3.1 0 5.2-1.5 5.2-4.2 0-4.4-5.6-3.7-5.6-5.5z"/>
                        </svg>
                        Pagar {{ number_format($monto, 2) }} {{ $moneda }} con Stripe
                    </button>
                </form>

                <p class="text-xs text-gray-400">
                    Pago seguro procesado por Stripe. Modo de prueba: usa la tarjeta
                    <span class="font-mono bg-gray-100 px-1 rounded">4242 4242 4242 4242</span>, cualquier fecha futura y CVC.
                </p>

                <a href="{{ route('postulante.dashboard') }}" class="text-sm text-gray-500 hover:underline">
                    Pagar más tarde — ir a mi panel
                </a>
            @endif
        </div>
    </div>
</div>

</body>
</html>
