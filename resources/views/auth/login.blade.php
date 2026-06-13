<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FICCT – Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-7px); }
        }
        .badge-float-1 { animation: float 3.2s ease-in-out infinite; }
        .badge-float-2 { animation: float 3.8s ease-in-out infinite 0.6s; }
        .badge-float-3 { animation: float 4.4s ease-in-out infinite 1.2s; }
    </style>
</head>
<body class="min-h-screen bg-gray-100 flex flex-col">

    {{-- ── Top bar ───────────────────────────────────────────────────────── --}}
    <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
        <span class="font-semibold text-gray-800 text-sm tracking-wide">FICCT Portal</span>
        <div class="flex items-center gap-1 text-gray-400">
            <button class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </button>
            <button class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>
        </div>
    </header>

    {{-- ── Main ─────────────────────────────────────────────────────────── --}}
    <main class="flex-1 flex items-center justify-center p-4 sm:p-8">
        <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden flex" style="min-height: 540px;">

            {{-- ── LEFT: Formulario ─────────────────────────────────────── --}}
            <div class="w-full lg:w-1/2 px-10 py-10 flex flex-col justify-center">

                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Bienvenido de nuevo</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Accede a tu cuenta institucional de
                        <span class="font-semibold" style="color: #283342;">FICCT</span>.
                    </p>
                </div>

                {{-- Alertas --}}
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm space-y-0.5">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}" novalidate class="space-y-4">
                    @csrf

                    {{-- Correo --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">
                            Correo Institucional
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm pointer-events-none select-none">@</span>
                            <input
                                type="email" name="correo" id="correo"
                                value="{{ old('correo') }}"
                                autocomplete="email"
                                placeholder="nombre@uagrm.edu.bo"
                                class="w-full pl-7 pr-4 py-2.5 border rounded-lg text-sm text-gray-800 placeholder-gray-300 bg-gray-50 outline-none transition
                                       focus:bg-white focus:ring-2 focus:border-transparent
                                       {{ $errors->has('correo') ? 'border-red-400 bg-red-50 focus:ring-red-200' : 'border-gray-200 focus:ring-[#283342]/25' }}">
                        </div>
                    </div>

                    {{-- Contraseña --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Contraseña</label>
                            <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                type="password" id="password" name="password"
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full pl-10 pr-10 py-2.5 border rounded-lg text-sm text-gray-800 placeholder-gray-300 bg-gray-50 outline-none transition
                                       focus:bg-white focus:ring-2 focus:border-transparent
                                       {{ $errors->has('password') ? 'border-red-400 bg-red-50 focus:ring-red-200' : 'border-gray-200 focus:ring-[#283342]/25' }}">
                            <button type="button" id="togglePwd"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition">
                                <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Botón principal --}}
                    <button type="submit"
                            class="w-full py-2.5 rounded-lg text-white text-sm font-semibold tracking-wide
                                   transition hover:opacity-90 active:scale-[0.99] shadow-sm"
                            style="background-color: #283342;">
                        Iniciar Sesión
                    </button>
                </form>

            </div>

            {{-- ── RIGHT: Branding ───────────────────────────────────────── --}}
            <div class="hidden lg:flex lg:w-1/2 flex-col items-center justify-center relative overflow-hidden px-10 py-10"
                 style="background: linear-gradient(145deg, #1a2740 0%, #243350 50%, #1c3560 100%);">

                {{-- Glow orbs de fondo --}}
                <div class="absolute top-16 left-16 w-56 h-56 rounded-full pointer-events-none"
                     style="background: radial-gradient(circle, rgba(74,158,255,0.15), transparent); filter: blur(40px);"></div>
                <div class="absolute bottom-20 right-12 w-40 h-40 rounded-full pointer-events-none"
                     style="background: radial-gradient(circle, rgba(124,58,237,0.12), transparent); filter: blur(30px);"></div>

                {{-- Tarjeta con gráfico --}}
                <div class="relative w-52 h-52 mb-8">

                    {{-- Card principal --}}
                    <div class="w-full h-full rounded-2xl overflow-hidden shadow-2xl border border-white/10"
                         style="background: linear-gradient(145deg, #0d1b2e, #162540);">
                        <svg viewBox="0 0 200 200" class="w-full h-full">
                            <defs>
                                <radialGradient id="bgGlow" cx="50%" cy="50%" r="55%">
                                    <stop offset="0%"   stop-color="#3b82f6" stop-opacity="0.25"/>
                                    <stop offset="100%" stop-color="transparent" stop-opacity="0"/>
                                </radialGradient>
                                <filter id="blur2">
                                    <feGaussianBlur stdDeviation="2"/>
                                </filter>
                            </defs>

                            {{-- Fondo glow --}}
                            <circle cx="100" cy="100" r="85" fill="url(#bgGlow)"/>

                            {{-- Red exterior de nodos --}}
                            <line x1="100" y1="25"  x2="45"  y2="60"  stroke="#60a5fa" stroke-width="0.5" opacity="0.25"/>
                            <line x1="100" y1="25"  x2="155" y2="58"  stroke="#60a5fa" stroke-width="0.5" opacity="0.25"/>
                            <line x1="170" y1="95"  x2="155" y2="58"  stroke="#60a5fa" stroke-width="0.5" opacity="0.25"/>
                            <line x1="30"  y1="95"  x2="45"  y2="60"  stroke="#60a5fa" stroke-width="0.5" opacity="0.25"/>
                            <line x1="55"  y1="158" x2="45"  y2="60"  stroke="#60a5fa" stroke-width="0.5" opacity="0.18"/>
                            <line x1="145" y1="158" x2="155" y2="58"  stroke="#60a5fa" stroke-width="0.5" opacity="0.18"/>
                            <line x1="55"  y1="158" x2="30"  y2="95"  stroke="#60a5fa" stroke-width="0.5" opacity="0.18"/>
                            <line x1="145" y1="158" x2="170" y2="95"  stroke="#60a5fa" stroke-width="0.5" opacity="0.18"/>

                            {{-- Cristal central --}}
                            <polygon points="100,38 138,72 128,128 100,148 72,128 62,72"
                                     fill="none" stroke="#60a5fa" stroke-width="1" opacity="0.55"/>
                            {{-- Facetas internas --}}
                            <polygon points="100,38 138,72 100,100" fill="#1e40af" opacity="0.55"/>
                            <polygon points="100,38  62,72 100,100" fill="#1d4ed8" opacity="0.45"/>
                            <polygon points="138,72 128,128 100,100" fill="#2563eb" opacity="0.50"/>
                            <polygon points=" 62,72  72,128 100,100" fill="#1e40af" opacity="0.45"/>
                            <polygon points="128,128 100,148 100,100" fill="#3b82f6" opacity="0.45"/>
                            <polygon points=" 72,128 100,148 100,100" fill="#1d4ed8" opacity="0.40"/>
                            {{-- Líneas internas --}}
                            <line x1="100" y1="38"  x2="100" y2="100" stroke="#93c5fd" stroke-width="0.6" opacity="0.5"/>
                            <line x1="138" y1="72"  x2="100" y2="100" stroke="#93c5fd" stroke-width="0.6" opacity="0.5"/>
                            <line x1="62"  y1="72"  x2="100" y2="100" stroke="#93c5fd" stroke-width="0.6" opacity="0.5"/>
                            <line x1="128" y1="128" x2="100" y2="100" stroke="#93c5fd" stroke-width="0.6" opacity="0.5"/>
                            <line x1="72"  y1="128" x2="100" y2="100" stroke="#93c5fd" stroke-width="0.6" opacity="0.5"/>
                            <line x1="100" y1="148" x2="100" y2="100" stroke="#93c5fd" stroke-width="0.6" opacity="0.5"/>

                            {{-- Nodos --}}
                            <circle cx="100" cy="25"  r="3.5" fill="#bfdbfe"/>
                            <circle cx="100" cy="38"  r="2.5" fill="#93c5fd"/>
                            <circle cx="138" cy="72"  r="2.5" fill="#7dd3fc"/>
                            <circle cx="62"  cy="72"  r="2.5" fill="#7dd3fc"/>
                            <circle cx="128" cy="128" r="2.5" fill="#7dd3fc"/>
                            <circle cx="72"  cy="128" r="2.5" fill="#7dd3fc"/>
                            <circle cx="100" cy="148" r="2.5" fill="#93c5fd"/>
                            <circle cx="45"  cy="60"  r="1.8" fill="#60a5fa" opacity="0.7"/>
                            <circle cx="155" cy="58"  r="1.8" fill="#60a5fa" opacity="0.7"/>
                            <circle cx="170" cy="95"  r="1.8" fill="#60a5fa" opacity="0.7"/>
                            <circle cx="30"  cy="95"  r="1.8" fill="#60a5fa" opacity="0.7"/>
                            <circle cx="55"  cy="158" r="1.8" fill="#60a5fa" opacity="0.7"/>
                            <circle cx="145" cy="158" r="1.8" fill="#60a5fa" opacity="0.7"/>
                            {{-- Centro --}}
                            <circle cx="100" cy="100" r="4" fill="#dbeafe" opacity="0.85" filter="url(#blur2)"/>
                            <circle cx="100" cy="100" r="2.5" fill="#fff" opacity="0.9"/>
                        </svg>
                    </div>

                    {{-- Badges flotantes --}}
                    <div class="badge-float-1 absolute -top-4 -right-6 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg px-3 py-1.5 text-white text-xs font-mono font-semibold shadow-lg whitespace-nowrap">
                        &lt;Code/&gt;
                    </div>
                    <div class="badge-float-2 absolute top-1/3 -left-10 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg px-3 py-1.5 text-white text-xs font-medium shadow-lg whitespace-nowrap">
                        ✦ Network
                    </div>
                    <div class="badge-float-3 absolute -bottom-4 right-0 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg px-3 py-1.5 text-white text-xs font-medium shadow-lg whitespace-nowrap">
                        ✦ Secure
                    </div>
                </div>

                {{-- Texto --}}
                <div class="text-center space-y-2 max-w-xs">
                    <h2 class="text-xl font-bold text-white leading-snug">
                        Ingeniería que conecta al mundo
                    </h2>
                    <p class="text-white/55 text-sm leading-relaxed">
                        Accede a recursos académicos, Investigación y plataformas de telecomunicaciones de última generación.
                    </p>
                </div>
            </div>
        </div>
    </main>

    {{-- ── Footer ─────────────────────────────────────────────────────────── --}}
    <footer class="bg-white border-t border-gray-200 px-6 py-3 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-400">
        <span>
            © {{ date('Y') }}
            <a href="#" class="text-blue-600 hover:underline">
                Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones
            </a>
        </span>
        <div class="flex items-center gap-4">
            <a href="#" class="hover:text-gray-600 transition">Privacidad</a>
            <a href="#" class="hover:text-gray-600 transition">Términos de Uso</a>
            <a href="#" class="hover:text-gray-600 transition">Contacto Institucional</a>
        </div>
    </footer>

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
