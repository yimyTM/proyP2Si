<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FICCT – Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#283342',
                    },
                    keyframes: {
                        'fade-up': {
                            '0%': { opacity: '0', transform: 'translateY(16px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    },
                    animation: {
                        'fade-up': 'fade-up 0.5s ease-out both',
                    }
                }
            }
        }
    </script>
    <style>
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
    </style>
</head>
<body class="min-h-screen flex">

    {{-- ══════════════════════════════════════════
         COLUMNA IZQUIERDA – Bienvenida / Branding
         ══════════════════════════════════════════ --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between px-16 py-14 relative overflow-hidden"
         style="background-color: #283342;">

        {{-- Patrón decorativo de fondo --}}
        <div class="absolute inset-0 opacity-5" style="
            background-image: radial-gradient(circle at 20% 80%, #fff 1px, transparent 1px),
                              radial-gradient(circle at 80% 20%, #fff 1px, transparent 1px),
                              radial-gradient(circle at 40% 40%, #fff 1px, transparent 1px);
            background-size: 60px 60px;">
        </div>

        {{-- Logo superior --}}
        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center shadow-lg">
                <span class="text-white font-extrabold text-xl tracking-tighter">FC</span>
            </div>
            <div>
                <p class="text-white font-bold text-lg leading-tight">FICCT</p>
                <p class="text-white/50 text-xs">UAGRM</p>
            </div>
        </div>

        {{-- Contenido central --}}
        <div class="relative space-y-8">
            <div>
                <h1 class="text-4xl font-extrabold text-white leading-snug">
                    Sistema de<br>
                    <span class="text-white/60">Admisión</span><br>
                    Académica
                </h1>
                <p class="mt-4 text-white/60 text-sm leading-relaxed max-w-xs">
                    Gestión integral del proceso de admisión de la Facultad de Ingeniería en
                    Ciencias de la Computación y Telecomunicaciones.
                </p>
            </div>

            {{-- Características --}}
            <div class="space-y-4">
                @foreach([
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                      'title' => 'Acceso seguro por roles', 'desc' => 'Administrador, Docente y Postulante'],
                    ['icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                      'title' => 'Gestión de expedientes', 'desc' => 'Documentación digital del postulante'],
                    ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                      'title' => 'Apertura automática de grupos', 'desc' => 'Algoritmo de distribución equitativa'],
                ] as $feat)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feat['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white text-sm font-medium">{{ $feat['title'] }}</p>
                        <p class="text-white/40 text-xs mt-0.5">{{ $feat['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pie --}}
        <div class="relative">
            <p class="text-white/25 text-xs">
                © {{ date('Y') }} Universidad Autónoma Gabriel René Moreno
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         COLUMNA DERECHA – Formulario de login
         ══════════════════════════════════════════ --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 px-8 py-14">

        <div class="w-full max-w-sm">

            {{-- Logo (solo visible en móvil, la izquierda está oculta) --}}
            <div class="flex lg:hidden items-center gap-3 mb-8 justify-center">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #283342;">
                    <span class="text-white font-bold text-sm">FC</span>
                </div>
                <span class="text-gray-800 font-bold">FICCT</span>
            </div>

            {{-- Encabezado --}}
            <div class="mb-8 animate-fade-up">
                <h2 class="text-2xl font-bold text-gray-900">Bienvenido</h2>
                <p class="text-gray-400 text-sm mt-1">Ingresa tus credenciales para acceder al sistema</p>
            </div>

            {{-- Alertas --}}
            @if(session('success'))
                <div class="mb-5 p-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm animate-fade-up">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3 bg-red-50 border border-red-200 rounded-xl animate-fade-up">
                    @foreach($errors->all() as $error)
                        <p class="text-red-600 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Formulario --}}
            <form method="POST" action="{{ route('login.submit') }}" novalidate class="space-y-5 animate-fade-up delay-100">
                @csrf

                {{-- Correo --}}
                <div>
                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Correo electrónico
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input
                            type="email" id="correo" name="correo"
                            value="{{ old('correo') }}"
                            autocomplete="email"
                            placeholder="usuario@ejemplo.com"
                            class="w-full pl-10 pr-4 py-2.5 border rounded-xl text-sm text-gray-800 placeholder-gray-400 bg-white outline-none transition
                                   focus:ring-2 focus:border-transparent
                                   {{ $errors->has('correo') ? 'border-red-400 bg-red-50 focus:ring-red-200' : 'border-gray-200 focus:ring-[#283342]/20 hover:border-gray-300' }}"
                        >
                    </div>
                </div>

                {{-- Contraseña --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Contraseña
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            type="password" id="password" name="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full pl-10 pr-12 py-2.5 border rounded-xl text-sm text-gray-800 placeholder-gray-400 bg-white outline-none transition
                                   focus:ring-2 focus:border-transparent
                                   {{ $errors->has('password') ? 'border-red-400 bg-red-50 focus:ring-red-200' : 'border-gray-200 focus:ring-[#283342]/20 hover:border-gray-300' }}"
                        >
                        {{-- Toggle mostrar/ocultar contraseña --}}
                        <button type="button" id="togglePwd"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition">
                            <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Recordarme / Olvidé contraseña --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                               class="w-4 h-4 rounded border-gray-300 accent-[#283342]">
                        <label for="remember" class="ml-2 text-sm text-gray-600 select-none cursor-pointer">
                            Recordar mi sesión
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}"
                       class="text-sm font-medium hover:underline"
                       style="color: #283342;">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                {{-- Botón de ingreso --}}
                <button type="submit"
                        class="w-full py-3 rounded-xl text-white font-semibold text-sm tracking-wide
                               transition hover:opacity-90 active:scale-[0.99] shadow-md hover:shadow-lg"
                        style="background-color: #283342;">
                    Ingresar al Sistema
                </button>
            </form>

            {{-- Acceso alternativo --}}
            <div class="mt-6 text-center animate-fade-up delay-200">
                <p class="text-xs text-gray-400">
                    ¿Eres postulante y quieres verificar tu pago?
                </p>
                <a href="{{ route('verificar-pago') }}"
                   class="text-xs font-medium hover:underline mt-0.5 inline-block"
                   style="color: #283342;">
                    Consultar estado de pago →
                </a>
            </div>

            {{-- Nota de cuentas --}}
            <p class="mt-8 text-center text-xs text-gray-300 animate-fade-up delay-300">
                Las cuentas son gestionadas por el administrador del sistema.
            </p>

        </div>
    </div>

    <script>
        // Toggle mostrar/ocultar contraseña
        const btn = document.getElementById('togglePwd');
        const pwd = document.getElementById('password');
        const ico = document.getElementById('eyeIcon');
        const pathOff = 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21';
        const pathOn  = 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z';

        btn.addEventListener('click', () => {
            const show = pwd.type === 'password';
            pwd.type = show ? 'text' : 'password';
            ico.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${show ? pathOff : pathOn}"/>`;
        });
    </script>
</body>
</html>
