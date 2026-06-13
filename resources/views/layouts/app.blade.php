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
    <aside class="w-64 h-screen sticky top-0 shrink-0 flex flex-col" style="background-color: #283342;">

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
        <nav class="px-4 py-4 flex flex-col gap-1 flex-1 overflow-y-auto">
            @auth
                @if(Auth::user()->esRolAdmin())
                    @php
                        $navAdmin = [
                            ['route' => 'admin.dashboard',          'label' => 'Dashboard',          'perm' => 'ver_dashboard',           'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                            ['route' => 'admin.docentes.index',     'label' => 'Docentes',           'perm' => 'ver_docentes',            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                            ['route' => 'admin.gestiones.index',    'label' => 'Gestiones',          'perm' => 'gestionar_grupos',        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['route' => 'admin.turnos.index',       'label' => 'Turnos',             'perm' => 'gestionar_grupos',        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['route' => 'admin.aulas.index',        'label' => 'Aulas',              'perm' => 'gestionar_grupos',        'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                            ['route' => 'admin.expedientes',        'label' => 'Expedientes',        'perm' => 'gestionar_inscripciones', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['route' => 'admin.estudiantes',        'label' => 'Postulantes',        'perm' => 'ver_postulantes',         'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                            ['route' => 'admin.importar-postulantes','label' => 'Carga Masiva',       'perm' => 'registrar_postulante',    'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12'],
                            ['route' => 'admin.grupos.index',       'label' => 'Grupos',             'perm' => 'ver_grupos',              'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                            ['route' => 'admin.asignacion-docente', 'label' => 'Asignación Docente', 'perm' => 'asignar_docente_grupo',   'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['route' => 'admin.solicitudes.index',  'label' => 'Solicitudes Materia','perm' => 'ver_solicitudes',         'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['route' => 'admin.requisitos.index',   'label' => 'Requisitos',         'perm' => 'gestionar_usuarios',      'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['route' => 'admin.roles.index',        'label' => 'Roles y Permisos',   'perm' => 'gestionar_usuarios',      'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                            ['route' => 'admin.admision.index',     'label' => 'Admisión',           'perm' => 'gestionar_inscripciones', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                            ['route' => 'admin.reportes.index',     'label' => 'Reportes',           'perm' => 'ver_reportes',            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ];
                        $authUser = Auth::user();
                    @endphp
                    @foreach($navAdmin as $item)
                    @if($authUser->hasPermission($item['perm']))
                    @php
                        $isActive = request()->routeIs($item['route'])
                            || ($item['route'] === 'admin.docentes.index'     && request()->routeIs('admin.docentes.*'))
                            || ($item['route'] === 'admin.gestiones.index'    && (request()->routeIs('admin.gestiones.*') || request()->routeIs('admin.gestiones.carreras.*')))
                            || ($item['route'] === 'admin.estudiantes'        && request()->routeIs('admin.postulantes.*'))
                            || ($item['route'] === 'admin.importar-postulantes' && request()->routeIs('admin.importar-postulantes.*'))
                            || ($item['route'] === 'admin.grupos.index'       && request()->routeIs('admin.grupos.*'))
                            || ($item['route'] === 'admin.solicitudes.index'  && request()->routeIs('admin.solicitudes.*'))
                            || ($item['route'] === 'admin.requisitos.index'   && request()->routeIs('admin.requisitos.*'))
                            || ($item['route'] === 'admin.aulas.index'        && request()->routeIs('admin.aulas.*'))
                            || ($item['route'] === 'admin.admision.index'     && request()->routeIs('admin.admision.*'))
                            || ($item['route'] === 'admin.reportes.index'     && request()->routeIs('admin.reportes.*'));
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-white/80 hover:bg-white/10 hover:text-white text-sm transition {{ $isActive ? 'bg-white/15 text-white font-medium' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                    @endif
                    @endforeach
                @elseif(Auth::user()->esDocente())
                    @php
                        $navDocente = [
                            ['route' => 'docente.dashboard',            'label' => 'Dashboard',        'perm' => 'ver_dashboard',       'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                            ['route' => 'docente.asistencia.index',     'label' => 'Tomar Asistencia', 'perm' => 'registrar_asistencia', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                            ['route' => 'docente.calificaciones.index', 'label' => 'Calificaciones',   'perm' => 'registrar_notas',     'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                            ['route' => 'docente.resultados.index',     'label' => 'Resultados',       'perm' => 'ver_notas',           'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ];
                        $authUser = Auth::user();
                    @endphp
                    @foreach($navDocente as $item)
                    @if($authUser->hasPermission($item['perm']))
                    @php
                        $isActive = request()->routeIs($item['route'])
                            || ($item['route'] === 'docente.asistencia.index'     && request()->routeIs('docente.asistencia.*'))
                            || ($item['route'] === 'docente.calificaciones.index' && request()->routeIs('docente.calificaciones.*'))
                            || ($item['route'] === 'docente.resultados.index'     && request()->routeIs('docente.resultados.*'));
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-white/80 hover:bg-white/10 hover:text-white text-sm transition {{ $isActive ? 'bg-white/15 text-white font-medium' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                    @endif
                    @endforeach
                @elseif(Auth::user()->esPostulante())
                    @php
                        $postulante = Auth::user()->postulante;
                        $pagoSidebar = $postulante?->pagos()->latest()->first();
                    @endphp

                    {{-- Estado de pago — siempre visible en sidebar --}}
                    <div class="mb-2 mx-1 px-3 py-2.5 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-white/50 text-xs uppercase tracking-wider mb-1.5">Estado de Pago</p>
                        @if(!$postulante || !$pagoSidebar)
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0 animate-pulse"></span>
                                <span class="text-amber-300 text-xs font-medium">Sin pago registrado</span>
                            </div>
                        @elseif(in_array($pagoSidebar->estado, ['pagado','aprobado']))
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-400 shrink-0"></span>
                                <span class="text-green-300 text-xs font-medium">
                                    Pagado &middot; Bs {{ number_format($pagoSidebar->monto, 2) }}
                                </span>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-400 shrink-0 animate-pulse"></span>
                                <span class="text-red-300 text-xs font-medium">{{ ucfirst($pagoSidebar->estado) }}</span>
                            </div>
                        @endif
                    </div>

                    @php
                        $navPostulante = [
                            ['route' => 'postulante.dashboard',  'label' => 'Mis Datos',       'perm' => 'ver_dashboard',        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                            ['route' => 'postulante.expediente', 'label' => 'Mi Inscripción',  'perm' => 'ver_inscripcion',      'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['route' => 'postulante.resultados', 'label' => 'Mis Notas',       'perm' => 'ver_resultado_propio', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ];
                        $authUser = Auth::user();
                    @endphp
                    @foreach($navPostulante as $item)
                    @if($authUser->hasPermission($item['perm']))
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-white/80 hover:bg-white/10 hover:text-white text-sm transition {{ request()->routeIs($item['route']) ? 'bg-white/15 text-white font-medium' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                    @endif
                    @endforeach
                @endif
            @endauth
        </nav>

        {{-- Cerrar sesión --}}
        <div class="shrink-0 px-4 py-4 border-t border-white/10">
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
