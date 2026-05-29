<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FICCT – Sistema de Admisión')</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:   '#283342',
                        secondary: '#C8CBD0',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen flex">

    {{-- ── Sidebar ────────────────────────────────────────────────────── --}}
    <aside class="w-64 min-h-screen shrink-0" style="background-color: #283342;">

        {{-- Logo / Título --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                <span class="text-white font-bold text-sm">FC</span>
            </div>
            <div>
                <p class="text-white font-bold text-sm leading-tight">FICCT</p>
                <p class="text-white/60 text-xs">Sistema de Admisión</p>
            </div>
        </div>

        {{-- Nombre del usuario --}}
        <div class="px-6 py-4 border-b border-white/10">
            <p class="text-white/80 text-xs uppercase tracking-wider mb-1">Usuario</p>
            <p class="text-white font-medium text-sm truncate">{{ Auth::user()->nombreCompleto }}</p>
            <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full bg-white/10 text-white/70">
                {{ Auth::user()->rol?->nombre_Rol ?? 'Sin rol' }}
            </span>
        </div>

        {{-- Navegación --}}
        <nav class="px-4 py-4 flex flex-col gap-1">
            @auth
                @if(Auth::user()->esAdmin())
                    @php
                        $navAdmin = [
                            ['route' => 'admin.dashboard',         'label' => 'Dashboard',               'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                            ['route' => 'admin.docentes.index',    'label' => 'Docentes',                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                            ['route' => 'admin.gestiones.index',   'label' => 'Gestiones',               'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['route' => 'admin.turnos.index',      'label' => 'Turnos',                  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['route' => 'admin.expedientes',       'label' => 'Expedientes',             'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['route' => 'admin.estudiantes',       'label' => 'Postulantes',             'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                            ['route' => 'admin.grupos.index',      'label' => 'Grupos',                  'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                            ['route' => 'admin.asignacion-docente','label' => 'Asignación Docente',      'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['route' => 'admin.requisitos.index',  'label' => 'Requisitos',               'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['route' => 'admin.roles.index',       'label' => 'Roles y Permisos',         'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                        ];
                    @endphp
                    @foreach($navAdmin as $item)
                    @php
                        $isActive = request()->routeIs($item['route'])
                            || ($item['route'] === 'admin.docentes.index' && request()->routeIs('admin.docentes.*'))
                            || ($item['route'] === 'admin.gestiones.index' && (request()->routeIs('admin.gestiones.*') || request()->routeIs('admin.gestiones.carreras.*')))
                            || ($item['route'] === 'admin.estudiantes' && request()->routeIs('admin.postulantes.*'))
                            || ($item['route'] === 'admin.grupos.index' && request()->routeIs('admin.grupos.*'))
                            || ($item['route'] === 'admin.requisitos.index' && request()->routeIs('admin.requisitos.*'));
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-white/80 hover:bg-white/10 hover:text-white text-sm transition {{ $isActive ? 'bg-white/15 text-white font-medium' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                    @endforeach
                @elseif(Auth::user()->esDocente())
                    @php
                        $navDocente = [
                            ['route' => 'docente.dashboard',        'label' => 'Dashboard',         'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                            ['route' => 'docente.asistencia.index', 'label' => 'Tomar Asistencia',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ];
                    @endphp
                    @foreach($navDocente as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-white/80 hover:bg-white/10 hover:text-white text-sm transition
                              {{ request()->routeIs($item['route']) || ($item['route'] === 'docente.asistencia.index' && request()->routeIs('docente.asistencia.*')) ? 'bg-white/15 text-white font-medium' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                    @endforeach
                @elseif(Auth::user()->esPostulante())
                    <a href="{{ route('postulante.dashboard') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-white/80 hover:bg-white/10 hover:text-white text-sm transition {{ request()->routeIs('postulante.dashboard') ? 'bg-white/15 text-white font-medium' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                @endif
            @endauth
        </nav>

        {{-- Cerrar sesión --}}
        <div class="absolute bottom-0 left-0 w-64 px-4 py-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-white/70 hover:bg-red-500/20 hover:text-red-300 text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Contenido principal ────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-h-screen">

        {{-- Header --}}
        <header class="bg-white shadow-sm px-8 py-4 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Panel Principal')</h1>
            <p class="text-sm text-gray-500">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
        </header>

        {{-- Alertas flash --}}
        @if(session('success'))
            <div class="mx-8 mt-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-8 mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Contenido de cada vista --}}
        <main class="flex-1 px-8 py-6">
            @yield('content')
        </main>

        <footer class="px-8 py-3 text-center text-xs text-gray-400 border-t">
            FICCT – Universidad Autónoma Gabriel René Moreno &copy; {{ date('Y') }}
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
