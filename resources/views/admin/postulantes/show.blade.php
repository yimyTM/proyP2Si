@extends('layouts.app')

@section('title', $postulante->nombre_completo)
@section('page-title', 'Detalle del Postulante')

@section('content')
<div class="space-y-5 max-w-4xl">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">{{ $postulante->nombre_completo }}</h2>
            <p class="text-sm text-gray-500">CI: {{ $postulante->ci }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.postulantes.edit', $postulante) }}"
               class="px-4 py-2 rounded-lg text-white text-sm font-medium transition hover:opacity-90"
               style="background-color: #283342;">
                Editar
            </a>
            <a href="{{ route('admin.estudiantes') }}"
               class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                Volver al listado
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Información personal</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Teléfono</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->nroTelefono ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Sexo</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->sexo === 'M' ? 'Masculino' : ($postulante->sexo === 'F' ? 'Femenino' : '—') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Estado</dt>
                <dd class="font-medium text-gray-800">{{ ucfirst($postulante->estado) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Fecha de nacimiento</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Ciudad</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->ciudad ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Colegio de procedencia</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->colegio_procedencia ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Dirección</dt>
                <dd class="font-medium text-gray-800">{{ $postulante->direccion ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Cuenta de usuario</dt>
                <dd class="font-medium text-gray-800">
                    @if($postulante->usuario)
                        <span class="text-green-700">Activa</span> — {{ $postulante->usuario->correo }}
                    @else
                        <span class="text-gray-500">Sin cuenta (agregue un correo para crearla)</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-3">Pagos</h3>
            @if($postulante->pagos->isEmpty())
                <p class="text-sm text-gray-400">Sin pagos registrados.</p>
            @else
                <ul class="space-y-2 text-sm">
                    @foreach($postulante->pagos as $pago)
                    <li class="flex justify-between">
                        <span>{{ $pago->fecha?->format('d/m/Y') ?? '—' }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $pago->estado === 'aprobado' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ ucfirst($pago->estado) }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-3">Expediente</h3>
            @if($postulante->requisitos->isEmpty())
                <p class="text-sm text-gray-400">Sin documentos entregados.</p>
            @else
                <ul class="space-y-2 text-sm">
                    @foreach($postulante->requisitos as $req)
                    <li class="flex justify-between items-center">
                        <span>{{ $req->requisito?->nombre ?? 'Documento' }}</span>
                        @if($req->validado)
                            <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Validado</span>
                        @elseif($req->entregado)
                            <span class="text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">En revisión</span>
                        @else
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Pendiente</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    @if($postulante->inscripciones->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Carreras inscritas</h3>
        </div>
        <div class="px-6 py-4">
            @foreach($postulante->inscripciones as $inscripcion)
                <div class="mb-3 last:mb-0">
                    @forelse($inscripcion->carrerasInscritas->sortBy('prioridad') as $ci)
                        <span class="inline-block text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full mr-1 mb-1">
                            Op.{{ $ci->prioridad }}: {{ $ci->carrera?->nombre ?? '—' }}
                        </span>
                    @empty
                        <span class="text-sm text-gray-400">Sin carreras en esta inscripción</span>
                    @endforelse
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Calificaciones por gestión ─────────────────────────────────────────── --}}
    @foreach($postulante->inscripciones as $inscripcion)
    @if($inscripcion->notas->isNotEmpty())
    @php
        $examenes = $inscripcion->notas
            ->pluck('examMateria.examen')
            ->unique('idExamen')
            ->sortBy('nroParcial')
            ->values();

        $materias = $inscripcion->notas
            ->groupBy(fn($n) => $n->examMateria->idMateria)
            ->map(function ($notas) {
                $first = $notas->first();
                return (object)[
                    'nombre'    => $first->examMateria->materia->nombMateria,
                    'parciales' => $notas->keyBy(fn($n) => $n->examMateria->examen->nroParcial),
                ];
            })
            ->sortBy('nombre')
            ->values();

        $promedio    = $inscripcion->promedio;
        $resultado   = $inscripcion->resultado;
        $estadoAdm   = $inscripcion->estado_admision;

        $resColors = match($resultado) {
            'Aprobado'  => ['bg-emerald-100', 'text-emerald-700'],
            'Reprobado' => ['bg-red-100',     'text-red-700'],
            default     => ['bg-gray-100',    'text-gray-500'],
        };
        $admColors = match($estadoAdm) {
            'Admitido'  => ['bg-emerald-100', 'text-emerald-700'],
            'Reubicado' => ['bg-blue-100',    'text-blue-700'],
            'Reprobado' => ['bg-red-100',     'text-red-700'],
            default     => ['bg-gray-100',    'text-gray-400'],
        };
    @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b flex flex-wrap items-center justify-between gap-3">
            <h3 class="font-semibold text-gray-800">
                Calificaciones
                <span class="text-xs font-normal text-gray-400 ml-1">
                    (Inscripción #{{ $inscripcion->idInscripcion }})
                </span>
            </h3>
            <div class="flex flex-wrap items-center gap-2">
                @if($promedio !== null)
                <span class="text-sm font-bold {{ (float)$promedio >= 60 ? 'text-emerald-700' : 'text-red-600' }}">
                    Promedio: {{ number_format($promedio, 2) }}
                </span>
                @endif
                @if($resultado)
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $resColors[0] }} {{ $resColors[1] }}">
                    {{ $resultado }}
                </span>
                @endif
                @if($estadoAdm)
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $admColors[0] }} {{ $admColors[1] }}">
                    {{ $estadoAdm }}
                </span>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold">Materia</th>
                        @foreach($examenes as $examen)
                        <th class="px-4 py-3 text-center font-semibold whitespace-nowrap">
                            {{ $examen->descripcion }}
                            @if($examen->ponderacion)
                            <span class="block font-normal normal-case text-gray-400">({{ $examen->ponderacion }}%)</span>
                            @endif
                        </th>
                        @endforeach
                        <th class="px-5 py-3 text-center font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($materias as $materia)
                    @php $total = $materia->parciales->sum('calificacion'); @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $materia->nombre }}</td>
                        @foreach($examenes as $examen)
                        @php $nota = $materia->parciales->get($examen->nroParcial); @endphp
                        <td class="px-4 py-3 text-center">
                            @if($nota)
                            @php
                                $max = $nota->examMateria->puntaje ?? 100;
                                $pct = $max > 0 ? ($nota->calificacion / $max) * 100 : 0;
                            @endphp
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $pct >= 60 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                                {{ number_format($nota->calificacion, 1) }}
                                <span class="font-normal text-gray-400">/ {{ $max }}</span>
                            </span>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="px-5 py-3 text-center font-bold text-gray-700">
                            {{ $total > 0 ? number_format($total, 1) : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($inscripcion->carreraAsignada)
        <div class="px-6 py-3 bg-emerald-50 border-t border-emerald-100 flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-emerald-700">
                Carrera asignada: <strong>{{ $inscripcion->carreraAsignada->nombre }}</strong>
                @if($inscripcion->carreraAsignada->modalidad)
                — {{ $inscripcion->carreraAsignada->modalidad->nombModalidad }}
                @endif
            </span>
        </div>
        @endif
    </div>
    @endif
    @endforeach

    <form method="POST" action="{{ route('admin.postulantes.destroy', $postulante) }}"
          onsubmit="return confirm('¿Eliminar permanentemente a {{ $postulante->nombre_completo }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-red-600 hover:underline">
            Eliminar postulante
        </button>
    </form>

</div>
@endsection
