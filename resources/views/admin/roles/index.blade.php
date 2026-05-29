@extends('layouts.app')

@section('title', 'Roles y Permisos')
@section('page-title', 'Gestión de Roles y Permisos')

@section('content')
<div class="space-y-6">

    {{-- Aviso informativo ─────────────────────────────────────────────────── --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-700">
            <p class="font-semibold mb-0.5">Permisos predefinidos del sistema</p>
            <p class="text-blue-600">
                Los permisos disponibles están definidos por el equipo de desarrollo.
                Aquí solo puedes <strong>asignar o quitar</strong> permisos a cada rol.
                Los cambios se aplican inmediatamente al guardar.
            </p>
        </div>
    </div>

    {{-- Alertas flash ─────────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Tarjetas de roles (tabs) ──────────────────────────────────────────── --}}
    @php
        $rolColors = [
            'Administrador' => ['bg' => '#283342', 'light' => '#EEF0F2', 'text' => '#283342'],
            'Docente'       => ['bg' => '#7C3AED', 'light' => '#F5F3FF', 'text' => '#6D28D9'],
            'Postulante'    => ['bg' => '#2563EB', 'light' => '#EFF6FF', 'text' => '#1D4ED8'],
        ];
        $rolIcons = [
            'Administrador' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'Docente'       => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
            'Postulante'    => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        ];
        $rolActivo = session('rolActivo', $roles->first()?->idRol);
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($roles as $rol)
        @php
            $color  = $rolColors[$rol->nombre_Rol] ?? $rolColors['Administrador'];
            $icon   = $rolIcons[$rol->nombre_Rol]  ?? $rolIcons['Administrador'];
            $activo = $rolActivo == $rol->idRol;
            $pct    = $totalPermisos > 0 ? round(($rol->permisos->count() / $totalPermisos) * 100) : 0;
        @endphp
        <button
            onclick="activarTab({{ $rol->idRol }})"
            id="tab-btn-{{ $rol->idRol }}"
            class="text-left rounded-2xl p-5 border-2 transition-all duration-200 focus:outline-none
                   {{ $activo ? 'shadow-lg scale-[1.02]' : 'bg-white border-gray-100 hover:border-gray-300 hover:shadow-sm' }}"
            style="{{ $activo ? "background-color:{$color['light']}; border-color:{$color['bg']};" : '' }}"
        >
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background-color: {{ $color['bg'] }}">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                    </svg>
                </div>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                      style="background-color:{{ $color['light'] }}; color:{{ $color['text'] }};">
                    {{ $rol->users_count }} usuario{{ $rol->users_count !== 1 ? 's' : '' }}
                </span>
            </div>
            <p class="font-bold text-gray-900 text-base">{{ $rol->nombre_Rol }}</p>
            <p class="text-xs text-gray-400 mt-0.5 mb-3">{{ $rol->permisos->count() }} de {{ $totalPermisos }} permisos</p>
            {{-- Barra de progreso --}}
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full transition-all"
                     style="width: {{ $pct }}%; background-color: {{ $color['bg'] }};"></div>
            </div>
        </button>
        @endforeach
    </div>

    {{-- Panel de permisos por rol (tabs) ────────────────────────────────────── --}}
    @foreach($roles as $rol)
    @php $color = $rolColors[$rol->nombre_Rol] ?? $rolColors['Administrador']; @endphp
    <div id="tab-{{ $rol->idRol }}"
         class="tab-panel {{ $rolActivo == $rol->idRol ? '' : 'hidden' }}">

        <form method="POST"
              action="{{ route('admin.roles.permisos.update', $rol->idRol) }}">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Header del panel --}}
                <div class="px-6 py-4 border-b flex items-center justify-between"
                     style="border-left: 4px solid {{ $color['bg'] }};">
                    <div>
                        <h3 class="font-bold text-gray-900">Permisos — {{ $rol->nombre_Rol }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Selecciona los permisos que tendrá este rol. Los cambios se aplican al guardar.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Seleccionar / Deseleccionar todos --}}
                        <button type="button"
                                onclick="toggleTodos({{ $rol->idRol }}, true)"
                                class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                            Marcar todos
                        </button>
                        <button type="button"
                                onclick="toggleTodos({{ $rol->idRol }}, false)"
                                class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                            Desmarcar todos
                        </button>
                    </div>
                </div>

                {{-- Grid de permisos por categoría --}}
                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($permisosPorCategoria as $categoria => $permisosDeCategoria)
                    <div class="space-y-2">
                        {{-- Encabezado de categoría --}}
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-1.5 h-4 rounded-full" style="background-color: {{ $color['bg'] }};"></div>
                            <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">{{ $categoria }}</p>
                        </div>

                        @foreach($permisosDeCategoria as $permiso)
                        @php $asignado = $rol->permisos->contains('idPermiso', $permiso->idPermiso); @endphp
                        <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-all
                                      hover:border-gray-300 hover:bg-gray-50
                                      {{ $asignado ? 'border-gray-200 bg-gray-50' : 'border-gray-100 bg-white' }}"
                               id="label-{{ $rol->idRol }}-{{ $permiso->idPermiso }}">
                            <input
                                type="checkbox"
                                name="permisos[]"
                                value="{{ $permiso->idPermiso }}"
                                data-rol="{{ $rol->idRol }}"
                                {{ $asignado ? 'checked' : '' }}
                                onchange="actualizarLabel(this)"
                                class="mt-0.5 w-4 h-4 rounded border-gray-300 transition"
                                style="accent-color: {{ $color['bg'] }};"
                            >
                            <span class="text-sm text-gray-700 leading-snug select-none">
                                {{ $permiso->nombrePermiso }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    @endforeach
                </div>

                {{-- Footer con botón guardar --}}
                <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        <span id="count-{{ $rol->idRol }}">{{ $rol->permisos->count() }}</span>
                        de {{ $totalPermisos }} permisos seleccionados
                    </p>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90 shadow-sm"
                            style="background-color: {{ $color['bg'] }};">
                        Actualizar permisos
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endforeach

</div>
@endsection

@push('scripts')
<script>
// ── Activar tab ───────────────────────────────────────────────────────────────
function activarTab(rolId) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.getElementById('tab-' + rolId).classList.remove('hidden');

    // Actualizar estilos de botones de tab
    document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
        btn.classList.remove('shadow-lg', 'scale-[1.02]');
        btn.style.backgroundColor = '';
        btn.style.borderColor = '';
    });
}

// ── Seleccionar / Deseleccionar todos los permisos de un rol ──────────────────
function toggleTodos(rolId, estado) {
    document.querySelectorAll(`input[data-rol="${rolId}"]`).forEach(cb => {
        cb.checked = estado;
        actualizarLabel(cb);
    });
    actualizarContador(rolId);
}

// ── Resaltar label según estado del checkbox ──────────────────────────────────
function actualizarLabel(cb) {
    const rolId     = cb.dataset.rol;
    const permisoId = cb.value;
    const label     = document.getElementById(`label-${rolId}-${permisoId}`);
    if (! label) return;

    if (cb.checked) {
        label.classList.remove('border-gray-100', 'bg-white');
        label.classList.add('border-gray-200', 'bg-gray-50');
    } else {
        label.classList.remove('border-gray-200', 'bg-gray-50');
        label.classList.add('border-gray-100', 'bg-white');
    }
    actualizarContador(rolId);
}

// ── Actualizar contador de permisos seleccionados ────────────────────────────
function actualizarContador(rolId) {
    const total   = document.querySelectorAll(`input[data-rol="${rolId}"]`).length;
    const marcados = document.querySelectorAll(`input[data-rol="${rolId}"]:checked`).length;
    const span    = document.getElementById(`count-${rolId}`);
    if (span) span.textContent = marcados;
}

// ── Inicializar: abrir el tab activo al cargar ────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const activo = {{ session('rolActivo', $roles->first()?->idRol ?? 0) }};
    if (activo) {
        const panel = document.getElementById('tab-' + activo);
        if (panel) panel.classList.remove('hidden');
    }
});
</script>
@endpush
